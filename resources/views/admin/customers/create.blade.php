@extends('admin.layout')

@section('title', 'Tambah Customer')
@section('subtitle', 'Input lead/customer baru')

@section('menu')
  @include('admin.partials.menu', ['active' => 'customers'])
@endsection

@section('content')
  <div class="card-soft">
    <div class="d-flex justify-content-between mb-3">
      <h5 class="mb-0">Tambah Customer</h5>
      <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">
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

    <form method="POST" action="{{ route('customers.store') }}">
      @csrf

      <div class="row g-3">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Nama <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
          </div>

          <div class="mb-3">
            <label class="form-label">No. HP / WhatsApp</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
          </div>

          <div class="mb-3">
            <label class="form-label">Source</label>
            <input type="text" name="source" class="form-control" value="{{ old('source') }}" placeholder="Instagram, WA, Referral, dsb">
          </div>

          <div class="mb-3">
            <label class="form-label">Tag</label>
            <input type="text" name="tag" class="form-control" value="{{ old('tag') }}" placeholder="Opsional, misal: batch-jan, mahasiswa, dll">
          </div>
        </div>

        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Stage Awal <span class="text-danger">*</span></label>
            <select name="current_stage_id" class="form-select" required>
              <option value="">Pilih stage...</option>
              @foreach($pipelineStages as $stage)
                <option value="{{ $stage->id }}" @selected(old('current_stage_id') == $stage->id)>
                  {{ $stage->name }}
                </option>
              @endforeach
            </select>
            <div class="form-text">Saran: gunakan Interest Low/Med/High sebagai stage awal.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Marketing Penanggung Jawab</label>
            <select name="assigned_marketing_id" class="form-select">
              <option value="">Tidak ada / default</option>
              @foreach($marketingUsers as $u)
                <option value="{{ $u->id }}" @selected(old('assigned_marketing_id') == $u->id)>
                  {{ $u->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Customer Service</label>
            <select name="assigned_cs_id" class="form-select">
              <option value="">Belum ditentukan</option>
              @foreach($csUsers as $u)
                <option value="{{ $u->id }}" @selected(old('assigned_cs_id') == $u->id)>
                  {{ $u->name }}
                </option>
              @endforeach
            </select>
            <div class="form-text">
              CS bisa dipilih langsung atau nanti di-assign oleh admin perusahaan.
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Perkiraan Nilai Deal (Rp)</label>
            <input type="number" step="0.01" name="estimated_value" class="form-control" value="{{ old('estimated_value') }}">
          </div>
        </div>

        <div class="col-12">
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" rows="3" class="form-control" placeholder="Deskripsikan minat, kebutuhan, atau informasi penting lain...">{{ old('notes') }}</textarea>
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
