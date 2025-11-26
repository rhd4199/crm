@extends('admin.layout')

@section('title', 'Report Karyawan')
@section('subtitle', 'Performa admin, marketing, dan customer service')

@section('menu')
  @include('admin.partials.menu', ['active' => 'reports-employees'])
@endsection

@section('css')
<style>
  .role-badge{
    font-size:11px;
  }
</style>
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="mb-0">Report Karyawan</h5>
      <div style="font-size:13px; color:#6b7280;">
        Aktivitas admin, marketing, dan customer service dalam periode tertentu.
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

  <a href="{{ route('reports.employees.export.csv', request()->query()) }}"
      class="btn btn-sm btn-outline-secondary me-2">
    <i class="fa-solid fa-file-export me-1"></i> Export CSV
  </a>
 
  <div class="card-soft">
    <div class="mb-2 small text-muted">
      Data sejak {{ $fromDate->timezone('Asia/Jakarta')->format('d M Y') }} ({{ $days }} hari terakhir).
    </div>

    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Role</th>
          <th class="text-center">Customer (Marketing)</th>
          <th class="text-center">Customer (CS)</th>
          <th class="text-center">Interaksi (periode)</th>
          <th class="text-center">Closed Won (periode)</th>
        </tr>
        </thead>
        <tbody>
        @forelse($stats as $i => $row)
          @php($emp = $row['employee'])
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $emp->name }}</td>
            <td>
              @if($emp->global_role === 'super_admin')
                <span class="badge bg-dark role-badge">Super Admin</span>
              @else
                @if($emp->company_role === 'admin')
                  <span class="badge bg-primary role-badge">Admin</span>
                @elseif($emp->company_role === 'marketing')
                  <span class="badge bg-info text-dark role-badge">Marketing</span>
                @elseif($emp->company_role === 'customer_service')
                  <span class="badge bg-success role-badge">Customer Service</span>
                @else
                  <span class="badge bg-secondary role-badge">User</span>
                @endif
              @endif
            </td>
            <td class="text-center">{{ $row['handled_marketing'] }}</td>
            <td class="text-center">{{ $row['handled_cs'] }}</td>
            <td class="text-center">{{ $row['interactions'] }}</td>
            <td class="text-center">{{ $row['closed_won'] }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-3">
              Belum ada data karyawan untuk ditampilkan.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
