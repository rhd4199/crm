@extends('admin.layout')

@section('title', 'Detail Customer')
@section('subtitle', 'Timeline progres & interaksi')

@section('menu')
  @include('admin.partials.menu', ['active' => 'customers'])
@endsection

@section('css')
<style>
  .badge-stage{
    font-size:11px;
    border-radius:999px;
    padding:3px 8px;
  }
  .timeline{
    border-left:2px solid #e5e7eb;
    padding-left:14px;
    margin-left:6px;
  }
  .timeline-item{
    position:relative;
    margin-bottom:12px;
    padding-bottom:4px;
  }
  .timeline-item::before{
    content:'';
    position:absolute;
    left:-16px;
    top:4px;
    width:10px;
    height:10px;
    border-radius:999px;
    background:#3b82f6;
    border:2px solid #e5e7eb;
  }
  .timeline-date{
    font-size:11px;
    color:#9ca3af;
  }
</style>
@endsection

@section('content')
  @if(session('success'))
    <div class="alert alert-success py-2 px-3 small">
      {{ session('success') }}
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fa-solid fa-arrow-left-long me-1"></i> Kembali ke list
      </a>
      <h5 class="mb-0">{{ $customer->name }}</h5>
      <div style="font-size:13px; color:#6b7280;">
        @if($customer->company)
          {{ $customer->company->name }}
        @endif
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">
        <i class="fa-regular fa-pen-to-square me-1"></i> Edit
      </a>
      <form action="{{ route('customers.destroy', $customer) }}" method="POST"
            onsubmit="return confirm('Yakin ingin menghapus customer ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">
          <i class="fa-regular fa-trash-can me-1"></i> Hapus
        </button>
      </form>
    </div>
  </div>

  <div class="row g-3">
    <!-- Panel info -->
    <div class="col-lg-4">
      <div class="card-soft mb-3">
        <h6 class="mb-2">Info Customer</h6>
        <div class="mb-1" style="font-size:13px;">
          <div class="text-muted">Kontak</div>
          @if($customer->phone)
            <div class="d-flex align-items-center gap-2">
                <span>
                    <i class="fa-brands fa-whatsapp text-success me-1"></i>{{ $customer->phone }}
                </span>
                <a href="@waLink($customer->phone)" target="_blank"
                    class="btn btn-xs btn-outline-success btn-sm">
                    <i class="fa-solid fa-paper-plane"></i> Chat
                </a>
            </div>
            @endif
          @if($customer->email)
            <div><i class="fa-regular fa-envelope me-1"></i>{{ $customer->email }}</div>
          @endif
        </div>

        <div class="mb-1" style="font-size:13px;">
          <div class="text-muted">Source</div>
          <div>{{ $customer->source ?? '-' }}</div>
        </div>

        <div class="mb-1" style="font-size:13px;">
          <div class="text-muted">Stage saat ini</div>
          @if($customer->currentStage)
            @php
              $type = $customer->currentStage->type;
              $badgeClass = match($type){
                'won'  => 'bg-success-subtle text-success border-success-subtle',
                'lost' => 'bg-danger-subtle text-danger border-danger-subtle',
                'hold' => 'bg-warning-subtle text-warning border-warning-subtle',
                default => 'bg-primary-subtle text-primary border-primary-subtle'
              };
            @endphp
            <span class="badge badge-stage {{ $badgeClass }} border">
              {{ $customer->currentStage->name }}
            </span>
          @else
            <span class="text-muted">Belum di-set</span>
          @endif
        </div>

        <div class="mb-1" style="font-size:13px;">
          <div class="text-muted">Marketing</div>
          <div>{{ $customer->marketing?->name ?? '-' }}</div>
        </div>

        <div class="mb-1" style="font-size:13px;">
          <div class="text-muted">Customer Service</div>
          <div>{{ $customer->customerService?->name ?? '-' }}</div>
        </div>

        <div class="mb-1" style="font-size:13px;">
          <div class="text-muted">Perkiraan Nilai Deal</div>
          <div>
            @if($customer->estimated_value)
              Rp {{ number_format($customer->estimated_value, 0, ',', '.') }}
            @else
              -
            @endif
          </div>
        </div>

        <div class="mb-1" style="font-size:13px;">
          <div class="text-muted">Kontak terakhir</div>
          <div>
            @if($customer->last_contact_at)
              {{ $customer->last_contact_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
            @else
              Belum ada catatan interaksi
            @endif
          </div>
        </div>

        @if($customer->notes)
          <div class="mt-2" style="font-size:13px;">
            <div class="text-muted mb-1">Catatan</div>
            <div>{{ $customer->notes }}</div>
          </div>
        @endif
      </div>
    </div>

    <!-- Panel interaksi & timeline -->
    <div class="col-lg-8">
      <div class="card-soft mb-3">
        <div class="d-flex justify-content-between mb-2">
          <h6 class="mb-0">Interaksi Terbaru</h6>
          <span class="small text-muted">Marketing & CS bisa catat follow-up di sini</span>
        </div>

        <form method="POST" action="{{ route('customers.interactions.store', $customer) }}" class="mb-3">
          @csrf
          <div class="row g-2">
            <div class="col-md-3">
              <label class="form-label mb-1">Channel</label>
              <select name="channel" class="form-select form-select-sm" required>
                <option value="whatsapp">WhatsApp</option>
                <option value="phone">Telepon</option>
                <option value="email">Email</option>
                <option value="meeting">Meeting</option>
                <option value="other">Lainnya</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label mb-1">Arah</label>
              <select name="direction" class="form-select form-select-sm" required>
                <option value="outbound">Kita menghubungi</option>
                <option value="inbound">Customer menghubungi</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label mb-1">Follow up lagi</label>
              <input type="datetime-local" name="follow_up_at" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
              <label class="form-label mb-1">Durasi (detik)</label>
              <input type="number" name="duration_seconds" class="form-control form-control-sm" min="0">
            </div>
            <div class="col-12 mt-2">
              <label class="form-label mb-1">Ringkasan Interaksi</label>
              <textarea name="summary" rows="2" class="form-control form-control-sm" required
                        placeholder="Contoh: Telpon, tertarik tapi minta dihubungi lagi setelah gajian."></textarea>
            </div>
          </div>
          <div class="mt-2 text-end">
            <button type="submit" class="btn btn-sm btn-primary">
              <i class="fa-solid fa-plus me-1"></i> Simpan Interaksi
            </button>
          </div>
        </form>

        @if($interactions->isEmpty())
          <div class="text-muted small">
            Belum ada interaksi tercatat.
          </div>
        @else
          <div class="timeline">
            @foreach($interactions as $interaction)
              <div class="timeline-item">
                <div class="d-flex justify-content-between">
                  <div style="font-size:13px;">
                    <strong>
                      @if($interaction->channel === 'whatsapp')
                        <i class="fa-brands fa-whatsapp text-success"></i> WhatsApp
                      @elseif($interaction->channel === 'phone')
                        <i class="fa-solid fa-phone"></i> Telepon
                      @elseif($interaction->channel === 'email')
                        <i class="fa-regular fa-envelope"></i> Email
                      @elseif($interaction->channel === 'meeting')
                        <i class="fa-solid fa-handshake"></i> Meeting
                      @else
                        <i class="fa-regular fa-comment"></i> Lainnya
                      @endif
                    </strong>
                    <span class="text-muted">
                      • {{ $interaction->direction === 'outbound' ? 'Kita menghubungi' : 'Customer menghubungi' }}
                    </span>
                  </div>
                  <div class="timeline-date">
                    {{ $interaction->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                  </div>
                </div>
                <div class="small mt-1">
                  {{ $interaction->summary }}
                </div>
                <div class="small text-muted mt-1 d-flex justify-content-between">
                  <span>
                    Oleh: {{ $interaction->user?->name ?? '-' }}
                  </span>
                  <span>
                    @if($interaction->follow_up_at)
                      Follow up: {{ $interaction->follow_up_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                    @endif
                    @if($interaction->duration_seconds)
                      • Durasi: {{ $interaction->duration_seconds }} detik
                    @endif
                  </span>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <div class="card-soft">
        <h6 class="mb-2">Riwayat Perubahan Stage</h6>
        @if($stageHistories->isEmpty())
          <div class="small text-muted">
            Belum ada perubahan stage tercatat.
          </div>
        @else
          <div class="timeline">
            @foreach($stageHistories as $history)
              <div class="timeline-item">
                <div class="timeline-date">
                  {{ $history->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                </div>
                <div class="small">
                  Stage:
                  <strong>{{ $history->fromStage?->name ?? 'Baru dibuat' }}</strong>
                  <i class="fa-solid fa-arrow-right mx-1"></i>
                  <strong>{{ $history->toStage?->name }}</strong>
                </div>
                <div class="small text-muted">
                  Oleh: {{ $history->changer?->name ?? '-' }}
                  @if($history->note)
                    • {{ $history->note }}
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
