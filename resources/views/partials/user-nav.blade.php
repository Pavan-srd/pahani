{{--
  Shared user navigation bar.
  Usage:  @include('partials.user-nav', ['active' => 'bb-upload'])
  active: dashboard | pahani-upload | pahani-view | bb-upload | bb-view | reports
--}}
@php
  $active   = $active ?? '';
  $isPahani = in_array($active, ['pahani-upload', 'pahani-view'], true);
  $isBb     = in_array($active, ['bb-upload', 'bb-view'], true);
@endphp

<style>
  .page-nav{display:flex;justify-content:space-between;align-items:center;background:#154360;border-bottom:2px solid #f39c12;padding:0 20px;font-size:11px;position:relative;z-index:6000}
  .page-nav .nav-left{display:flex;align-items:stretch;flex-wrap:wrap}
  .page-nav .nav-item{color:#aed6f1;padding:8px 14px;cursor:pointer;border:none;border-right:1px solid rgba(255,255,255,0.1);background:none;font:inherit;font-size:11px;transition:background 0.15s;text-decoration:none;display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
  .page-nav .nav-item:hover,.page-nav .nav-dd.open > .nav-item{background:rgba(255,255,255,0.1);color:white}
  .page-nav .nav-item.active,.page-nav .nav-dd.active > .nav-item{background:#f39c12;color:#1a1a2e;font-weight:bold}
  .page-nav .nav-item .caret{font-size:8px;margin-left:2px;transition:transform 0.15s}
  .page-nav .nav-dd.open > .nav-item .caret{transform:rotate(180deg)}
  .page-nav .nav-dd{position:relative;display:flex}
  .page-nav .dd-menu{display:none;position:absolute;top:100%;left:0;min-width:240px;background:white;border:1px solid #c8dce9;border-top:3px solid #f39c12;box-shadow:0 6px 18px rgba(0,0,0,0.18);padding:4px 0}
  .page-nav .nav-dd.open .dd-menu{display:block}
  @media (hover:hover){ .page-nav .nav-dd:hover .dd-menu{display:block} }
  .page-nav .dd-item{display:flex;align-items:center;gap:8px;padding:9px 14px;color:#154360;text-decoration:none;font-size:11px;font-weight:bold;border-left:3px solid transparent}
  .page-nav .dd-item:hover{background:#eaf2f8;border-left-color:#1a6fa8}
  .page-nav .dd-item.active{background:#fff7e6;border-left-color:#f39c12;color:#1a1a2e}
  .page-nav .nav-right{margin-left:auto}
  .page-nav .logout-btn{background:#dc3545;color:#fff;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-weight:600;transition:.2s}
  .page-nav .logout-btn:hover{background:#bb2d3b}
  @media(max-width:700px){
    .page-nav{padding:0 8px}
    .page-nav .dd-menu{min-width:200px}
  }
</style>

<div class="page-nav">
  <div class="nav-left">
    <a class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('home') }}">🏠 Dashboard</a>

    <div class="nav-dd {{ $isPahani ? 'active' : '' }}">
      <button type="button" class="nav-item dd-toggle" aria-haspopup="true" aria-expanded="false">
        📂 Pahani <span class="caret">▼</span>
      </button>
      <div class="dd-menu" role="menu">
        <a class="dd-item {{ $active === 'pahani-upload' ? 'active' : '' }}" role="menuitem" href="{{ route('pahani.index') }}">📤 Upload Pahani</a>
        <a class="dd-item {{ $active === 'pahani-view' ? 'active' : '' }}" role="menuitem" href="{{ route('pahani.view') }}">📋 View Pahani</a>
      </div>
    </div>

    <div class="nav-dd {{ $isBb ? 'active' : '' }}">
      <button type="button" class="nav-item dd-toggle" aria-haspopup="true" aria-expanded="false">
        🗂️ Bhu Bharathi <span class="caret">▼</span>
      </button>
      <div class="dd-menu" role="menu">
        <a class="dd-item {{ $active === 'bb-upload' ? 'active' : '' }}" role="menuitem" href="{{ route('bhu-bharathi.index') }}">📤 Upload Bhu Bharathi Disposals</a>
        <a class="dd-item {{ $active === 'bb-view' ? 'active' : '' }}" role="menuitem" href="{{ route('bhu-bharathi.my-files') }}">📋 View Bhu Bharathi Disposals</a>
      </div>
    </div>

    <a class="nav-item {{ $active === 'reports' ? 'active' : '' }}" href="{{ route('reports.user') }}">📊 Reports</a>
  </div>

  <div class="nav-right">
    <form id="user-nav-logout-form" method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="button" class="logout-btn" onclick="userNavLogout()">🚪 Logout</button>
    </form>
  </div>
</div>

<script>
(function () {
  // Click / tap to open (needed on touch screens); hover also opens on desktop via CSS.
  const dds = document.querySelectorAll('.page-nav .nav-dd');
  const closeAll = except => dds.forEach(dd => {
    if (dd !== except) {
      dd.classList.remove('open');
      dd.querySelector('.dd-toggle')?.setAttribute('aria-expanded', 'false');
    }
  });

  dds.forEach(dd => {
    const btn = dd.querySelector('.dd-toggle');
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const open = !dd.classList.contains('open');
      closeAll(dd);
      dd.classList.toggle('open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });

  document.addEventListener('click', () => closeAll(null));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(null); });
})();

function userNavLogout() {
  const form = document.getElementById('user-nav-logout-form');
  if (window.Swal) {
    Swal.fire({
      title: 'Logout?', text: 'Are you sure you want to logout?', icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, Logout', cancelButtonText: 'Cancel'
    }).then(r => { if (r.isConfirmed) form.submit(); });
  } else if (confirm('Are you sure you want to logout?')) {
    form.submit();
  }
}
</script>