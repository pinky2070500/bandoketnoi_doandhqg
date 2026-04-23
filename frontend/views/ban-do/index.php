<?php
/** @var yii\web\View $this */
$this->title = 'Bản đồ kết nối – ĐHQG TP.HCM';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?= $this->title ?></title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --green:#16a37f;--green-dark:#0d7a5f;--green-lite:#e6f7f2;
  --orange:#f59e0b;--red:#ef4444;
  --gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;
  --gray-400:#9ca3af;--gray-500:#6b7280;--gray-600:#4b5563;
  --gray-700:#374151;--gray-800:#1f2937;--gray-900:#111827;
  --shadow-sm:0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.06);
  --shadow-md:0 4px 16px rgba(0,0,0,.10),0 2px 6px rgba(0,0,0,.07);
  --shadow-lg:0 20px 40px rgba(0,0,0,.14),0 8px 16px rgba(0,0,0,.10);
  --radius:12px;--radius-sm:8px;
  --panel-w:340px;--sidebar-w:256px;--top-h:52px;--bot-h:56px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;font-family:'Inter',sans-serif;font-size:14px;overflow:hidden;-webkit-tap-highlight-color:transparent}

/* ── TOPBAR ── */
#topbar{
  position:fixed;top:0;left:0;right:0;height:var(--top-h);
  background:rgba(255,255,255,.97);backdrop-filter:blur(12px);
  border-bottom:1px solid var(--gray-200);
  display:flex;align-items:center;gap:10px;padding:0 12px;
  z-index:1100;
}
.tb-logo{display:flex;align-items:center;gap:9px;flex-shrink:0;text-decoration:none}
.tb-logo-icon{
  width:34px;height:34px;border-radius:9px;flex-shrink:0;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 2px 6px rgba(22,163,127,.35);
}
.tb-logo-icon i{color:#fff;font-size:14px}
.tb-brand h1{font-size:14px;font-weight:700;color:var(--gray-900);letter-spacing:-.3px;white-space:nowrap}
.tb-brand p{font-size:10px;color:var(--gray-400);margin-top:1px;white-space:nowrap}
.tb-sep{width:1px;height:26px;background:var(--gray-200);flex-shrink:0}
#tb-search{
  display:flex;align-items:center;gap:7px;
  background:var(--gray-100);border:1.5px solid transparent;
  border-radius:22px;padding:0 12px;height:34px;
  flex:1;min-width:0;transition:border-color .2s,background .2s;
}
#tb-search:focus-within{background:#fff;border-color:var(--green)}
#tb-search i{color:var(--gray-400);font-size:12px;flex-shrink:0}
#search-input{
  border:none;background:transparent;outline:none;
  font-size:13px;color:var(--gray-800);width:100%;font-family:inherit;
}
#search-input::placeholder{color:var(--gray-400)}
.tb-ticker{flex:1;min-width:0;display:flex;align-items:center;gap:7px;overflow:hidden}
.tb-ticker-badge{
  font-size:9px;font-weight:700;letter-spacing:.5px;
  background:var(--green-lite);color:var(--green-dark);
  padding:2px 7px;border-radius:5px;flex-shrink:0;white-space:nowrap;
}
.tb-ticker-text{font-size:11.5px;color:var(--gray-600);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
/* Mobile: ẩn ticker + brand tagline */
#mob-filter-btn{
  display:none;width:34px;height:34px;border-radius:9px;flex-shrink:0;
  background:var(--gray-100);border:1.5px solid var(--gray-200);
  align-items:center;justify-content:center;cursor:pointer;
  color:var(--gray-600);font-size:14px;transition:all .18s;position:relative;
}
#mob-filter-btn.has-filter{background:var(--green-lite);border-color:var(--green);color:var(--green-dark)}
#mob-filter-dot{
  position:absolute;top:5px;right:5px;
  width:7px;height:7px;border-radius:50%;background:var(--red);
  display:none;border:1.5px solid #fff;
}
.tb-avatar{
  width:32px;height:32px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:11px;font-weight:700;cursor:pointer;
}

/* ── SIDEBAR DESKTOP ── */
#sidebar{
  position:fixed;top:var(--top-h);left:0;bottom:0;
  width:var(--sidebar-w);background:#fff;
  border-right:1px solid var(--gray-200);
  overflow-y:auto;overflow-x:hidden;z-index:1000;
  scrollbar-width:thin;scrollbar-color:var(--gray-200) transparent;
  transition:transform .3s cubic-bezier(.4,0,.2,1);
}
#sidebar::-webkit-scrollbar{width:4px}
#sidebar::-webkit-scrollbar-thumb{background:var(--gray-200);border-radius:4px}

.sb-block{padding:13px 15px;border-bottom:1px solid var(--gray-100)}
.sb-label{
  font-size:10px;font-weight:700;letter-spacing:.8px;
  color:var(--gray-400);text-transform:uppercase;margin-bottom:9px;
}
.type-list{display:flex;flex-direction:column;gap:4px}
.type-btn{
  display:flex;align-items:center;gap:9px;
  padding:8px 10px;border-radius:var(--radius-sm);
  border:1.5px solid var(--gray-200);background:var(--gray-50);
  color:var(--gray-600);font-size:12.5px;font-weight:500;
  cursor:pointer;transition:all .18s;text-align:left;width:100%;font-family:inherit;
}
.type-btn:hover{background:var(--gray-100)}
.type-btn.active{background:var(--green-lite);border-color:var(--green);color:var(--green-dark)}
.type-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}
.chip-row{display:flex;gap:5px;flex-wrap:wrap}
.chip{
  font-size:11.5px;font-weight:500;padding:5px 11px;border-radius:20px;
  border:1.5px solid var(--gray-200);background:var(--gray-50);
  color:var(--gray-500);cursor:pointer;transition:all .18s;user-select:none;
}
.chip.c-red  {background:#fef2f2;border-color:#fca5a5;color:#b91c1c}
.chip.c-amber{background:#fffbeb;border-color:#fcd34d;color:#92400e}
.chip.c-green{background:var(--green-lite);border-color:#6ee7d0;color:var(--green-dark)}
.filter-row{margin-bottom:8px}
.filter-row label{font-size:11px;color:var(--gray-500);display:block;margin-bottom:4px;font-weight:500}
.filter-row select{
  width:100%;padding:7px 10px;border:1.5px solid var(--gray-200);
  border-radius:var(--radius-sm);background:var(--gray-50);
  color:var(--gray-800);font-size:12.5px;outline:none;cursor:pointer;
  font-family:inherit;transition:border-color .2s;
}
.filter-row select:focus{border-color:var(--green)}
.layer-row{display:flex;align-items:center;justify-content:space-between;padding:5px 0}
.layer-label{font-size:12.5px;color:var(--gray-700);display:flex;align-items:center;gap:8px}
.layer-label i{width:14px;text-align:center}
.toggle-sw{
  position:relative;width:34px;height:19px;
  background:var(--gray-300);border-radius:10px;
  cursor:pointer;transition:background .2s;flex-shrink:0;
}
.toggle-sw.on{background:var(--green)}
.toggle-sw::after{
  content:'';position:absolute;top:2px;left:2px;
  width:15px;height:15px;border-radius:50%;background:#fff;
  box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .2s;
}
.toggle-sw.on::after{transform:translateX(15px)}
.stat-row{
  display:flex;justify-content:space-between;align-items:center;
  padding:5px 0;border-bottom:1px solid var(--gray-100);
}
.stat-row:last-child{border:none}
.stat-k{font-size:12px;color:var(--gray-500)}
.stat-v{font-size:13px;font-weight:600;color:var(--gray-800)}
.stat-v.g{color:var(--green)}.stat-v.o{color:var(--orange)}.stat-v.r{color:var(--red)}
#result-bar{
  padding:7px 15px;font-size:11.5px;color:var(--gray-500);
  background:var(--gray-50);border-bottom:1px solid var(--gray-100);
}
#result-bar strong{color:var(--green);font-weight:600}

/* ── MAP ── */
#map-wrap{
  position:fixed;top:var(--top-h);left:var(--sidebar-w);right:0;bottom:0;z-index:0;
  transition:left .3s cubic-bezier(.4,0,.2,1),right .3s cubic-bezier(.4,0,.2,1);
}
#map{width:100%;height:100%}
#map-wrap.panel-open{right:var(--panel-w)}

/* ── DETAIL PANEL ── */
#detail{
  position:fixed;top:var(--top-h);right:0;bottom:0;
  width:var(--panel-w);background:#fff;
  border-left:1px solid var(--gray-200);
  box-shadow:var(--shadow-lg);z-index:1050;overflow-y:auto;
  transform:translateX(100%);
  transition:transform .32s cubic-bezier(.4,0,.2,1);
}
#detail.open{transform:translateX(0)}
.dt-header{position:sticky;top:0;background:#fff;z-index:10;border-bottom:1px solid var(--gray-100)}
.dt-hero{height:130px;position:relative;overflow:hidden;background:linear-gradient(135deg,#0d7a5f,#16a37f,#34d399)}
.dt-hero-pattern{
  position:absolute;inset:0;opacity:.12;
  background-image:radial-gradient(circle at 20% 50%,#fff 1px,transparent 1px),
                   radial-gradient(circle at 80% 20%,#fff 1px,transparent 1px);
  background-size:28px 28px;
}
.dt-hero-icon{
  position:absolute;bottom:-18px;left:18px;
  width:58px;height:58px;border-radius:14px;
  background:#fff;display:flex;align-items:center;justify-content:center;
  box-shadow:var(--shadow-md);
}
.dt-hero-icon i{font-size:24px;color:var(--green)}
.dt-close{
  position:absolute;top:10px;right:10px;
  width:30px;height:30px;border-radius:8px;
  background:rgba(255,255,255,.2);backdrop-filter:blur(8px);
  border:none;cursor:pointer;color:#fff;font-size:13px;
  display:flex;align-items:center;justify-content:center;transition:background .18s;
}
.dt-close:hover{background:rgba(255,255,255,.35)}
.dt-meta{padding:26px 18px 14px}
.dt-badge{
  display:inline-flex;align-items:center;gap:5px;
  font-size:10px;font-weight:700;letter-spacing:.4px;
  padding:3px 9px;border-radius:20px;margin-bottom:9px;text-transform:uppercase;
}
.dt-name{font-size:16px;font-weight:700;color:var(--gray-900);line-height:1.35;letter-spacing:-.3px;margin-bottom:5px}
.dt-loc{font-size:12px;color:var(--gray-400);display:flex;align-items:center;gap:5px}
.dt-loc i{color:var(--green);font-size:11px}
.dt-section{padding:14px 18px;border-bottom:1px solid var(--gray-100)}
.dt-sec-title{font-size:10px;font-weight:700;letter-spacing:.8px;color:var(--gray-400);text-transform:uppercase;margin-bottom:11px}
.dt-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px}
.dt-kv{background:var(--gray-50);border-radius:var(--radius-sm);padding:9px 11px;border:1px solid var(--gray-100)}
.dt-kv .k{font-size:10px;color:var(--gray-400);margin-bottom:3px;font-weight:500}
.dt-kv .v{font-size:14px;font-weight:700;color:var(--gray-800)}
.dt-kv .u{font-size:11px;color:var(--gray-400);font-weight:400}
.dt-progress-wrap{padding:14px 18px}
.dt-prog-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:7px}
.dt-prog-label{font-size:12px;font-weight:600;color:var(--gray-700)}
.dt-prog-pct{font-size:13px;font-weight:700}
.dt-prog-track{height:7px;background:var(--gray-100);border-radius:4px;overflow:hidden}
.dt-prog-fill{height:100%;border-radius:4px;transition:width .5s cubic-bezier(.4,0,.2,1)}
.dt-prog-fill.waiting{background:var(--gray-300)}
.dt-prog-fill.active{background:linear-gradient(90deg,var(--orange),#fbbf24)}
.dt-prog-fill.done{background:linear-gradient(90deg,var(--green),#34d399)}
.dt-prog-steps{display:flex;justify-content:space-between;margin-top:7px}
.dt-prog-step{font-size:10px;color:var(--gray-400);text-align:center;flex:1}
.dt-prog-step::before{content:'';display:block;width:6px;height:6px;border-radius:50%;background:var(--gray-300);margin:0 auto 3px}
.dt-prog-step.done-step::before{background:var(--green)}
.dt-contact{
  margin:0 18px 16px;padding:13px;
  background:linear-gradient(135deg,var(--green-lite),#f0fdf9);
  border:1px solid #a7f3d0;border-radius:var(--radius);
}
.dt-contact-top{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.dt-contact-av{
  width:36px;height:36px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;
}
.dt-contact-name{font-size:13px;font-weight:600;color:var(--gray-800)}
.dt-contact-role{font-size:11px;color:var(--gray-500);margin-top:1px}
.dt-contact-phone{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--green-dark);font-weight:500}
.dt-contact-phone i{font-size:11px}

/* ── MAP FABs ── */
.map-fab{
  position:absolute;z-index:900;background:#fff;
  border:1px solid var(--gray-200);border-radius:var(--radius-sm);
  width:34px;height:34px;display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:var(--gray-600);font-size:13px;
  box-shadow:var(--shadow-sm);transition:all .18s;
}
.map-fab:hover{background:var(--gray-50);color:var(--green);border-color:var(--green)}
#btn-home{top:12px;left:12px}
#btn-fs{top:52px;left:12px}

/* ── LEGEND ── */
#legend{
  position:absolute;bottom:24px;left:12px;
  background:rgba(255,255,255,.95);backdrop-filter:blur(8px);
  border:1px solid var(--gray-200);border-radius:var(--radius);
  padding:11px 13px;z-index:900;box-shadow:var(--shadow-sm);
}
.leg-title{font-size:10px;font-weight:700;letter-spacing:.8px;color:var(--gray-400);text-transform:uppercase;margin-bottom:7px}
.leg-item{display:flex;align-items:center;gap:7px;font-size:11.5px;color:var(--gray-600);margin-bottom:4px}
.leg-item:last-child{margin:0}
.leg-dot{width:11px;height:11px;border-radius:50%;border:2px solid rgba(255,255,255,.9);box-shadow:0 1px 3px rgba(0,0,0,.2)}

/* ── LOADING ── */
#loading{
  position:fixed;inset:0;background:rgba(255,255,255,.88);
  backdrop-filter:blur(4px);z-index:9000;
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:13px;
}
#loading.gone{display:none}
.ld-spinner{width:42px;height:42px;border:3px solid var(--gray-200);border-top-color:var(--green);border-radius:50%;animation:spin .7s linear infinite}
.ld-text{font-size:13px;color:var(--gray-500);font-weight:500}
@keyframes spin{to{transform:rotate(360deg)}}

/* Cluster */
.clu{border-radius:50%;background:var(--green);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;border:2.5px solid #fff;box-shadow:0 2px 8px rgba(22,163,127,.45)}
@keyframes pulse{0%{box-shadow:0 0 0 0 rgba(22,163,127,.6)}70%{box-shadow:0 0 0 10px rgba(22,163,127,0)}100%{box-shadow:0 0 0 0 rgba(22,163,127,0)}}

/* ── XÃ LABEL (permanent, hiện khi zoom>=11) ── */
.xa-label{
  background:rgba(255,255,255,.88);backdrop-filter:blur(4px);
  color:#4f46e5;font-size:10px;font-weight:600;
  padding:2px 7px;border-radius:10px;white-space:nowrap;
  border:1px solid rgba(99,102,241,.25);
  box-shadow:0 1px 4px rgba(0,0,0,.1);
  display:flex;align-items:center;gap:4px;
  font-family:'Inter',sans-serif;
  pointer-events:none;
}
.xa-lbl-dot{
  background:#16a37f;color:#fff;
  border-radius:8px;font-size:9px;font-weight:700;
  padding:0 4px;min-width:14px;text-align:center;
}

/* ── XÃ TOOLTIP (hover) ── */
.xa-tooltip{min-width:160px;font-family:'Inter',sans-serif}
.xa-tt-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:4px}
.xa-tt-name{font-size:13px;font-weight:700;color:#1f2937}
.xa-tt-badge{
  font-size:10px;font-weight:600;padding:2px 7px;border-radius:10px;
  background:var(--green-lite,#e6f7f2);color:#0d7a5f;white-space:nowrap;flex-shrink:0;
}
.xa-tt-badge.hl{background:#e6f7f2;color:#0d7a5f}
.xa-tt-old{
  font-size:11px;color:#6b7280;
  border-top:1px solid #f3f4f6;padding-top:5px;margin-top:2px;
  display:flex;align-items:flex-start;gap:5px;line-height:1.4;
}
.xa-tt-old i{color:#9ca3af;font-size:10px;margin-top:2px;flex-shrink:0}
/* Override leaflet tooltip style */
.leaflet-tooltip{
  padding:8px 11px!important;border-radius:10px!important;
  border:1px solid #e5e7eb!important;
  box-shadow:0 4px 12px rgba(0,0,0,.1)!important;
  background:#fff!important;
}

/* ══════════════════════════════════
   MOBILE STYLES  (max-width: 768px)
   ══════════════════════════════════ */
@media(max-width:768px){
  :root{--top-h:50px;--bot-h:58px}

  /* Topbar mobile */
  .tb-ticker{display:none}
  .tb-sep{display:none}
  #mob-filter-btn{display:flex}

  /* Map full screen */
  #map-wrap{left:0!important;right:0!important;bottom:var(--bot-h)}

  /* Sidebar → bottom sheet */
  #sidebar{
    top:auto;bottom:var(--bot-h);left:0;right:0;
    width:100%;height:0;
    border-right:none;border-top:1px solid var(--gray-200);
    border-radius:20px 20px 0 0;
    box-shadow:0 -4px 20px rgba(0,0,0,.1);
    transform:translateY(100%);overflow:hidden;
    transition:height .3s cubic-bezier(.4,0,.2,1),transform .3s cubic-bezier(.4,0,.2,1);
  }
  #sidebar.mob-open{
    height:75vh;transform:translateY(0);overflow-y:auto;
  }
  /* Drag handle */
  #sidebar::before{
    content:'';display:block;
    width:40px;height:4px;border-radius:2px;
    background:var(--gray-300);margin:10px auto 6px;flex-shrink:0;
  }

  /* Detail panel → full bottom sheet */
  #detail{
    top:auto;bottom:var(--bot-h);left:0;right:0;
    width:100%;height:0;
    border-left:none;border-top:1px solid var(--gray-200);
    border-radius:20px 20px 0 0;
    box-shadow:0 -4px 20px rgba(0,0,0,.12);
    transform:translateY(100%);overflow:hidden;
  }
  #detail.open{
    height:78vh;transform:translateY(0);overflow-y:auto;
  }
  #detail::before{
    content:'';display:block;
    width:40px;height:4px;border-radius:2px;
    background:var(--gray-300);margin:10px auto 0;
  }
  #map-wrap.panel-open{right:0!important;bottom:var(--bot-h)}

  /* Detail hero nhỏ hơn */
  .dt-hero{height:110px}
  .dt-hero-icon{width:50px;height:50px;bottom:-14px;left:14px}
  .dt-hero-icon i{font-size:20px}
  .dt-meta{padding:22px 16px 12px}
  .dt-name{font-size:15px}

  /* Legend nhỏ hơn */
  #legend{bottom:12px;left:8px;padding:8px 10px}
  .leg-title{font-size:9px}
  .leg-item{font-size:11px}

  /* FAB nhỏ hơn */
  .map-fab{width:32px;height:32px;font-size:12px}
  #btn-home{top:10px;left:10px}
  #btn-fs{display:none}

  /* Bottom nav bar */
  #bot-nav{display:flex}
}

/* ── BOTTOM NAV (mobile only) ── */
#bot-nav{
  display:none;
  position:fixed;bottom:0;left:0;right:0;height:var(--bot-h);
  background:#fff;border-top:1px solid var(--gray-200);
  z-index:1200;align-items:center;
  padding:0 8px;gap:4px;
}
.bn-btn{
  flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:3px;height:100%;cursor:pointer;border:none;background:none;
  color:var(--gray-400);font-family:inherit;padding:4px 0;
  transition:color .18s;border-radius:10px;
}
.bn-btn i{font-size:18px}
.bn-btn span{font-size:10px;font-weight:500}
.bn-btn.active{color:var(--green)}
.bn-badge{
  position:absolute;top:-2px;right:-4px;
  background:var(--red);color:#fff;border-radius:10px;
  font-size:9px;font-weight:700;padding:1px 5px;
  border:1.5px solid #fff;
}
</style>
</head>
<body>

<!-- LOADING -->
<div id="loading">
  <div class="ld-spinner"></div>
  <span class="ld-text">Đang tải dữ liệu bản đồ...</span>
</div>

<!-- TOPBAR -->
<div id="topbar">
  <a class="tb-logo" href="#">
    <div class="tb-logo-icon"><i class="fa-solid fa-map-location-dot"></i></div>
    <div class="tb-brand">
      <h1>Bản đồ kết nối</h1>
      <p class="tb-tagline">Nối đúng nguồn – Dùng nơi cần</p>
    </div>
  </a>
  <div class="tb-sep"></div>
  <div id="tb-search">
    <i class="fa fa-magnifying-glass"></i>
    <input id="search-input" type="text" placeholder="Tìm công trình, xã..."/>
  </div>
  <div class="tb-ticker">
    <span class="tb-ticker-badge">LIVE</span>
    <span class="tb-ticker-text" id="ticker-text">Đang tải...</span>
  </div>
  <!-- Mobile filter button -->
  <div id="mob-filter-btn" onclick="toggleMobSidebar()">
    <i class="fa fa-sliders"></i>
    <div id="mob-filter-dot"></div>
  </div>
  <div class="tb-avatar" title="Quản trị">AD</div>
</div>

<!-- SIDEBAR (desktop left / mobile bottom sheet) -->
<div id="sidebar">

  <div class="sb-block">
    <div class="sb-label" style="display:flex;justify-content:space-between;align-items:center">
      Loại công trình
      <span id="type-count" style="font-size:10px;color:var(--green);font-weight:600;background:var(--green-lite);padding:1px 7px;border-radius:10px;letter-spacing:0">4/4</span>
    </div>
    <div class="type-list">
      <button class="type-btn active" data-type="cau" onclick="toggleType(this)">
        <span class="type-dot" style="background:#16a37f"></span>
        <span style="flex:1">Cầu giao thông</span>
        <i class="fa fa-check type-check" style="font-size:11px;color:var(--green)"></i>
      </button>
      <button class="type-btn active" data-type="duong" onclick="toggleType(this)">
        <span class="type-dot" style="background:#3b82f6"></span>
        <span style="flex:1">Đường bê tông</span>
        <i class="fa fa-check type-check" style="font-size:11px;color:var(--green)"></i>
      </button>
      <button class="type-btn active" data-type="truong" onclick="toggleType(this)">
        <span class="type-dot" style="background:#f59e0b"></span>
        <span style="flex:1">Trường học</span>
        <i class="fa fa-check type-check" style="font-size:11px;color:var(--green)"></i>
      </button>
      <button class="type-btn active" data-type="nuoc" onclick="toggleType(this)">
        <span class="type-dot" style="background:#8b5cf6"></span>
        <span style="flex:1">Nước sạch</span>
        <i class="fa fa-check type-check" style="font-size:11px;color:var(--green)"></i>
      </button>
    </div>
  </div>

  <div class="sb-block">
    <div class="sb-label">Mức độ cấp thiết</div>
    <div class="chip-row">
      <span class="chip c-red"   data-p="khan_cap"    onclick="toggleChip(this)">Khẩn cấp</span>
      <span class="chip c-amber" data-p="cao"         onclick="toggleChip(this)">Cao</span>
      <span class="chip c-green" data-p="binh_thuong" onclick="toggleChip(this)">Thường</span>
    </div>
  </div>

  <div class="sb-block">
    <div class="sb-label">Địa điểm</div>
    <div class="filter-row">
      <label>Phường / Xã</label>
      <select id="filter-xa" onchange="applyFilters()">
        <option value="">Tất cả phường/xã</option>
      </select>
    </div>
    <div class="filter-row">
      <label>Năm đầu tư</label>
      <select id="filter-nam" onchange="applyFilters()">
        <option value="">Tất cả năm</option>
        <option>2025</option><option>2026</option><option>2027</option>
        <option>2028</option><option>2029</option><option>2030</option>
      </select>
    </div>
  </div>

  <div class="sb-block">
    <div class="sb-label">Lớp bản đồ</div>
    <div class="layer-row">
      <span class="layer-label"><i class="fa fa-draw-polygon" style="color:var(--green)"></i>Ranh tỉnh</span>
      <div class="toggle-sw on" id="tog-ranh" onclick="toggleLayer(this,'ranh')"></div>
    </div>
    <div class="layer-row">
      <span class="layer-label"><i class="fa fa-map" style="color:#3b82f6"></i>Ranh xã</span>
      <div class="toggle-sw" id="tog-xa" onclick="toggleLayer(this,'xa')"></div>
    </div>
  </div>

  <div id="result-bar">Hiển thị <strong id="rc">0</strong> công trình</div>

  <div class="sb-block">
    <div class="sb-label">Thống kê</div>
    <div class="stat-row"><span class="stat-k">Tổng</span><span class="stat-v" id="st-t">–</span></div>
    <div class="stat-row"><span class="stat-k">Hoàn thành</span><span class="stat-v g" id="st-h">–</span></div>
    <div class="stat-row"><span class="stat-k">Đang thi công</span><span class="stat-v o" id="st-d">–</span></div>
    <div class="stat-row"><span class="stat-k">Chờ đầu tư</span><span class="stat-v" id="st-c">–</span></div>
    <div class="stat-row"><span class="stat-k">Khẩn cấp</span><span class="stat-v r" id="st-k">–</span></div>
  </div>

</div>

<!-- MAP -->
<div id="map-wrap">
  <div id="map"></div>
  <div class="map-fab" id="btn-home" title="Về Đồng Tháp" onclick="map.flyTo([10.58,105.63],10,{duration:1.2})">
    <i class="fa fa-house"></i>
  </div>
  <div class="map-fab" id="btn-fs" title="Toàn màn hình" onclick="toggleFS()">
    <i class="fa fa-expand" id="fs-icon"></i>
  </div>
  <div id="legend">
    <div class="leg-title">Mức ưu tiên</div>
    <div class="leg-item"><div class="leg-dot" style="background:#ef4444"></div>Khẩn cấp</div>
    <div class="leg-item"><div class="leg-dot" style="background:#f59e0b"></div>Ưu tiên cao</div>
    <div class="leg-item"><div class="leg-dot" style="background:#16a37f"></div>Bình thường</div>
  </div>
</div>

<!-- DETAIL PANEL -->
<div id="detail">
  <div class="dt-header">
    <div class="dt-hero" id="dt-hero">
      <div class="dt-hero-pattern"></div>
      <div class="dt-hero-icon"><i class="fa-solid fa-bridge"></i></div>
      <button class="dt-close" onclick="closeDetail()"><i class="fa fa-xmark"></i></button>
    </div>
    <div class="dt-meta">
      <div class="dt-badge" id="dt-badge"></div>
      <div class="dt-name" id="dt-name"></div>
      <div class="dt-loc"><i class="fa fa-location-dot"></i><span id="dt-loc"></span></div>
    </div>
  </div>
  <div class="dt-section">
    <div class="dt-sec-title">Thông số kỹ thuật</div>
    <div class="dt-grid">
      <div class="dt-kv"><div class="k">Chiều dài</div><div class="v" id="dt-dai">–</div></div>
      <div class="dt-kv"><div class="k">Chiều rộng</div><div class="v" id="dt-rong">–</div></div>
      <div class="dt-kv"><div class="k">Tải trọng</div><div class="v" id="dt-tai">–</div></div>
      <div class="dt-kv"><div class="k">Năm đầu tư</div><div class="v" id="dt-nam">–</div></div>
      <div class="dt-kv"><div class="k">Mã CT</div><div class="v" id="dt-ma" style="font-size:12px">–</div></div>
      <div class="dt-kv"><div class="k">Trạng thái</div><div class="v" id="dt-tt" style="font-size:12px">–</div></div>
    </div>
  </div>
  <div class="dt-progress-wrap">
    <div class="dt-prog-head">
      <span class="dt-prog-label">Tiến độ thực hiện</span>
      <span class="dt-prog-pct" id="dt-pct">0%</span>
    </div>
    <div class="dt-prog-track"><div class="dt-prog-fill" id="dt-fill" style="width:0%"></div></div>
    <div class="dt-prog-steps">
      <div class="dt-prog-step" id="step-0">Chờ đầu tư</div>
      <div class="dt-prog-step" id="step-1">Thi công</div>
      <div class="dt-prog-step" id="step-2">Hoàn thành</div>
    </div>
  </div>
  <div id="dt-contact-wrap" class="dt-section" style="border:none">
    <div class="dt-sec-title">Đầu mối liên hệ</div>
    <div class="dt-contact">
      <div class="dt-contact-top">
        <div class="dt-contact-av" id="dt-av">?</div>
        <div>
          <div class="dt-contact-name" id="dt-cname">–</div>
          <div class="dt-contact-role" id="dt-crole">–</div>
        </div>
      </div>
      <div class="dt-contact-phone" id="dt-cphone">
        <i class="fa fa-phone"></i><span id="dt-csdt">–</span>
      </div>
    </div>
  </div>

  <!-- Action buttons -->
  <div style="padding:0 18px 18px;display:flex;gap:8px">
    <button onclick="locateMarker()"
      style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;
             padding:9px;border-radius:9px;border:1.5px solid var(--gray-200);
             background:var(--gray-50);color:var(--gray-600);font-size:12.5px;
             font-weight:500;cursor:pointer;font-family:inherit;transition:all .18s"
      onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green-dark)';this.style.background='var(--green-lite)'"
      onmouseout="this.style.borderColor='var(--gray-200)';this.style.color='var(--gray-600)';this.style.background='var(--gray-50)'">
      <i class="fa fa-crosshairs"></i> Định vị
    </button>
    <a id="btn-edit" href="#"
      target="_blank"
      style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;
             padding:9px;border-radius:9px;border:1.5px solid var(--green);
             background:var(--green-lite);color:var(--green-dark);font-size:12.5px;
             font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s"
      onmouseover="this.style.background='var(--green)';this.style.color='#fff'"
      onmouseout="this.style.background='var(--green-lite)';this.style.color='var(--green-dark)'">
      <i class="fa fa-eye"></i> Xem chi tiết
    </a>
  </div>
</div>

<!-- BOTTOM NAV (mobile only) -->
<div id="bot-nav">
  <button class="bn-btn active" id="bn-map" onclick="botNav('map')">
    <i class="fa fa-map"></i><span>Bản đồ</span>
  </button>
  <button class="bn-btn" id="bn-filter" onclick="botNav('filter')" style="position:relative">
    <i class="fa fa-sliders"></i><span>Lọc</span>
    <div class="bn-badge" id="bn-badge" style="display:none">!</div>
  </button>
  <button class="bn-btn" id="bn-stats" onclick="botNav('stats')">
    <i class="fa fa-chart-bar"></i><span>Thống kê</span>
  </button>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
const API={ct:'/api/congtrinh',tk:'/api/thongke',ranh:'/api/ranh-tinh',xa:'/api/phuong-xa',dsx:'/api/danh-sach-xa',hl:'/api/xa-highlight'};
const isMob=()=>window.innerWidth<=768;

/* ── MAP ── */
const map=L.map('map',{zoomControl:false}).setView([10.58,105.63],10);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{
  attribution:'© OpenStreetMap © CARTO',subdomains:'abcd',maxZoom:19,
}).addTo(map);
L.control.zoom({position:'bottomright'}).addTo(map);

/* ── LAYERS ── */
let lRanh=null,lXa=null,lHighlight=null,selMarker=null;
let xaLayerLoaded=false;
const cluster=L.markerClusterGroup({
  iconCreateFunction(c){
    const n=c.getChildCount(),s=n>50?46:n>10?40:34;
    return L.divIcon({html:`<div class="clu" style="width:${s}px;height:${s}px;font-size:${s>40?14:12}px">${n}</div>`,iconSize:[s,s],className:''});
  },
  maxClusterRadius:55,showCoverageOnHover:false,spiderfyOnMaxZoom:true,
});
map.addLayer(cluster);

/* ── STATE ── */
const st={priority:new Set(['khan_cap','cao','binh_thuong']),xa:'',nam:'',q:''};
const activeTypes=new Set(['cau','duong','truong','nuoc']);

/* ── ICONS ── */
const COL={khan_cap:'#ef4444',cao:'#f59e0b',binh_thuong:'#16a37f'};
function mkIcon(p,sel=false){
  const c=COL[p]||'#16a37f',s=sel?26:22;
  const ring=sel?`<div style="position:absolute;inset:-4px;border-radius:50%;border:2px solid ${c};opacity:.5;animation:pulse 1.5s ease infinite"></div>`:'';
  return L.divIcon({
    html:`<div style="position:relative;width:${s}px;height:${s}px">${ring}<div style="width:${s}px;height:${s}px;border-radius:50%;background:${c};border:2.5px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.25);display:flex;align-items:center;justify-content:center"><div style="width:${Math.round(s*.32)}px;height:${Math.round(s*.32)}px;border-radius:50%;background:rgba(255,255,255,.85)"></div></div></div>`,
    iconSize:[s,s],iconAnchor:[s/2,s/2],className:'',
  });
}

/* ── RENDER ── */
function renderMarkers(features){
  cluster.clearLayers();
  features.forEach(f=>{
    const[lng,lat]=f.geometry.coordinates,p=f.properties;
    const m=L.marker([lat,lng],{icon:mkIcon(p.muc_uu_tien)});
    m._props=p;
    m.on('click',()=>onMarkerClick(m,p));
    cluster.addLayer(m);
  });
  document.getElementById('rc').textContent=features.length;
}

function onMarkerClick(m,p){
  if(selMarker&&selMarker!==m) selMarker.setIcon(mkIcon(selMarker._props.muc_uu_tien,false));
  selMarker=m;
  m.setIcon(mkIcon(p.muc_uu_tien,true));
  // Mobile: đóng sidebar trước khi mở detail
  if(isMob()) closeMobSidebar();
  showDetail(p);
  map.panTo(m.getLatLng(),{animate:true,duration:.5});
}

/* ── LOAD DATA ── */
async function loadCT(){
  const p=new URLSearchParams();
  if(st.xa)  p.set('xa',st.xa);
  if(st.nam) p.set('nam',st.nam);
  if(st.q)   p.set('q',st.q);
  if(st.priority.size<3) p.set('priority',[...st.priority].join(','));
  if(activeTypes.size<4) p.set('loai_ct',[...activeTypes].join(','));
  const d=await fetch(`${API.ct}?${p}`).then(r=>r.json());
  renderMarkers(d.features||[]);
}

async function loadTK(){
  const d=await fetch(API.tk).then(r=>r.json());
  const t=d.tong;
  document.getElementById('st-t').textContent=t.tong??'–';
  document.getElementById('st-h').textContent=t.hoan_thanh??'–';
  document.getElementById('st-d').textContent=t.dang_thi_cong??'–';
  document.getElementById('st-c').textContent=t.cho_dau_tu??'–';
  document.getElementById('st-k').textContent=t.khan_cap??'–';
  const hs=d.theo_huyen||[];
  document.getElementById('ticker-text').textContent=
    hs.map(h=>`${h.ten_huyen}: ${h.so_luong} CT`).join('  ·  ')||'WebGIS công trình tình nguyện';
}

async function loadRanh(){
  const d=await fetch(API.ranh).then(r=>r.json());
  lRanh=L.geoJSON(d,{style:{color:'#16a37f',weight:2.5,fillOpacity:0,dashArray:'6,4',opacity:.8}}).addTo(map);
}

/* ── XÃ LAYER với label permanent + tooltip hover ── */
async function loadXaLayer(){
  if(xaLayerLoaded) return;
  xaLayerLoaded=true;
  const d=await fetch(API.xa).then(r=>r.json());

  // Tạo layer polygon xã
  lXa=L.geoJSON(d,{
    style: xaStyle,
    onEachFeature(f,l){
      const p=f.properties;
      // Tooltip hover: hiển thị sap_nhap + ten_xa
      const sapNhap = p.sap_nhap
        ? `<div class="xa-tt-old"><i class="fa fa-code-merge"></i> Sáp nhập từ: ${p.sap_nhap}</div>`
        : '';
      const badge = p.so_ct > 0
        ? `<span class="xa-tt-badge">${p.so_ct} CT</span>`
        : '';
      l.bindTooltip(`
        <div class="xa-tooltip">
          <div class="xa-tt-head">
            <span class="xa-tt-name">${p.ten_xa}</span>
            ${badge}
          </div>
          ${sapNhap}
        </div>
      `, {sticky:true, direction:'top', offset:[0,-4]});

      l.on('mouseover', function(){
        if(!this._isHighlighted) this.setStyle(xaHoverStyle);
      });
      l.on('mouseout', function(){
        if(!this._isHighlighted) this.setStyle(xaStyle(f));
      });
    },
  });

  // Label layer riêng dùng divIcon tại centroid
  const labelGroup=L.layerGroup();
  d.features.forEach(f=>{
    try{
      // Tính centroid thô từ bounds
      const tmp=L.geoJSON(f);
      const c=tmp.getBounds().getCenter();
      const p=f.properties;
      const dot=p.so_ct>0?`<span class="xa-lbl-dot">${p.so_ct}</span>`:'';
      L.marker(c,{
        icon:L.divIcon({
          html:`<div class="xa-label">${p.ten_xa}${dot}</div>`,
          className:'',iconAnchor:[50,10],iconSize:[100,20],
        }),
        interactive:false,
      }).addTo(labelGroup);
    }catch(e){}
  });

  // Nhóm cả polygon + label vào 1 layer group
  lXa._labelGroup=labelGroup;

  // Ẩn/hiện label theo zoom
  map.on('zoomend', updateXaLabels);
}

function xaStyle(f){
  const hasCT = f && f.properties && f.properties.so_ct > 0;
  return {
    color:'#6366f1',weight:.8,
    fillColor: hasCT ? '#6366f1' : '#6366f1',
    fillOpacity: hasCT ? .07 : .03,
  };
}
const xaHoverStyle={color:'#4f46e5',weight:1.5,fillColor:'#6366f1',fillOpacity:.15};
const xaHighlightStyle={color:'#16a37f',weight:2.5,fillColor:'#16a37f',fillOpacity:.18};

function updateXaLabels(){
  if(!lXa||!lXa._labelGroup) return;
  const zoom=map.getZoom();
  if(zoom>=11){
    if(!map.hasLayer(lXa._labelGroup)) map.addLayer(lXa._labelGroup);
  } else {
    if(map.hasLayer(lXa._labelGroup)) map.removeLayer(lXa._labelGroup);
  }
}

/* ── HIGHLIGHT XÃ KHI FILTER ── */
async function highlightXa(ma_xa){
  // Reset highlight cũ
  if(lHighlight){ map.removeLayer(lHighlight); lHighlight=null; }
  if(!ma_xa) return;

  // Reset style tất cả polygon xã
  if(lXa) lXa.eachLayer(l=>{ l._isHighlighted=false; l.setStyle(xaStyle(l.feature)); });

  try{
    const d=await fetch(`${API.hl}?ma_xa=${ma_xa}`).then(r=>r.json());
    if(!d.features||!d.features.length) return;

    lHighlight=L.geoJSON(d,{
      style:xaHighlightStyle,
      onEachFeature(f,l){
        l._isHighlighted=true;
        const p=f.properties;
        const sapNhap=p.sap_nhap
          ?`<div class="xa-tt-old"><i class="fa fa-code-merge"></i> Sáp nhập từ: ${p.sap_nhap}</div>`:'';
        l.bindTooltip(`
          <div class="xa-tooltip">
            <div class="xa-tt-head">
              <span class="xa-tt-name">${p.ten_xa}</span>
              <span class="xa-tt-badge hl">Đang lọc</span>
            </div>
            ${sapNhap}
          </div>
        `,{sticky:true,direction:'top',offset:[0,-4]});
      },
    }).addTo(map);

    // Zoom vào xã được chọn
    map.fitBounds(lHighlight.getBounds(),{padding:[40,40],maxZoom:13});
  }catch(e){console.warn('Highlight error',e)}
}

/* Load danh sách xã vào select */
async function loadDanhSachXa(){
  try{
    const list=await fetch(API.dsx).then(r=>r.json());
    const sel=document.getElementById('filter-xa');
    list.forEach(item=>{
      const o=document.createElement('option');
      // item có thể là {value, label} hoặc string
      if(typeof item==='object'){
        o.value=item.value||''; o.textContent=item.label||item.value;
      } else {
        o.value=item; o.textContent=item;
      }
      sel.appendChild(o);
    });
  }catch(e){console.warn('Không tải được danh sách xã',e)}
}

/* ── DETAIL ── */
const BADGE={
  khan_cap:  {bg:'#fef2f2',c:'#b91c1c',dot:'#ef4444',label:'Khẩn cấp',grad:'135deg,#7f1d1d,#ef4444'},
  cao:       {bg:'#fffbeb',c:'#92400e',dot:'#f59e0b',label:'Ưu tiên cao',grad:'135deg,#78350f,#f59e0b'},
  binh_thuong:{bg:'#f0fdf9',c:'#065f46',dot:'#16a37f',label:'Bình thường',grad:'135deg,#0d7a5f,#34d399'},
};
const ST_LABEL={cho_dau_tu:'Chờ đầu tư',dang_thi_cong:'Đang thi công',hoan_thanh:'Hoàn thành'};

function showDetail(p){
  const b=BADGE[p.muc_uu_tien]||BADGE.binh_thuong;
  document.getElementById('dt-hero').style.background=`linear-gradient(${b.grad})`;
  const badge=document.getElementById('dt-badge');
  badge.style.background=b.bg;badge.style.color=b.c;
  badge.innerHTML=`<span style="width:7px;height:7px;border-radius:50%;background:${b.dot};display:inline-block"></span>${b.label}`;
  document.getElementById('dt-name').textContent=p.ten_ct;
  document.getElementById('dt-loc').textContent=`${p.ten_xa}, ${p.ten_huyen}`;
  document.getElementById('dt-dai').innerHTML=p.chieu_dai?`${p.chieu_dai}<span class="u"> m</span>`:'–';
  document.getElementById('dt-rong').innerHTML=p.chieu_rong?`${p.chieu_rong}<span class="u"> m</span>`:'–';
  document.getElementById('dt-tai').innerHTML=p.tai_trong?`${p.tai_trong}<span class="u"> tấn</span>`:'–';
  document.getElementById('dt-nam').textContent=p.nam_dau_tu||'–';
  document.getElementById('dt-ma').textContent=p.ma_ct||'–';
  document.getElementById('dt-tt').textContent=ST_LABEL[p.trang_thai]||p.trang_thai||'–';
  const pct=p.tien_do||0;
  const fill=document.getElementById('dt-fill');
  fill.style.width=pct+'%';
  fill.className='dt-prog-fill '+(pct===100?'done':pct>0?'active':'waiting');
  document.getElementById('dt-pct').textContent=pct+'%';
  document.getElementById('dt-pct').style.color=pct===100?'#16a37f':pct>0?'#f59e0b':'#9ca3af';
  ['step-0','step-1','step-2'].forEach((id,i)=>{
    document.getElementById(id).classList.toggle('done-step',pct>=[0,1,100][i]);
  });
  const cw=document.getElementById('dt-contact-wrap');
  if(p.lien_he){
    cw.style.display='block';
    const ini=p.lien_he.split(' ').slice(-2).map(w=>w[0]).join('').toUpperCase();
    document.getElementById('dt-av').textContent=ini;
    document.getElementById('dt-cname').textContent=p.lien_he;
    document.getElementById('dt-crole').textContent=p.chuc_vu||'–';
    document.getElementById('dt-csdt').textContent=p.sdt||'Chưa có';
    document.getElementById('dt-cphone').style.display=p.sdt?'flex':'none';
  }else{cw.style.display='none'}
  document.getElementById('detail').classList.add('open');
  if(!isMob()) document.getElementById('map-wrap').classList.add('panel-open');
  setTimeout(()=>map.invalidateSize(),350);

  // Nút định vị
  window._currentProps = p;

  // Nút xem chi tiết → mở trang admin view trong tab mới
  const adminUrl = 'http://admin.bandoketnoi.local/cong-trinh/xem/' + p.id;
  document.getElementById('btn-edit').href = adminUrl;
}

function closeDetail(){
  document.getElementById('detail').classList.remove('open');
  document.getElementById('map-wrap').classList.remove('panel-open');
  if(selMarker){selMarker.setIcon(mkIcon(selMarker._props.muc_uu_tien,false));selMarker=null}
  setTimeout(()=>map.invalidateSize(),350);
}

function locateMarker(){
  const p = window._currentProps;
  if(!p) return;
  const [lng,lat] = [0,0]; // fallback
  // Tìm marker đang chọn
  if(selMarker){
    const ll = selMarker.getLatLng();
    map.flyTo(ll, 15, {duration:1.2});
    // Flash effect
    const el = selMarker.getElement();
    if(el){ el.style.transition='transform .2s'; el.style.transform='scale(1.8)'; setTimeout(()=>el.style.transform='scale(1)',400); }
  }
}

/* ── FILTERS ── */
function applyFilters(){
  st.xa=document.getElementById('filter-xa').value;
  st.nam=document.getElementById('filter-nam').value;
  loadCT();
  // Highlight xã nếu đang bật layer xã
  if(st.xa && lXa && map.hasLayer(lXa)){
    highlightXa(st.xa);
  } else if(!st.xa && lHighlight){
    map.removeLayer(lHighlight);
    lHighlight=null;
    if(lXa) lXa.eachLayer(l=>{l._isHighlighted=false;l.setStyle(xaStyle(l.feature))});
  }
  if(isMob()) closeMobSidebar();
}

window.toggleChip=function(el){
  const p=el.dataset.p;
  if(st.priority.has(p)){if(st.priority.size>1){st.priority.delete(p);el.style.opacity='.4'}}
  else{st.priority.add(p);el.style.opacity='1'}
  loadCT();
};

function updateTypeCount(){
  const cnt=document.getElementById('type-count');
  if(cnt) cnt.textContent=activeTypes.size+'/'+document.querySelectorAll('.type-btn').length;
}
window.toggleType=function(el){
  const type=el.dataset.type,check=el.querySelector('.type-check');
  if(activeTypes.has(type)){
    if(activeTypes.size<=1) return;
    activeTypes.delete(type);el.classList.remove('active');
    if(check) check.style.opacity='0';
  }else{
    activeTypes.add(type);el.classList.add('active');
    if(check) check.style.opacity='1';
  }
  updateTypeCount();loadCT();
};

window.toggleLayer=function(el,name){
  el.classList.toggle('on');
  const on=el.classList.contains('on');
  if(name==='ranh'){
    if(on&&lRanh) map.addLayer(lRanh);
    else if(lRanh) map.removeLayer(lRanh);
  } else {
    if(on){
      if(!xaLayerLoaded){
        loadXaLayer().then(()=>{
          if(lXa){ map.addLayer(lXa); updateXaLabels(); }
          // Nếu đang có filter xã thì highlight luôn
          if(st.xa) highlightXa(st.xa);
        });
      } else {
        if(lXa){ map.addLayer(lXa); updateXaLabels(); }
        if(st.xa) highlightXa(st.xa);
      }
    } else {
      if(lXa) map.removeLayer(lXa);
      if(lXa&&lXa._labelGroup) map.removeLayer(lXa._labelGroup);
      if(lHighlight){ map.removeLayer(lHighlight); lHighlight=null; }
    }
  }
};

/* ── MOBILE NAV ── */
function toggleMobSidebar(){
  const sb=document.getElementById('sidebar');
  sb.classList.toggle('mob-open');
  // Đóng detail nếu đang mở
  if(sb.classList.contains('mob-open')) closeDetail();
}
function closeMobSidebar(){
  document.getElementById('sidebar').classList.remove('mob-open');
}

window.botNav=function(tab){
  document.querySelectorAll('.bn-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('bn-'+tab).classList.add('active');
  if(tab==='map'){closeMobSidebar();closeDetail()}
  else if(tab==='filter'){
    closeDetail();
    document.getElementById('sidebar').classList.add('mob-open');
  }else if(tab==='stats'){
    closeMobSidebar();closeDetail();
    // Scroll sidebar đến thống kê rồi mở
    const sb=document.getElementById('sidebar');
    sb.classList.add('mob-open');
    setTimeout(()=>{
      const st_el=document.getElementById('st-t');
      if(st_el) st_el.closest('.sb-block').scrollIntoView({behavior:'smooth'});
    },350);
  }
};

/* ── SEARCH ── */
let stimer=null;
document.getElementById('search-input').addEventListener('input',function(){
  clearTimeout(stimer);st.q=this.value.trim();stimer=setTimeout(loadCT,320);
});

/* ── FULLSCREEN ── */
function toggleFS(){
  const ico=document.getElementById('fs-icon');
  if(!document.fullscreenElement){document.documentElement.requestFullscreen();ico.className='fa fa-compress'}
  else{document.exitFullscreen();ico.className='fa fa-expand'}
}

/* ── CLOSE sidebar khi tap ngoài (mobile) ── */
document.getElementById('map').addEventListener('click',()=>{
  if(isMob()) closeMobSidebar();
});

/* ── INIT ── */
(async()=>{
  try{
    await Promise.all([loadRanh(),loadDanhSachXa(),loadTK()]);
    await loadCT();
  }finally{
    document.getElementById('loading').classList.add('gone');
  }
})();
</script>
</body>
</html>