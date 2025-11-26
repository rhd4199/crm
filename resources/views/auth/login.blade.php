<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Login — Depati CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>

  <!-- Font & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css" rel="stylesheet"/>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <style>
    :root{
      --bg: #0b1720;
      --panel: #ffffff;
      --line: #dde2ea;
      --brand: #2e65b7;      /* Depati-ish blue */
      --brand-soft: #e3ecfb;
      --accent: #43b581;
      --danger: #e55353;
      --ink: #222a2e;
      --muted: #6b7280;
    }

    *{ box-sizing:border-box; }
    body{
      margin:0;
      font-family: "Source Sans Pro", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: radial-gradient(circle at top, #0f2233, #020509 60%);
      color: var(--ink);
      min-height: 100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:16px;
    }

    .shell{
      max-width:1120px;
      width:100%;
      border-radius:24px;
      overflow:hidden;
      background: #f7f9fc;
      box-shadow:
        0 22px 45px rgba(15,23,42,.40),
        0 0 0 1px rgba(15,23,42,.65);
      display:grid;
      grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
      min-height:560px;
    }

    @media (max-width: 900px){
      .shell{ grid-template-columns: minmax(0,1fr); }
      .hero{ display:none; }
    }

    /* ---------- HERO ---------- */
    .hero{
      position:relative; overflow:hidden;
      background:
        linear-gradient(120deg, rgba(79,125,200,.35), rgba(59,130,246,.10)),
        url('https://images.unsplash.com/photo-1531297484001-80022131f5a1?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;
      border-right:1px solid var(--line);
    }
    .hero::before{
      content:""; position:absolute; inset:0; background:linear-gradient(135deg, rgba(3,7,18,.9), rgba(15,23,42,.2));
    }
    .hero-content{
      position:relative; z-index:1; color:#e5edf8; height:100%;
      display:flex; flex-direction:column; justify-content:space-between;
      padding:32px 32px 24px;
    }
    .badge-pill{
      display:inline-flex; align-items:center; gap:8px;
      padding:6px 12px;
      border-radius:999px;
      background:rgba(15,23,42,.6);
      font-size:12px;
      letter-spacing:.03em;
      text-transform:uppercase;
    }
    .hero-title{
      font-size:28px;
      font-weight:700;
      margin-top:16px;
      margin-bottom:8px;
    }
    .hero-sub{
      font-size:15px;
      color:#c5d2e8;
      max-width:420px;
    }
    .hero-metrics{
      display:flex; gap:16px; flex-wrap:wrap; margin-top:24px;
    }
    .metric-card{
      background:rgba(15,23,42,.70);
      border-radius:16px;
      padding:12px 16px;
      min-width:130px;
      border:1px solid rgba(148,163,184,.35);
    }
    .metric-label{ font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:#9fb3d9; }
    .metric-value{ font-size:20px; font-weight:700; margin-top:4px; }

    .hero-footer{
      display:flex; align-items:center; justify-content:space-between;
      font-size:12px; color:#9fb3d9;
      gap:12px;
    }
    .avatars{
      display:flex;
    }
    .avatars img{
      width:28px; height:28px; border-radius:999px;
      border:2px solid rgba(15,23,42,.9);
      margin-left:-8px;
      background:#64748b;
      object-fit:cover;
    }
    .avatars img:first-child{ margin-left:0; }

    /* ---------- FORM PANEL ---------- */
    .panel{
      background:var(--panel);
      padding:32px 32px 24px;
      display:flex;
      flex-direction:column;
      justify-content:space-between;
    }
    .brand{
      display:flex; align-items:center; gap:10px; margin-bottom:24px;
    }
    .brand-icon{
      width:32px; height:32px; border-radius:12px;
      display:inline-flex; align-items:center; justify-content:center;
      background:var(--brand);
      color:#fff;
      font-weight:700;
      box-shadow:0 0 0 3px var(--brand-soft);
    }
    .brand-text{ font-weight:700; letter-spacing:.03em; text-transform:uppercase; font-size:13px; color:#111827; }

    .panel-title{
      font-size:22px; font-weight:700;
    }
    .panel-sub{
      font-size:14px;
      color:var(--muted);
      margin-bottom:20px;
    }

    .form-label{ font-weight:600; font-size:13px; color:#111827; }
    .form-control{
      border-radius:10px;
      border-color:#d1d5db;
      font-size:14px;
      padding:9px 11px;
    }
    .form-control:focus{
      border-color:var(--brand);
      box-shadow:0 0 0 3px rgba(59,130,246,.18);
    }

    .btn-brand{
      background:var(--brand);
      color:#fff;
      font-weight:600;
      border-radius:999px;
      border:none;
      padding:9px 16px;
      font-size:14px;
      display:inline-flex; align-items:center; justify-content:center; gap:6px;
    }
    .btn-brand:hover{ background:#25539a; color:#fff; }

    .password-toggle{
      position:absolute;
      right:10px;
      top:50%;
      transform:translateY(-50%);
      border:none;
      background:transparent;
      color:#9ca3af;
    }

    .alert-small{
      border-radius:10px;
      padding:8px 10px;
      font-size:13px;
    }

    .panel-footer{
      font-size:12px;
      color:var(--muted);
      margin-top:20px;
      display:flex;
      justify-content:space-between;
      gap:8px;
      flex-wrap:wrap;
    }
    .panel-footer a{
      color:var(--brand);
      text-decoration:none;
      font-weight:600;
    }
    .panel-footer a:hover{ text-decoration:underline; }
  </style>
</head>
<body>
  <div class="shell">
    <!-- HERO KIRI -->
    <aside class="hero">
      <div class="hero-content">
        <div>
          <div class="badge-pill">
            <span class="badge-dot bg-success rounded-circle" style="width:8px;height:8px;"></span>
            Depati CRM • Multi Perusahaan
          </div>
          <h1 class="hero-title mt-3">Kelola relasi customer lintas perusahaan, dalam satu panel.</h1>
          <p class="hero-sub">
            Pantau progres lead, follow-up CS, dan performa tim marketing dari satu dashboard terpusat.
          </p>

          <div class="hero-metrics">
            <div class="metric-card">
              <div class="metric-label">Lead aktif</div>
              <div class="metric-value">+128</div>
              <div style="font-size:11px;color:#9fb3d9;">dalam 30 hari terakhir</div>
            </div>
            <div class="metric-card">
              <div class="metric-label">Closing rate</div>
              <div class="metric-value">32%</div>
              <div style="font-size:11px;color:#9fb3d9;">dari lead yang di-contact</div>
            </div>
          </div>
        </div>

        <div class="hero-footer">
          <div class="d-flex align-items-center gap-2">
            <div class="avatars">
              <img src="https://i.pravatar.cc/60?img=1" alt="">
              <img src="https://i.pravatar.cc/60?img=2" alt="">
              <img src="https://i.pravatar.cc/60?img=3" alt="">
            </div>
            <div>
              <div style="font-size:12px;">Dipakai tim marketing & CS</div>
              <div style="font-size:11px;color:#9fb3d9;">untuk tracking follow-up harian</div>
            </div>
          </div>
          <div>
            <i class="fa-solid fa-shield-heart me-1"></i> Data terenkripsi & multi-tenant
          </div>
        </div>
      </div>
    </aside>

    <!-- PANEL LOGIN KANAN -->
    <main class="panel">
      <div>
        <div class="brand">
          <div class="brand-icon">
            <i class="fa-solid fa-people-arrows"></i>
          </div>
          <div>
            <div class="brand-text">DEPATI CRM</div>
            <div style="font-size:11px; color:var(--muted); margin-top:2px;">
              Customer Relationship Manager
            </div>
          </div>
        </div>

        <h2 class="panel-title mb-1">Masuk ke panel admin</h2>
        <p class="panel-sub mb-3">Gunakan akun Super Admin atau Admin Perusahaan untuk mengakses dashboard.</p>

        @if ($errors->any())
          <div class="alert alert-danger alert-small d-flex align-items-start gap-2">
            <i class="fa-solid fa-circle-exclamation mt-1"></i>
            <div>
              @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('login.perform') }}" class="mt-3">
          @csrf

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
              id="email"
              type="email"
              name="email"
              class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email') }}"
              required
              autofocus
            >
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="password" class="form-label mb-0">Password</label>
            </div>
            <div class="position-relative">
              <input
                id="password"
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                required
              >
              <button class="password-toggle" type="button" id="togglePw">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
              <label class="form-check-label" for="remember" style="font-size:13px;">
                Ingat saya
              </label>
            </div>
          </div>

          <button type="submit" class="btn-brand w-100">
            <i class="fa-solid fa-door-open"></i>
            <span>Masuk</span>
          </button>
        </form>
      </div>

      <div class="panel-footer">
        <span>© {{ date('Y') }} Depati CRM. All rights reserved.</span>
        <span>v0.1 — internal build</span>
      </div>
    </main>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function(){
      const btn = document.getElementById('togglePw');
      const inp = document.getElementById('password');
      if(!btn || !inp) return;

      btn.addEventListener('click', ()=>{
        const isPw = inp.type === 'password';
        inp.type = isPw ? 'text' : 'password';
        const icon = btn.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
      });
    })();
  </script>
</body>
</html>
