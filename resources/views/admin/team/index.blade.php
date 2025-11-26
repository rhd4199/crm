@extends('admin.layout')

@section('title', 'Tim & Role')
@section('subtitle', 'Kelola akun admin, marketing, dan customer service')

@section('menu')
  @include('admin.partials.menu', ['active' => 'team'])
@endsection

@section('content')
  @if(session('success'))
    <div class="alert alert-success py-2 px-3 small">
      {{ session('success') }}
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="mb-0">Anggota Tim</h5>
      <div style="font-size:13px; color:#6b7280;">
        Akun yang bisa mengakses CRM di perusahaan ini.
      </div>
    </div>
    <a href="{{ route('team.create') }}" class="btn btn-sm btn-primary">
      <i class="fa-solid fa-user-plus me-1"></i> Tambah Anggota
    </a>
  </div>

  <div class="card-soft">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($team as $i => $member)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $member->name }}</td>
            <td>{{ $member->email }}</td>
            <td style="text-transform:capitalize;">
              @if($member->global_role === 'super_admin')
                <span class="badge bg-dark">Super Admin</span>
              @else
                @if($member->company_role === 'admin')
                  <span class="badge bg-primary">Admin</span>
                @elseif($member->company_role === 'marketing')
                  <span class="badge bg-info text-dark">Marketing</span>
                @elseif($member->company_role === 'customer_service')
                  <span class="badge bg-success">Customer Service</span>
                @else
                  <span class="badge bg-secondary">User</span>
                @endif
              @endif
            </td>
            <td>
              @if($member->is_active)
                <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Nonaktif</span>
              @endif
            </td>
            <td class="text-end">
              @if($member->global_role !== 'super_admin')
                <a href="{{ route('team.edit', $member) }}" class="btn btn-xs btn-outline-primary btn-sm">
                  <i class="fa-regular fa-pen-to-square"></i>
                </a>
                @if($member->is_active)
                  <form action="{{ route('team.destroy', $member) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Nonaktifkan akun ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">
                      <i class="fa-solid fa-user-slash"></i>
                    </button>
                  </form>
                @endif
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-3">
              Belum ada anggota tim selain Anda.
              <a href="{{ route('team.create') }}">Tambah satu sekarang.</a>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
