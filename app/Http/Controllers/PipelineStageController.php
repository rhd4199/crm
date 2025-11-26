<?php

namespace App\Http\Controllers;

use App\Models\PipelineStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PipelineStageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->global_role === 'super_admin' && !$user->company_id) {
            // Super admin tanpa company: sementara redirect ke dashboard atau buat pilihan company
            return redirect()->route('dashboard')->with('info', 'Pilih perusahaan dulu untuk mengelola pipeline.');
        }

        $companyId = $user->company_id;

        $stages = PipelineStage::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->get();

        return view('admin.pipeline.index', compact('stages'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $this->authorizeRole($user); // hanya admin perusahaan yang boleh

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:open,hold,won,lost'],
        ]);

        $maxSort = PipelineStage::where('company_id', $companyId)->max('sort_order') ?? 0;

        PipelineStage::create([
            'company_id' => $companyId,
            'name'       => $data['name'],
            'type'       => $data['type'],
            'sort_order' => $maxSort + 1,
        ]);

        return back()->with('success', 'Stage berhasil ditambahkan.');
    }

    public function update(Request $request, PipelineStage $pipelineStage)
    {
        $user = Auth::user();
        $this->authorizeRole($user);

        if ($pipelineStage->company_id !== $user->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'type'       => ['required', 'in:open,hold,won,lost'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $pipelineStage->update($data);

        return back()->with('success', 'Stage berhasil diperbarui.');
    }

    public function destroy(PipelineStage $pipelineStage)
    {
        $user = Auth::user();
        $this->authorizeRole($user);

        if ($pipelineStage->company_id !== $user->company_id) {
            abort(403);
        }

        $pipelineStage->delete();

        return back()->with('success', 'Stage berhasil dihapus.');
    }

    protected function authorizeRole($user): void
    {
        if ($user->global_role === 'super_admin') {
            return;
        }

        if ($user->company_role !== 'admin') {
            abort(403, 'Hanya admin perusahaan yang boleh mengelola pipeline.');
        }
    }
}
