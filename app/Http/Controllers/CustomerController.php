<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\PipelineStage;
use App\Models\PipelineStageHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\Company;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->global_role === 'super_admin') {
            $companyId = $request->query('company_id');
    
            // kalau belum dipilih, bisa ambil company pertama atau null
            if (!$companyId) {
                $companyId = Company::where('status', 'active')->orderBy('name')->value('id');
            }
        } else {
            $companyId = $user->company_id;
        }

        $query = Customer::with(['currentStage', 'marketing', 'customerService'])
            ->where('company_id', $companyId);

        if ($request->filled('stage_id')) {
            $query->where('current_stage_id', $request->stage_id);
        }

        if ($request->filled('marketing_id')) {
            $query->where('assigned_marketing_id', $request->marketing_id);
        }

        if ($request->filled('cs_id')) {
            $query->where('assigned_cs_id', $request->cs_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        [$query, $companyId] = $this->buildCustomerQueryForCurrentUser($request);

        $customers = $query->orderByDesc('id')->paginate(15);

        $pipelineStages = PipelineStage::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->get();

        $marketingUsers = User::where('company_id', $companyId)
            ->where('company_role', 'marketing')
            ->orderBy('name')
            ->get();

        $csUsers = User::where('company_id', $companyId)
            ->where('company_role', 'customer_service')
            ->orderBy('name')
            ->get();

        $companies = [];
        if ($user->global_role === 'super_admin') {
            $companies = Company::orderBy('name')->get();
        }

        return view('admin.customers.index', compact(
            'customers',
            'pipelineStages',
            'companyId',
            'marketingUsers',
            'csUsers',
            'companies'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $companyId = $user->global_role === 'super_admin' ? $user->company_id : $user->company_id;

        $pipelineStages = PipelineStage::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->get();

        $marketingUsers = User::where('company_id', $companyId)
            ->where('company_role', 'marketing')
            ->orderBy('name')
            ->get();

        $csUsers = User::where('company_id', $companyId)
            ->where('company_role', 'customer_service')
            ->orderBy('name')
            ->get();

        return view('admin.customers.create', compact('pipelineStages', 'marketingUsers', 'csUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->global_role === 'super_admin' ? $user->company_id : $user->company_id;

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
            'source'        => ['nullable', 'string', 'max:100'],
            'tag'           => ['nullable', 'string', 'max:100'],
            'assigned_marketing_id' => ['nullable', 'exists:users,id'],
            'assigned_cs_id'        => ['nullable', 'exists:users,id'],
            'current_stage_id'      => ['required', 'exists:pipeline_stages,id'],
            'notes'         => ['nullable', 'string'],
            'estimated_value' => ['nullable', 'numeric'],
        ]);

        $data['company_id'] = $companyId;
        $data['created_by'] = $user->id;

        $customer = Customer::create($data);

        // history & activity log untuk stage awal
        PipelineStageHistory::create([
            'customer_id'   => $customer->id,
            'company_id'    => $companyId,
            'from_stage_id' => null,
            'to_stage_id'   => $customer->current_stage_id,
            'changed_by'    => $user->id,
            'note'          => 'Customer created',
        ]);

        ActivityLog::create([
            'company_id'   => $companyId,
            'user_id'      => $user->id,
            'action'       => 'customer_created',
            'loggable_type'=> Customer::class,
            'loggable_id'  => $customer->id,
            'data'         => ['customer' => $customer->only(['id', 'name', 'phone', 'email'])],
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $this->authorizeCompany($customer);

        $customer->load(['company', 'currentStage', 'marketing', 'customerService']);

        $stageHistories = $customer->stageHistories()
            ->with(['fromStage', 'toStage', 'changer'])
            ->orderByDesc('created_at')
            ->get();

        $interactions = $customer->interactions()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.customers.show', compact('customer', 'stageHistories', 'interactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $this->authorizeCompany($customer);

        $user = Auth::user();
        $companyId = $customer->company_id;

        $pipelineStages = PipelineStage::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->get();

        $marketingUsers = User::where('company_id', $companyId)
            ->where('company_role', 'marketing')
            ->orderBy('name')
            ->get();

        $csUsers = User::where('company_id', $companyId)
            ->where('company_role', 'customer_service')
            ->orderBy('name')
            ->get();

        return view('admin.customers.edit', compact('customer', 'pipelineStages', 'marketingUsers', 'csUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorizeCompany($customer);

        $user = Auth::user();
        $oldStageId = $customer->current_stage_id;

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
            'source'        => ['nullable', 'string', 'max:100'],
            'tag'           => ['nullable', 'string', 'max:100'],
            'assigned_marketing_id' => ['nullable', 'exists:users,id'],
            'assigned_cs_id'        => ['nullable', 'exists:users,id'],
            'current_stage_id'      => ['required', 'exists:pipeline_stages,id'],
            'notes'         => ['nullable', 'string'],
            'estimated_value' => ['nullable', 'numeric'],
        ]);

        $customer->update($data);

        // Jika stage berubah → simpan history + log
        if ((int)$oldStageId !== (int)$customer->current_stage_id) {
            PipelineStageHistory::create([
                'customer_id'   => $customer->id,
                'company_id'    => $customer->company_id,
                'from_stage_id' => $oldStageId,
                'to_stage_id'   => $customer->current_stage_id,
                'changed_by'    => $user->id,
                'note'          => 'Stage updated from form',
            ]);

            ActivityLog::create([
                'company_id'   => $customer->company_id,
                'user_id'      => $user->id,
                'action'       => 'customer_stage_changed',
                'loggable_type'=> Customer::class,
                'loggable_id'  => $customer->id,
                'data'         => [
                    'from_stage_id' => $oldStageId,
                    'to_stage_id'   => $customer->current_stage_id,
                ],
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $this->authorizeCompany($customer);

        $user = Auth::user();

        $customer->delete(); // soft delete

        ActivityLog::create([
            'company_id'   => $customer->company_id,
            'user_id'      => $user->id,
            'action'       => 'customer_deleted',
            'loggable_type'=> Customer::class,
            'loggable_id'  => $customer->id,
            'data'         => [
                'id'   => $customer->id,
                'name' => $customer->name,
            ],
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }

    /**
     * Pastikan user hanya bisa akses customer di company-nya.
     */
    protected function authorizeCompany(Customer $customer): void
    {
        $user = Auth::user();

        if ($user->global_role === 'super_admin') {
            return;
        }

        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Anda tidak berhak mengakses customer ini.');
        }
    }

    public function updateStage(Request $request, Customer $customer): JsonResponse
    {
        $this->authorizeCompany($customer);

        $user = Auth::user();

        // Batasin siapa saja yang boleh ganti stage:
        // admin perusahaan, marketing, dan CS
        if (
            $user->global_role !== 'super_admin' &&
            !in_array($user->company_role, ['admin', 'marketing', 'customer_service'])
        ) {
            return response()->json(['message' => 'Tidak berhak mengubah stage.'], 403);
        }

        $data = $request->validate([
            'stage_id' => ['required', 'exists:pipeline_stages,id'],
        ]);

        $newStageId = (int) $data['stage_id'];

        // pastikan stage milik company yang sama
        $stage = PipelineStage::where('company_id', $customer->company_id)
            ->where('id', $newStageId)
            ->firstOrFail();

        $oldStageId = $customer->current_stage_id;

        if ($oldStageId === $newStageId) {
            return response()->json([
                'message' => 'Stage tidak berubah.',
            ]);
        }

        $customer->current_stage_id = $newStageId;
        $customer->save();

        PipelineStageHistory::create([
            'customer_id'   => $customer->id,
            'company_id'    => $customer->company_id,
            'from_stage_id' => $oldStageId,
            'to_stage_id'   => $newStageId,
            'changed_by'    => $user->id,
            'note'          => 'Stage updated from list',
        ]);

        ActivityLog::create([
            'company_id'   => $customer->company_id,
            'user_id'      => $user->id,
            'action'       => 'customer_stage_changed_quick',
            'loggable_type'=> Customer::class,
            'loggable_id'  => $customer->id,
            'data'         => [
                'from_stage_id' => $oldStageId,
                'to_stage_id'   => $newStageId,
            ],
        ]);

        return response()->json([
            'message' => 'Stage berhasil diperbarui.',
            'stage'   => [
                'id'   => $stage->id,
                'name' => $stage->name,
                'type' => $stage->type,
            ],
        ]);
    }

    private function buildCustomerQueryForCurrentUser(Request $request)
    {
        $user = Auth::user();

        // Tentukan company_id
        if ($user->global_role === 'super_admin') {
            $companyId = $request->query('company_id');

            if (!$companyId) {
                $companyId = Company::where('status', 'active')->orderBy('name')->value('id');
            }
        } else {
            $companyId = $user->company_id;
        }

        $query = Customer::with(['currentStage', 'marketing', 'customerService', 'company'])
            ->where('company_id', $companyId);

        if ($request->filled('stage_id')) {
            $query->where('current_stage_id', $request->stage_id);
        }

        if ($request->filled('marketing_id')) {
            $query->where('assigned_marketing_id', $request->marketing_id);
        }

        if ($request->filled('cs_id')) {
            $query->where('assigned_cs_id', $request->cs_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        return [$query, $companyId];
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$query, $companyId] = $this->buildCustomerQueryForCurrentUser($request);

        $fileName = 'customers_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // header CSV
            fputcsv($handle, [
                'Nama',
                'Phone',
                'Email',
                'Stage',
                'Marketing',
                'Customer Service',
                'Source',
                'Tag',
                'Estimated Value',
                'Last Contact At',
                'Created At',
            ]);

            $query->orderBy('id')->chunk(500, function ($chunk) use ($handle) {
                foreach ($chunk as $customer) {
                    fputcsv($handle, [
                        $customer->name,
                        $customer->phone,
                        $customer->email,
                        $customer->currentStage?->name,
                        $customer->marketing?->name,
                        $customer->customerService?->name,
                        $customer->source,
                        $customer->tag,
                        $customer->estimated_value,
                        optional($customer->last_contact_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        optional($customer->created_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

}
