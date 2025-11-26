@extends('admin.layout')

@section('title', 'Pipeline / Roadmap')
@section('subtitle', 'Atur tahapan progres customer per perusahaan')

@section('menu')
  @include('admin.partials.menu', ['active' => 'pipeline'])
@endsection

@section('content')
  @if(session('success'))
    <div class="alert alert-success py-2 px-3 small">
      {{ session('success') }}
    </div>
  @endif

  <div class="row g-3">
    <!-- Form tambah stage -->
    <div class="col-md-4">
      <div class="card-soft h-100">
        <h6 class="mb-2">Tambah Stage Baru</h6>
        <p class="small text-muted">
          Gunakan ini untuk menambah tahapan baru dalam roadmap, misalnya
          <strong>Follow Up Kedua</strong> atau <strong>Prospek Panas</strong>.
        </p>

        <form method="POST" action="{{ route('pipeline.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Nama Stage <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control form-control-sm"
                   value="{{ old('name') }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Tipe <span class="text-danger">*</span></label>
            <select name="type" class="form-select form-select-sm" required>
              <option value="open" @selected(old('type') === 'open')>Open (proses berjalan)</option>
              <option value="hold" @selected(old('type') === 'hold')>Hold (ditahan sementara)</option>
              <option value="won" @selected(old('type') === 'won')>Won (deal / closing)</option>
              <option value="lost" @selected(old('type') === 'lost')>Lost (gagal / batal)</option>
            </select>
          </div>

          <button type="submit" class="btn btn-sm btn-primary w-100">
            <i class="fa-solid fa-plus me-1"></i> Tambah Stage
          </button>
        </form>
      </div>
    </div>

    <!-- Daftar stage -->
    <div class="col-md-8">
      <div class="card-soft">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="mb-0">Daftar Stage</h6>
          <span class="small text-muted">
            Urutan stage mengikuti <strong>Sort Order</strong>.
          </span>
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

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
            <tr>
              <th style="width:50px;">#</th>
              <th>Nama Stage</th>
              <th style="width:120px;">Tipe</th>
              <th style="width:100px;">Sort</th>
              <th style="width:120px;" class="text-end">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($stages as $i => $stage)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                  <form method="POST" action="{{ route('pipeline.update', $stage) }}" class="d-flex gap-1 align-items-center">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" class="form-control form-control-sm"
                           value="{{ old('name_'.$stage->id, $stage->name) }}">
                </td>
                <td>
                    <select name="type" class="form-select form-select-sm">
                      <option value="open" @selected($stage->type === 'open')>Open</option>
                      <option value="hold" @selected($stage->type === 'hold')>Hold</option>
                      <option value="won" @selected($stage->type === 'won')>Won</option>
                      <option value="lost" @selected($stage->type === 'lost')>Lost</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="sort_order"
                           class="form-control form-control-sm text-center"
                           value="{{ $stage->sort_order }}">
                </td>
                <td class="text-end">
                    <button type="submit" class="btn btn-xs btn-outline-primary btn-sm">
                      <i class="fa-regular fa-floppy-disk"></i>
                    </button>
                  </form>

                  <form method="POST" action="{{ route('pipeline.destroy', $stage) }}" class="d-inline"
                        onsubmit="return confirm('Hapus stage ini? Pastikan tidak dipakai customer aktif.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">
                      <i class="fa-regular fa-trash-can"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-3">
                  Belum ada stage. Tambahkan dari panel sebelah kiri.
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-2 small text-muted">
          <strong>Saran:</strong> urutan umum yang enak:
          <code>Interest Low → Interest Medium → Interest High → Contacted → Hold → Closed Won → Closed Lost</code>.
        </div>
      </div>
    </div>
  </div>
@endsection
