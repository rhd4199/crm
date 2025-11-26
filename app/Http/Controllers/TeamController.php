<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    protected function ensureAdmin()
    {
        $user = Auth::user();

        // Super admin boleh, admin perusahaan juga boleh
        if ($user->global_role === 'super_admin') {
            return;
        }

        if ($user->company_role !== User::COMPANY_ROLE_ADMIN) {
            abort(403, 'Hanya admin perusahaan yang boleh mengelola tim.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();

        $auth = Auth::user();

        // Untuk sekarang: fokus ke company user tersebut
        $companyId = $auth->company_id;

        $team = User::where('company_id', $companyId)
            ->orderByRaw("field(company_role, 'admin','marketing','customer_service') asc")
            ->orderBy('name')
            ->get();

        return view('admin.team.index', compact('team'));
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $auth = Auth::user();
        $companyId = $auth->company_id;

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:6'],
            'company_role' => ['required', 'in:admin,marketing,customer_service'],
        ]);

        User::create([
            'company_id'   => $companyId,
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'global_role'  => User::GLOBAL_ROLE_USER,
            'company_role' => $data['company_role'],
            'is_active'    => true,
        ]);

        return redirect()->route('team.index')->with('success', 'Anggota tim berhasil dibuat.');
    }

    public function edit(User $team)
    {
        $this->ensureAdmin();

        $auth = Auth::user();

        if ($auth->global_role !== 'super_admin' && $team->company_id !== $auth->company_id) {
            abort(403);
        }

        return view('admin.team.edit', ['user' => $team]);
    }

    public function update(Request $request, User $team)
    {
        $this->ensureAdmin();

        $auth = Auth::user();
        if ($auth->global_role !== 'super_admin' && $team->company_id !== $auth->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email,' . $team->id],
            'password'     => ['nullable', 'string', 'min:6'],
            'company_role' => ['required', 'in:admin,marketing,customer_service'],
            'is_active'    => ['required', 'boolean'],
        ]);

        $team->name = $data['name'];
        $team->email = $data['email'];
        $team->company_role = $data['company_role'];
        $team->is_active = $data['is_active'];

        if (!empty($data['password'])) {
            $team->password = Hash::make($data['password']);
        }

        $team->save();

        return redirect()->route('team.index')->with('success', 'Data anggota tim diperbarui.');
    }

    public function destroy(User $team)
    {
        $this->ensureAdmin();

        $auth = Auth::user();
        if ($auth->global_role !== 'super_admin' && $team->company_id !== $auth->company_id) {
            abort(403);
        }

        // Alih-alih hapus, kita nonaktifkan saja
        $team->is_active = false;
        $team->save();

        return redirect()->route('team.index')->with('success', 'Akun dinonaktifkan.');
    }
}
