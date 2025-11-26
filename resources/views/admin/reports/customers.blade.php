@extends('admin.layout')

@section('title', 'Report Customers')
@section('subtitle', 'Ringkasan lead & status pipeline')

@section('menu')
  @include('admin.partials.menu', ['active' => 'reports-customers'])
@endsection

@section('css')
<style>
  .stat-card{
    background:#ffffff;
    border-radius:16px;
    border:1px solid #e5e7eb;
    padding:12px 14px;
  }
  .stat-label{
    font-size:12px;
    color:#6b7280;
    text-transform:uppercase;
    letter-spacing:.06em;
  }
  .stat-value{
    font-size:20px;
    font-weight:700;
  }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="mb-0">Report Customers</h5>
    <div style="font-size:13px; color:#6b7280;">
      Gambaran distribusi lead per stage dan status pipeline.
    </div>
  </div>

  <form method="GET" class="d-flex align-items-center gap-2">
    @if(auth()->user()?->global_role === 'super_admin')
      <select name="company_id" class="form-select form-select-sm">
        @foreach($companies as $company)
          <option value="{{ $company->id }}" @selected($companyId == $company->id)>
            {{ $company->name }}
          </option>
        @endforeach
      </select>
    @endif

    <label class="form-label mb-0 small">Periode (hari)</label>
    <input type="number" name="days" class="form-control form-control-sm" style="width:80px;"
           value="{{ $days }}" min="1" max="365">

    <button class="btn btn-sm btn-outline-primary">
      <i class="fa-solid fa-rotate-right me-1"></i> Terapkan
    </button>
  </form>
</div>


  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="stat-card">
        <div class="stat-label">Total Customer</div>
        <div class="stat-value">{{ $totalCustomers }}</div>
        <div class="small text-muted mt-1">Semua status</div>
      </div>
    </div>
    <div class="col-md-3">
      @php
        $open = $byType['open'] ?? 0;
      @endphp
      <div class="stat-card">
        <div class="stat-label">Open</div>
        <div class="stat-value">{{ $open }}</div>
        <div class="small text-muted mt-1">Masih dalam proses</div>
      </div>
    </div>
    <div class="col-md-3">
      @php
        $won = $byType['won'] ?? 0;
      @endphp
      <div class="stat-card">
        <div class="stat-label">Closed Won</div>
        <div class="stat-value">{{ $won }}</div>
        <div class="small text-muted mt-1">Status deal / closing</div>
      </div>
    </div>
    <div class="col-md-3">
      @php
        $lost = $byType['lost'] ?? 0;
      @endphp
      <div class="stat-card">
        <div class="stat-label">Closed Lost</div>
        <div class="stat-value">{{ $lost }}</div>
        <div class="small text-muted mt-1">Gagal / batal</div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <div class="stat-card">
        <div class="stat-label">Lead Baru</div>
        <div class="stat-value">{{ $newCustomersInPeriod }}</div>
        <div class="small text-muted mt-1">
          Dibuat sejak {{ $fromDate->timezone('Asia/Jakarta')->format('d M Y') }} ({{ $days }} hari terakhir)
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stat-card">
        <div class="stat-label">Closed Won (Periode)</div>
        <div class="stat-value">{{ $closedWonInPeriod }}</div>
        <div class="small text-muted mt-1">
          Berada di stage tipe <code>won</code> dan di-update {{ $days }} hari terakhir.
        </div>
      </div>
    </div>
  </div>

  <a href="{{ route('reports.customers.export.csv', request()->query()) }}"
    class="btn btn-sm btn-outline-secondary me-2">
   <i class="fa-solid fa-file-export me-1"></i> Export CSV
  </a>
 
  <div class="card-soft">
    <h6 class="mb-2">Distribusi Customer per Stage</h6>
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead>
        <tr>
          <th>Stage</th>
          <th>Tipe</th>
          <th class="text-end">Jumlah Customer</th>
        </tr>
        </thead>
        <tbody>
        @forelse($byStage as $row)
          @php
            $type = $row->type;
            $badgeClass = match($type){
              'won'  => 'bg-success-subtle text-success border-success-subtle',
              'lost' => 'bg-danger-subtle text-danger border-danger-subtle',
              'hold' => 'bg-warning-subtle text-warning border-warning-subtle',
              default => 'bg-primary-subtle text-primary border-primary-subtle'
            };
          @endphp
          <tr>
            <td>{{ $row->name }}</td>
            <td>
              <span class="badge {{ $badgeClass }} border rounded-pill" style="font-size:11px;">
                {{ ucfirst($row->type) }}
              </span>
            </td>
            <td class="text-end">{{ $row->total }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center text-muted py-3">
              Belum ada data customer yang terhubung ke stage.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
