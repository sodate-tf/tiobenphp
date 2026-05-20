{{-- resources/views/partials/header-styles.blade.php --}}
<style>
  /* ===== Header “flat”: sem blur, sem transparência, sem sombras ===== */
  .tb-topbar{
    position:fixed; top:0; left:0; right:0;
    background:#fff;
    border-bottom:1px solid #e2e8f0;
    z-index:9999;
  }
  .tb-bottombar{
    position:fixed; left:0; right:0; bottom:0;
    background:#fff;
    border-top:1px solid #e2e8f0;
    z-index:9999;
  }

  .tb-wrap{ max-width:72rem; margin:0 auto; padding:0 16px; }
  .tb-h16{ height:64px; }

  .tb-brand{ display:flex; align-items:center; gap:10px; min-width:0; text-decoration:none; }
  .tb-brand-title{ font-weight:800; color:#78350f; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .tb-brand-sub{ font-size:12px; color:#92400e; opacity:.85; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

  .tb-nav{ display:flex; align-items:center; gap:10px; font-size:14px; font-weight:700; color:#78350f; white-space:nowrap; }
  .tb-link{ padding:8px 10px; border-radius:8px; text-decoration:none; color:inherit; }
  .tb-link:hover{ background:#f8fafc; }

  .tb-pill{ display:inline-flex; align-items:center; gap:8px; }
  .tb-lang{
    display:inline-flex; align-items:center; justify-content:center;
    padding:6px 10px;
    border:1px solid #e2e8f0;
    border-radius:999px;
    text-decoration:none;
    font-weight:800;
    color:#78350f;
    background:#fff;
    font-size:12px;
  }
  .tb-lang:hover{ background:#f8fafc; }
  .tb-lang-on{ background:#fffbeb; border-color:#fcd34d; }

  /* Dropdown “flat” */
  .tb-dd{ position:relative; }
  .tb-dd-btn{
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 10px;
    border:1px solid transparent;
    border-radius:8px;
    background:#fff;
    font-weight:800;
    color:#78350f;
    cursor:pointer;
  }
  .tb-dd-btn:hover{ background:#f8fafc; }
  .tb-dd-menu{
    position:absolute; right:0; top:calc(100% + 6px);
    width:360px; max-width:90vw;
    background:#fff;
    border:1px solid #e2e8f0;
    border-radius:8px;
    overflow:hidden;
    z-index:99999;
  }
  .tb-dd-head{
    padding:10px 12px;
    font-size:12px;
    font-weight:900;
    color:#92400e;
    border-bottom:1px solid #e2e8f0;
    background:#fff;
  }
  .tb-dd-item{
    display:block;
    padding:10px 12px;
    font-size:14px;
    font-weight:700;
    color:#0f172a;
    text-decoration:none;
  }
  .tb-dd-item:hover{ background:#f8fafc; }

  /* Mobile bottom nav “flat” */
  .tb-mnav{
    display:flex;
    align-items:center;
    justify-content:space-around;
    height:64px;
    font-size:11px;
    font-weight:800;
    color:#78350f;
  }
  .tb-mitem{
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:6px;
    text-decoration:none;
    color:inherit;
    background:transparent;
  }
  .tb-micon{
    height:32px; width:32px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    display:flex; align-items:center; justify-content:center;
    background:#fff;
  }
  .tb-mitem:hover .tb-micon{ background:#f8fafc; }

  .tb-mpanel{
    position:absolute; left:12px; right:12px; bottom:64px;
    background:#fff;
    border:1px solid #e2e8f0;
    border-radius:8px;
    overflow:hidden;
    z-index:99999;
  }
  .tb-mpanel-head{
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 12px;
    border-bottom:1px solid #e2e8f0;
    font-size:12px;
    font-weight:900;
    color:#92400e;
  }
  .tb-close{
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:#fff;
    padding:6px 10px;
    font-size:12px;
    font-weight:900;
    color:#0f172a;
    cursor:pointer;
  }
  .tb-close:hover{ background:#f8fafc; }

  .tb-hidden{ display:none !important; }
</style>