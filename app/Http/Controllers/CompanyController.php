<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PipelineStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    protected function ensureSuperAdmin(): void
    {
        if (Auth::user()->global_role !== 'super_admin') {
            abort(403, 'Hanya super admin yang boleh mengelola perusahaan.');
        }
    }

    public function index()
    {
        $this->ensureSuperAdmin();

        $companies = Company::orderBy('name')->paginate(20);

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $this->ensureSuperAdmin();

        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $this->ensureSuperAdmin();

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'code'   => ['nullable', 'string', 'max:50', 'unique:companies,code'],
            'status' => ['required', 'in:active,inactive'],
            'address'=> ['nullable', 'string', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:50'],
            'email'  => ['nullable', 'email', 'max:255'],
        ]);

        $company = Company::create([
            'name'    => $data['name'],
            'code'    => $data['code'] ?? null,
            'status'  => $data['status'],
            'address' => $data['address'] ?? null,
            'phone'   => $data['phone'] ?? null,
            'email'   => $data['email'] ?? null,
        ]);

        $this->createDefaultStagesForCompany($company);

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil dibuat.');
    }


    public function edit(Company $company)
    {
        $this->ensureSuperAdmin();

        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $this->ensureSuperAdmin();
    
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'code'   => ['nullable', 'string', 'max:50', 'unique:companies,code,' . $company->id],
            'status' => ['required', 'in:active,inactive'],
            'address'=> ['nullable', 'string', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:50'],
            'email'  => ['nullable', 'email', 'max:255'],
        ]);
    
        $company->update($data);
    
        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }
    

    public function destroy(Company $company)
    {
        $this->ensureSuperAdmin();

        // Untuk sekarang: soft “deaktivasi” via status saja, jangan hard delete
        $company->status = 'inactive';
        $company->save();

        return redirect()->route('companies.index')->with('success', 'Perusahaan dinonaktifkan.');
    }

    /**
     * Buat default pipeline stages ketika perusahaan baru dibuat.
     */
    protected function createDefaultStagesForCompany(Company $company): void
    {
        if (PipelineStage::where('company_id', $company->id)->exists()) {
            return;
        }

        $defaults = [
            ['name' => 'Interest Low',    'type' => 'open'],
            ['name' => 'Interest Medium', 'type' => 'open'],
            ['name' => 'Interest High',   'type' => 'open'],
            ['name' => 'Contacted',       'type' => 'open'],
            ['name' => 'Hold',            'type' => 'hold'],
            ['name' => 'Closed Won',      'type' => 'won'],
            ['name' => 'Closed Lost',     'type' => 'lost'],
        ];

        $sort = 1;
        foreach ($defaults as $row) {
            PipelineStage::create([
                'company_id' => $company->id,
                'name'       => $row['name'],
                'type'       => $row['type'],
                'sort_order' => $sort++,
            ]);
        }
    }
}
