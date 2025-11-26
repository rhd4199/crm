@extends('admin.layout')

@section('title', 'Perusahaan')
@section('subtitle', 'Kelola perusahaan yang menggunakan CRM')

@section('menu')
  @include('admin.partials.menu', ['active' => 'companies'])
@endsection

@section('content')
  @if(session('success'))
    <div class="alert alert-success py-2 px-3 small">
      {{ session('success') }}
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="mb-0">Perusahaan</h5>
      <div style="font-size:13px; color:#6b7280;">
        Daftar semua perusahaan yang terdaftar di sistem.
      </div>
    </div>
    <a href="{{ route('companies.create') }}" class="btn btn-sm btn-primary">
      <i class="fa-solid fa-building-circle-plus me-1"></i> Tambah Perusahaan
    </a>
  </div>

  <div class="card-soft">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead>
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Kode</th>
              <th>Status</th>
              <th>Dibuat</th>
              <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($companies as $i => $company)
            <tr>
              <td>{{ $companies->firstItem() + $i }}</td>
              <td>{{ $company->name }}</td>
              <td>
                @if($company->code)
                  <code>{{ $company->code }}</code>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if($company->status === 'active')
                  <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                @else
                  <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Nonaktif</span>
                @endif
              </td>
              <td style="font-size:12px;">
                {{ $company->created_at?->timezone('Asia/Jakarta')->format('d M Y') }}
              </td>
              <td class="text-end">
                <a href="{{ route('companies.edit', $company) }}" class="btn btn-xs btn-outline-primary btn-sm">
                  <i class="fa-regular fa-pen-to-square"></i>
                </a>
                @if($company->status === 'active')
                  <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Nonaktifkan perusahaan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">
                      <i class="fa-solid fa-ban"></i>
                    </button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-3">
              Belum ada perusahaan terdaftar.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-2">
      {{ $companies->links() }}
    </div>
  </div>
@endsection
