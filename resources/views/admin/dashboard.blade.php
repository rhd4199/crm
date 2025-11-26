@extends('admin.layout')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan aktivitas CRM')

{{-- MENU KIRI --}}
@section('menu')
  @include('admin.partials.menu', ['active' => 'dashboard'])
@endsection

@section('css')
<style>
  .clock-card{
    background: linear-gradient(135deg, #2e65b7, #43b581);
    border-radius:16px;
    color:#f9fafb;
    padding:16px 18px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
  }
  .clock-time{
    font-family: 'Courier New', monospace;
    font-size:32px;
    font-weight:700;
    letter-spacing:2px;
  }
  .clock-date{
    font-size:13px;
    opacity:.9;
  }
  .metric-small{
    display:flex;
    flex-direction:column;
    gap:2px;
  }
  .metric-label{
    font-size:12px;
    color:#9ca3af;
    text-transform:uppercase;
    letter-spacing:.08em;
  }
  .metric-value{
    font-size:20px;
    font-weight:700;
  }
</style>
@endsection

@section('content')
  <div class="row g-3 mb-3">
    <div class="col-lg-6">
      <div class="clock-card">
        <div>
          <div style="font-size:13px; opacity:.9;">Selamat datang, {{ $user->name }}</div>
          <div class="clock-time" id="clockTime">--:--:--</div>
          <div class="clock-date" id="clockDate">Memuat tanggal...</div>
        </div>
        <div class="text-end">
          <div class="metric-label">Role</div>
          <div class="metric-value" style="font-size:18px;">
            @if($user->global_role === 'super_admin')
              Super Admin
            @else
              {{ ucfirst(str_replace('_',' ',$user->company_role)) }}
            @endif
          </div>
          @if($user->company)
            <div style="font-size:12px; opacity:.9;">Perusahaan: {{ $user->company->name }}</div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card-soft h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="mb-0">Snapshot Hari Ini</h6>
          <span class="badge bg-success-subtle text-success border border-success-subtle">
            Draft (dummy)
          </span>
        </div>
        <div class="row g-3">
          <div class="col-4">
            <div class="metric-small">
              <div class="metric-label">Lead baru</div>
              <div class="metric-value">0</div>
              <div style="font-size:12px; color:#9ca3af;">hari ini</div>
            </div>
          </div>
          <div class="col-4">
            <div class="metric-small">
              <div class="metric-label">Di-contact</div>
              <div class="metric-value">0</div>
              <div style="font-size:12px; color:#9ca3af;">oleh CS</div>
            </div>
          </div>
          <div class="col-4">
            <div class="metric-small">
              <div class="metric-label">Closed</div>
              <div class="metric-value">0</div>
              <div style="font-size:12px; color:#9ca3af;">deal hari ini</div>
            </div>
          </div>
        </div>
        <div class="mt-3" style="font-size:12px; color:#6b7280;">
          Angka di atas masih dummy. Nanti kita isi dari tabel <code>customers</code>,
          <code>interactions</code>, dan <code>pipeline_stage_histories</code>.
        </div>
      </div>
    </div>
  </div>

  <div class="card-soft">
    <h6 class="mb-2">Langkah Berikutnya</h6>
    <ol class="mb-1" style="font-size:14px;">
      <li>Implement middleware untuk scope <strong>company</strong> per user.</li>
      <li>Buat halaman <strong>daftar customers</strong> (CRUD dasar).</li>
      <li>Buat UI untuk <strong>pipeline stages</strong> per perusahaan (Interest Low/Med/High, Contacted, dll).</li>
      <li>Tambahkan fitur assign CS + log perubahan stage (gunakan <code>pipeline_stage_histories</code> + <code>activity_logs</code>).</li>
    </ol>
    <p class="mb-0" style="font-size:13px; color:#6b7280;">
      Semua sudah ready di level struktur. Tinggal kita isi data dan logika per menu.
    </p>
  </div>
@endsection

@section('js')
<script>
  function updateClock(){
    const now = new Date();
    const h = now.getHours().toString().padStart(2,'0');
    const m = now.getMinutes().toString().padStart(2,'0');
    const s = now.getSeconds().toString().padStart(2,'0');

    const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
    const tanggal = now.toLocaleDateString('id-ID', opts);

    const elTime = document.getElementById('clockTime');
    const elDate = document.getElementById('clockDate');
    if(elTime) elTime.textContent = `${h}:${m}:${s} WIB`;
    if(elDate) elDate.textContent = tanggal;
  }

  updateClock();
  setInterval(updateClock, 1000);
</script>
@endsection
