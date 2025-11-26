@php($a = $active ?? '')

<a class="nav-link-custom {{ $a==='dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
  <i class="fa-solid fa-gauge"></i> <span>Dashboard</span>
</a>

<small>Data Utama</small>
@if(auth()->user()?->global_role === 'super_admin')
  <a class="nav-link-custom {{ $a==='companies' ? 'active' : '' }}" href="{{ route('companies.index') }}">
    <i class="fa-solid fa-building"></i> <span>Perusahaan</span>
  </a>
@endif

<a class="nav-link-custom {{ $a==='customers' ? 'active' : '' }}" href="{{ route('customers.index') }}">
  <i class="fa-solid fa-user-group"></i> <span>Customers</span>
</a>

<a class="nav-link-custom {{ $a==='pipeline' ? 'active' : '' }}" href="{{ route('pipeline.index') }}">
  <i class="fa-solid fa-route"></i> <span>Pipeline / Roadmap</span>
</a>

<small>Tim</small>
<a class="nav-link-custom {{ $a==='team' ? 'active' : '' }}" href="{{ route('team.index') }}">
  <i class="fa-solid fa-users-gear"></i> <span>Tim & Role</span>
</a>

<small>Report</small>
<a class="nav-link-custom {{ $a==='reports-customers' ? 'active' : '' }}" href="{{ route('reports.customers') }}">
  <i class="fa-solid fa-chart-line"></i> <span>Report Customers</span>
</a>
<a class="nav-link-custom {{ $a==='reports-employees' ? 'active' : '' }}" href="{{ route('reports.employees') }}">
  <i class="fa-solid fa-user-tie"></i> <span>Report Karyawan</span>
</a>

<small>Seting</small>
<a class="nav-link-custom {{ $a==='setting' ? 'active' : '' }}" href="{{ route('setting.menu') }}">
  <i class="fa-solid fa-cog"></i> <span>Menu Management</span>
</a>
