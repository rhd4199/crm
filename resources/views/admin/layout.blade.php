<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>
    @yield('title', 'Dashboard') — Depati CRM
  </title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --brand:        #2e65b7;
      --brand-hover:  #24529a;
      --accent:       #43b581;
      --bg:           #f5f6f8;
      --card:         #ffffff;
      --border:       #e2e5ea;
      --text:         #1f2933;
      --muted:        #6b7280;
      --sidebar:      #1f2933;
      --sidebar-ink:  #cbd5f0;
      --sidebar-active:#111827;
    }

    html,body{
      height:100%;
      margin:0;
      font-family:"Source Sans Pro", system-ui, -apple-system, "Segoe UI", sans-serif;
      background:var(--bg);
      color:var(--text);
    }

    .app{
      min-height:100vh;
      display:flex;
      align-items:stretch;
    }

    /* Sidebar */
    .sidebar{
      width:260px;
      background:var(--sidebar);
      color:var(--sidebar-ink);
      display:flex;
      flex-direction:column;
    }
    .sidebar-brand{
      padding:16px 18px;
      border-bottom:1px solid rgba(148,163,184,.35);
      display:flex;
      align-items:center;
      gap:10px;
    }
    .sidebar-icon{
      width:32px;
      height:32px;
      border-radius:12px;
      background:var(--brand);
      display:flex;
      align-items:center;
      justify-content:center;
      color:#fff;
      font-weight:700;
      box-shadow:0 0 0 3px rgba(59,130,246,.45);
    }
    .sidebar-title{
      font-size:13px;
      letter-spacing:.05em;
      text-transform:uppercase;
      font-weight:700;
    }
    .sidebar-sub{
      font-size:11px;
      opacity:.8;
    }

    .sidebar-nav{
      flex:1;
      padding:12px 10px 16px;
      overflow-y:auto;
      font-size:14px;
    }
    .sidebar-nav small{
      display:block;
      text-transform:uppercase;
      font-size:11px;
      letter-spacing:.08em;
      color:rgba(203,213,225,.8);
      margin:10px 10px 4px;
    }
    .nav-link-custom{
      display:flex;
      align-items:center;
      gap:8px;
      padding:7px 10px;
      border-radius:10px;
      color:var(--sidebar-ink);
      text-decoration:none;
      font-size:14px;
    }
    .nav-link-custom i{
      width:18px;
      text-align:center;
    }
    .nav-link-custom:hover{
      background:rgba(148,163,184,.18);
      color:#ffffff;
    }
    .nav-link-custom.active{
      background:var(--sidebar-active);
      color:#ffffff;
      font-weight:600;
    }

    .sidebar-footer{
      padding:10px 14px 14px;
      border-top:1px solid rgba(148,163,184,.35);
      font-size:12px;
      color:rgba(203,213,225,.9);
    }

    /* Main */
    .main{
      flex:1;
      display:flex;
      flex-direction:column;
      min-width:0;
    }
    .topbar{
      height:60px;
      background:#ffffff;
      border-bottom:1px solid var(--border);
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:0 18px;
    }
    .topbar-left{
      display:flex;
      align-items:center;
      gap:10px;
    }
    .topbar-title{
      font-weight:600;
      font-size:16px;
    }
    .topbar-sub{
      font-size:12px;
      color:var(--muted);
    }

    .topbar-right{
      display:flex;
      align-items:center;
      gap:12px;
    }
    .avatar-circle{
      width:32px;
      height:32px;
      border-radius:999px;
      background:#e5e7eb;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:14px;
      font-weight:600;
      color:#374151;
    }

    .content{
      padding:18px;
    }
    .card-soft{
      background:var(--card);
      border-radius:16px;
      border:1px solid var(--border);
      padding:16px 18px;
      box-shadow:0 2px 5px rgba(15,23,42,.03);
    }

    @media (max-width: 960px){
      .sidebar{
        display:none;
      }
      .app{
        flex-direction:column;
      }
    }
  </style>

  @yield('css')
</head>
<body>
<div class="app">
  <!-- SIDEBAR -->
  <aside class="sidebar d-none d-md-flex">
    <div class="sidebar-brand">
      <div class="sidebar-icon">
        <i class="fa-solid fa-user-group"></i>
      </div>
      <div>
        <div class="sidebar-title">DEPATI CRM</div>
        <div class="sidebar-sub">Admin Panel</div>
      </div>
    </div>

    <nav class="sidebar-nav">
      @yield('menu')
    </nav>

    <div class="sidebar-footer">
      <div>v0.1 — internal</div>
      <div style="opacity:.8;">© {{ date('Y') }} Depati Akademi</div>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <header class="topbar">
      <div class="topbar-left">
        <div class="topbar-title">@yield('title', 'Dashboard')</div>
        <div class="topbar-sub d-none d-sm-block">@yield('subtitle', 'Ringkasan aktivitas CRM hari ini')</div>
      </div>
      <div class="topbar-right">
        @php($user = auth()->user())
        @if($user)
          <div class="text-end d-none d-sm-block">
            <div style="font-size:13px; font-weight:600;">{{ $user->name }}</div>
            <div style="font-size:11px; color:var(--muted); text-transform:capitalize;">
              {{ $user->global_role === 'super_admin' ? 'Super Admin' : $user->company_role }}
            </div>
          </div>
          <div class="avatar-circle">
            {{ strtoupper(substr($user->name,0,1)) }}
          </div>
          <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">
              <i class="fa-solid fa-right-from-bracket me-1"></i>Keluar
            </button>
          </form>
        @endif
      </div>
    </header>

    <main class="content">
      @yield('content')
    </main>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('js')
</body>
</html>
