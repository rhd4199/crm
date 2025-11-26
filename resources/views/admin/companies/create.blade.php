@extends('admin.layout')

@section('title', 'Tambah Perusahaan')
@section('subtitle', 'Registrasi perusahaan baru ke sistem')

@section('menu')
  @include('admin.partials.menu', ['active' => 'companies'])
@endsection

@section('content')
  <div class="card-soft">
    <div class="d-flex justify-content-between mb-3">
      <h5 class="mb-0">Tambah Perusahaan</h5>
      <a href="{{ route('companies.index') }}" class="btn btn-sm btn-outline-secondary">
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

    <form method="POST" action="{{ route('companies.store') }}">
      @csrf

      <div class="row g-3">
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Kode (opsional)</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}"
                   placeholder="Misal: DEPATI, TUGU, dst">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active" @selected(old('status', 'active') === 'active')>Aktif</option>
              <option value="inactive" @selected(old('status') === 'inactive')>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
          </div>
        </div>
      </div>
      

      <div class="mt-2 d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk me-1"></i> Simpan
        </button>
      </div>
    </form>
  </div>
@endsection
