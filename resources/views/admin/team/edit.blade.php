@extends('admin.layout')

@section('title', 'Edit Anggota Tim')
@section('subtitle', 'Perbarui data akun & status')

@section('menu')
  @include('admin.partials.menu', ['active' => 'team'])
@endsection

@section('content')
  <div class="card-soft">
    <div class="d-flex justify-content-between mb-3">
      <h5 class="mb-0">Edit Anggota Tim</h5>
      <a href="{{ route('team.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left-long me-1"></i> Kembali
      </a>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger py-2 px-3 small">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('team.update', $user) }}">
      @csrf
      @method('PUT')

      <div class="row g-3">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Nama <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required
                   value="{{ old('name', $user->name) }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required
                   value="{{ old('email', $user->email) }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Password (opsional)</label>
            <input type="password" name="password" class="form-control">
            <div class="form-text">Isi hanya jika ingin mengganti password.</div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Role di Perusahaan <span class="text-danger">*</span></label>
            <select name="company_role" class="form-select" required>
              <option value="admin" @selected(old('company_role', $user->company_role) === 'admin')>Admin</option>
              <option value="marketing" @selected(old('company_role', $user->company_role) === 'marketing')>Marketing</option>
              <option value="customer_service" @selected(old('company_role', $user->company_role) === 'customer_service')>Customer Service</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Status Akun</label>
            <select name="is_active" class="form-select">
              <option value="1" @selected(old('is_active', $user->is_active) == true)>Aktif</option>
              <option value="0" @selected(old('is_active', $user->is_active) == false)>Nonaktif</option>
            </select>
          </div>
        </div>
      </div>

      <div class="mt-2 d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
@endsection
