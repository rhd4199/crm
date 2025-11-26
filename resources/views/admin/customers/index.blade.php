@extends('admin.layout')

@section('title', 'Customers')
@section('subtitle', 'Daftar lead & customer')

@section('menu')
  @include('admin.partials.menu', ['active' => 'customers'])
@endsection

@section('content')
  @if(session('success'))
    <div class="alert alert-success py-2 px-3 small">
      {{ session('success') }}
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="mb-0">Customers</h5>
      <div style="font-size:13px; color:#6b7280;">Kelola customer di perusahaan Anda.</div>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
      <i class="fa-solid fa-user-plus me-1"></i> Tambah Customer
    </a>
  </div>

  <form method="GET" class="card-soft mb-3">
    <div class="row g-2 align-items-end">
  
      @if(auth()->user()?->global_role === 'super_admin')
        <div class="col-md-3">
          <label class="form-label mb-1">Perusahaan</label>
          <select name="company_id" class="form-select form-select-sm" onchange="this.form.submit()">
            @forelse($companies as $company)
              <option value="{{ $company->id }}" @selected($companyId == $company->id)>
                {{ $company->name }}
              </option>
            @empty
              <option value="">Belum ada perusahaan</option>
            @endforelse
          </select>
        </div>
      @endif
  
      {{-- kolom-kolom filter lain: q, stage, marketing, cs --}}
      <div class="col-md-3">
        <label class="form-label mb-1">Cari</label>
        <input type="text" name="q" class="form-control form-control-sm"
               placeholder="Nama / HP / Email"
               value="{{ request('q') }}">
      </div>
  
      <div class="col-md-3">
        <label class="form-label mb-1">Stage</label>
        <select name="stage_id" class="form-select form-select-sm">
          <option value="">Semua stage</option>
          @foreach($pipelineStages as $stage)
            <option value="{{ $stage->id }}" @selected(request('stage_id') == $stage->id)>
              {{ $stage->name }}
            </option>
          @endforeach
        </select>
      </div>
  
      <div class="col-md-3">
        <label class="form-label mb-1">Marketing</label>
        <select name="marketing_id" class="form-select form-select-sm">
          <option value="">Semua</option>
          @foreach($marketingUsers as $m)
            <option value="{{ $m->id }}" @selected(request('marketing_id') == $m->id)>
              {{ $m->name }}
            </option>
          @endforeach
        </select>
      </div>
  
      <div class="col-md-3">
        <label class="form-label mb-1">Customer Service</label>
        <select name="cs_id" class="form-select form-select-sm">
          <option value="">Semua</option>
          @foreach($csUsers as $c)
            <option value="{{ $c->id }}" @selected(request('cs_id') == $c->id)>
              {{ $c->name }}
            </option>
          @endforeach
        </select>
      </div>
  
      <div class="col-12 mt-2">
        <button class="btn btn-sm btn-outline-primary">
          <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
        </button>
        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-link">
          Reset
        </a>
      </div>
    </div>
  </form>
  
  
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Customers</h5>
    <a href="{{ route('customers.export.csv', request()->query()) }}"
       class="btn btn-sm btn-outline-secondary">
      <i class="fa-solid fa-file-export me-1"></i> Export CSV
    </a>
  </div>
  <div class="card-soft">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Kontak</th>
          <th>Source</th>
          <th>Stage</th>
          <th>Marketing</th>
          <th>CS</th>
          <th>Last Contact</th>
          <th class="text-end">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($customers as $i => $customer)
          <tr>
            <td>{{ $customers->firstItem() + $i }}</td>
            <td>
              <div class="fw-semibold">{{ $customer->name }}</div>
              @if($customer->tag)
                <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">
                  {{ $customer->tag }}
                </span>
              @endif
            </td>
            <td style="font-size:13px;">
                @if($customer->phone)
                  <div class="d-flex align-items-center gap-1">
                    <i class="fa-brands fa-whatsapp me-1 text-success"></i>
                    <span>{{ $customer->phone }}</span>
                    <a href="@waLink($customer->phone)" target="_blank"
                       class="btn btn-xs btn-outline-success btn-sm ms-1">
                      <i class="fa-solid fa-paper-plane"></i>
                    </a>
                  </div>
                @endif
                @if($customer->email)
                  <div><i class="fa-regular fa-envelope me-1"></i>{{ $customer->email }}</div>
                @endif
              </td>
            <td style="font-size:13px;">
              {{ $customer->source ?? '-' }}
            </td>
            <td>
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
              
                  <select class="form-select form-select-sm js-stage-select"
                          data-customer-id="{{ $customer->id }}"
                          style="min-width:150px;">
                    @foreach($pipelineStages as $stage)
                      <option value="{{ $stage->id }}"
                              data-type="{{ $stage->type }}"
                              @selected($customer->current_stage_id == $stage->id)>
                        {{ $stage->name }}
                      </option>
                    @endforeach
                  </select>
              
                  <div class="mt-1">
                    <span class="badge {{ $badgeClass }} border rounded-pill px-2 js-stage-badge" style="font-size:11px;">
                      {{ $customer->currentStage->name }}
                    </span>
                  </div>
                @else
                  <select class="form-select form-select-sm js-stage-select"
                          data-customer-id="{{ $customer->id }}"
                          style="min-width:150px;">
                    <option value="">Pilih stage...</option>
                    @foreach($pipelineStages as $stage)
                      <option value="{{ $stage->id }}" data-type="{{ $stage->type }}">
                        {{ $stage->name }}
                      </option>
                    @endforeach
                  </select>
                @endif
              </td>
            <td style="font-size:13px;">
              {{ $customer->marketing?->name ?? '-' }}
            </td>
            <td style="font-size:13px;">
              {{ $customer->customerService?->name ?? '-' }}
            </td>
            <td style="font-size:12px;">
                @if($customer->last_contact_at)
                  {{ $customer->last_contact_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                @else
                  <span class="text-muted">Belum ada</span>
                @endif
              </td>
              <td class="text-end">
                {{-- Quick interaction --}}
                <button type="button"
                        class="btn btn-xs btn-outline-success btn-sm js-open-interaction-modal"
                        data-customer-id="{{ $customer->id }}"
                        data-customer-name="{{ $customer->name }}"
                        data-bs-toggle="modal"
                        data-bs-target="#interactionModal">
                  <i class="fa-solid fa-plus"></i>
                </button>
              
                <a href="{{ route('customers.show', $customer) }}" class="btn btn-xs btn-outline-secondary btn-sm">
                  <i class="fa-regular fa-eye"></i>
                </a>
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-xs btn-outline-primary btn-sm">
                  <i class="fa-regular fa-pen-to-square"></i>
                </a>
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus customer ini?')">
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
            <td colspan="8" class="text-center text-muted py-4">
              Belum ada customer. <a href="{{ route('customers.create') }}">Tambah satu sekarang.</a>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-2">
      {{ $customers->withQueryString()->links() }}
    </div>
  </div>

  {{-- Modal Quick Interaction --}}
<div class="modal fade" id="interactionModal" tabindex="-1" aria-labelledby="interactionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="interactionForm">
        @csrf
        <input type="hidden" name="redirect" value="back">

        <div class="modal-header py-2">
          <h6 class="modal-title" id="interactionModalLabel">Catat Interaksi</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-2 small text-muted">
            Customer: <span id="interactionCustomerName" class="fw-semibold"></span>
          </div>

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
              <label class="form-label mb-1">Ringkasan</label>
              <textarea name="summary" rows="3" class="form-control form-control-sm" required
                        placeholder="Contoh: Telpon, tertarik tapi minta dihubungi lagi setelah gajian."></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer py-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('js')
<script>
  $(function () {
    const token = $('meta[name="csrf-token"]').attr('content');

    // --- Quick change stage (kalau sudah ada, biarkan) ---
    function typeToBadgeClass(type) {
      switch (type) {
        case 'won':
          return 'bg-success-subtle text-success border-success-subtle';
        case 'lost':
          return 'bg-danger-subtle text-danger border-danger-subtle';
        case 'hold':
          return 'bg-warning-subtle text-warning border-warning-subtle';
        default:
          return 'bg-primary-subtle text-primary border-primary-subtle';
      }
    }

    $('.js-stage-select').on('change', function () {
      const $select = $(this);
      const customerId = $select.data('customer-id');
      const stageId = $select.val();

      if (!stageId) return;

      $select.prop('disabled', true);

      $.ajax({
        url: '{{ url('customers') }}/' + customerId + '/stage',
        method: 'POST',
        data: {
          _token: token,
          stage_id: stageId
        },
        success: function (res) {
          if (res.stage) {
            const type = res.stage.type;
            const name = res.stage.name;

            const $badge = $select.closest('td').find('.js-stage-badge');
            $badge
              .attr('class', 'badge border rounded-pill px-2 js-stage-badge ' + typeToBadgeClass(type))
              .text(name);
          }
        },
        error: function (xhr) {
          alert(xhr.responseJSON?.message || 'Gagal mengubah stage.');
          window.location.reload();
        },
        complete: function () {
          $select.prop('disabled', false);
        }
      });
    });

    // --- Quick interaction modal ---
    $('.js-open-interaction-modal').on('click', function () {
      const customerId = $(this).data('customer-id');
      const customerName = $(this).data('customer-name');

      const actionUrl = '{{ url('customers') }}/' + customerId + '/interactions';

      $('#interactionForm').attr('action', actionUrl);
      $('#interactionCustomerName').text(customerName);

      // reset form setiap kali buka
      $('#interactionForm')[0].reset();
      $('input[name="redirect"]').val('back');
    });
  });
</script>
@endsection

