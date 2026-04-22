<?php
/** @var yii\web\View $this */
$this->title = 'Bản đồ kết nối – ĐHQG TP.HCM';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $this->title ?></title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --green:      #16a37f;
  --green-dark: #0d7a5f;
  --green-lite: #e6f7f2;
  --orange:     #f59e0b;
  --red:        #ef4444;
  --gray-50:    #f9fafb;
  --gray-100:   #f3f4f6;
  --gray-200:   #e5e7eb;
  --gray-400:   #9ca3af;
  --gray-600:   #4b5563;
  --gray-800:   #1f2937;
  --gray-900:   #111827;
  --shadow-sm:  0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.06);
  --shadow-md:  0 4px 16px rgba(0,0,0,.10),0 2px 6px rgba(0,0,0,.07);
  --shadow-lg:  0 20px 40px rgba(0,0,0,.14),0 8px 16px rgba(0,0,0,.10);
  --radius:     12px;
  --radius-sm:  8px;
  --panel-w:    340px;
  --top-h:      56px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;font-family:'Inter',sans-serif;font-size:14px;overflow:hidden}

/* ── TOPBAR ── */
#topbar{
  position:fixed;top:0;left:0;right:0;height:var(--top-h);
  background:rgba(255,255,255,.96);backdrop-filter:blur(12px);
  border-bottom:1px solid var(--gray-200);
  display:flex;align-items:center;gap:14px;padding:0 16px;
  z-index:1100;
}
.tb-logo{
  display:flex;align-items:center;gap:10px;flex-shrink:0;text-decoration:none;
}
.tb-logo-icon{
  width:36px;height:36px;border-radius:10px;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 2px 8px rgba(22,163,127,.35);
}
.tb-logo-icon i{color:#fff;font-size:15px;}
.tb-brand h1{font-size:15px;font-weight:700;color:var(--gray-900);letter-spacing:-.3px}
.tb-brand p {font-size:11px;color:var(--gray-400);margin-top:1px}
.tb-sep{width:1px;height:28px;background:var(--gray-200);flex-shrink:0}
#tb-search{
  display:flex;align-items:center;gap:8px;
  background:var(--gray-100);border:1.5px solid transparent;
  border-radius:24px;padding:0 14px;height:36px;
  flex:1;max-width:380px;transition:border-color .2s,background .2s;
}
#tb-search:focus-within{background:#fff;border-color:var(--green)}
#tb-search i{color:var(--gray-400);font-size:13px;flex-shrink:0}
#search-input{
  border:none;background:transparent;outline:none;
  font-size:13px;color:var(--gray-800);width:100%;font-family:inherit;
}
#search-input::placeholder{color:var(--gray-400)}
.tb-ticker{
  flex:1;display:flex;align-items:center;gap:8px;overflow:hidden;min-width:0;
}
.tb-ticker-badge{
  font-size:10px;font-weight:700;letter-spacing:.5px;
  background:var(--green-lite);color:var(--green-dark);
  padding:3px 8px;border-radius:6px;flex-shrink:0;white-space:nowrap;
}
.tb-ticker-text{font-size:12px;color:var(--gray-600);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tb-avatar{
  width:34px;height:34px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:12px;font-weight:700;cursor:pointer;
  box-shadow:0 2px 6px rgba(22,163,127,.3);
}

/* ── SIDEBAR LEFT ── */
#sidebar{
  position:fixed;top:var(--top-h);left:0;bottom:0;
  width:256px;background:#fff;border-right:1px solid var(--gray-200);
  overflow-y:auto;overflow-x:hidden;z-index:1000;
  scrollbar-width:thin;scrollbar-color:var(--gray-200) transparent;
}
#sidebar::-webkit-scrollbar{width:4px}
#sidebar::-webkit-scrollbar-thumb{background:var(--gray-200);border-radius:4px}

.sb-block{padding:14px 16px;border-bottom:1px solid var(--gray-100)}
.sb-label{
  font-size:10px;font-weight:700;letter-spacing:.8px;
  color:var(--gray-400);text-transform:uppercase;margin-bottom:10px;
}

/* Type pills */
.type-list{display:flex;flex-direction:column;gap:4px}
.type-btn{
  display:flex;align-items:center;gap:9px;
  padding:8px 11px;border-radius:var(--radius-sm);
  border:1.5px solid var(--gray-200);background:var(--gray-50);
  color:var(--gray-600);font-size:12.5px;font-weight:500;
  cursor:pointer;transition:all .18s;text-align:left;width:100%;
  font-family:inherit;
}
.type-btn:hover{background:var(--gray-100)}
.type-btn.active{
  background:var(--green-lite);border-color:var(--green);
  color:var(--green-dark);
}
.type-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}

/* Priority chips */
.chip-row{display:flex;gap:6px;flex-wrap:wrap}
.chip{
  font-size:11.5px;font-weight:500;padding:5px 12px;border-radius:20px;
  border:1.5px solid var(--gray-200);background:var(--gray-50);
  color:var(--gray-500);cursor:pointer;transition:all .18s;user-select:none;
}
.chip.c-red   {background:#fef2f2;border-color:#fca5a5;color:#b91c1c}
.chip.c-amber {background:#fffbeb;border-color:#fcd34d;color:#92400e}
.chip.c-green {background:var(--green-lite);border-color:#6ee7d0;color:var(--green-dark)}

/* Selects */
.filter-row{margin-bottom:8px}
.filter-row label{font-size:11px;color:var(--gray-500);display:block;margin-bottom:4px;font-weight:500}
.filter-row select{
  width:100%;padding:7px 10px;border:1.5px solid var(--gray-200);
  border-radius:var(--radius-sm);background:var(--gray-50);
  color:var(--gray-800);font-size:12.5px;outline:none;cursor:pointer;
  font-family:inherit;transition:border-color .2s;
}
.filter-row select:focus{border-color:var(--green)}

/* Layer toggles */
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

/* Stats */
.stat-row{
  display:flex;justify-content:space-between;align-items:center;
  padding:5px 0;border-bottom:1px solid var(--gray-100);
}
.stat-row:last-child{border:none}
.stat-k{font-size:12px;color:var(--gray-500)}
.stat-v{font-size:13px;font-weight:600;color:var(--gray-800)}
.stat-v.g{color:var(--green)}
.stat-v.o{color:var(--orange)}
.stat-v.r{color:var(--red)}

/* Result bar */
#result-bar{
  padding:8px 16px;font-size:11.5px;
  color:var(--gray-500);background:var(--gray-50);
  border-bottom:1px solid var(--gray-100);
}
#result-bar strong{color:var(--green);font-weight:600}

/* ── MAP ── */
#map-wrap{
  position:fixed;
  top:var(--top-h);left:256px;right:0;bottom:0;
  z-index:0;
}
#map{width:100%;height:100%}

/* ── DETAIL PANEL (slide-in right) ── */
#detail{
  position:fixed;top:var(--top-h);right:0;bottom:0;
  width:var(--panel-w);background:#fff;
  border-left:1px solid var(--gray-200);
  box-shadow:var(--shadow-lg);
  z-index:1050;overflow-y:auto;
  transform:translateX(100%);
  transition:transform .32s cubic-bezier(.4,0,.2,1);
}
#detail.open{transform:translateX(0)}

/* Detail header */
.dt-header{
  position:sticky;top:0;background:#fff;z-index:10;
  border-bottom:1px solid var(--gray-100);
}
.dt-hero{
  height:140px;position:relative;overflow:hidden;
  background:linear-gradient(135deg,#0d7a5f 0%,#16a37f 50%,#34d399 100%);
}
.dt-hero-pattern{
  position:absolute;inset:0;opacity:.12;
  background-image:radial-gradient(circle at 20% 50%,#fff 1px,transparent 1px),
                   radial-gradient(circle at 80% 20%,#fff 1px,transparent 1px);
  background-size:30px 30px;
}
.dt-hero-icon{
  position:absolute;bottom:-20px;left:20px;
  width:64px;height:64px;border-radius:16px;
  background:#fff;display:flex;align-items:center;justify-content:center;
  box-shadow:var(--shadow-md);
}
.dt-hero-icon i{font-size:26px;color:var(--green)}
.dt-close{
  position:absolute;top:10px;right:10px;
  width:32px;height:32px;border-radius:8px;
  background:rgba(255,255,255,.2);backdrop-filter:blur(8px);
  border:none;cursor:pointer;color:#fff;font-size:14px;
  display:flex;align-items:center;justify-content:center;
  transition:background .18s;
}
.dt-close:hover{background:rgba(255,255,255,.35)}
.dt-meta{padding:28px 20px 16px}
.dt-badge{
  display:inline-flex;align-items:center;gap:5px;
  font-size:10.5px;font-weight:700;letter-spacing:.4px;
  padding:3px 10px;border-radius:20px;margin-bottom:10px;
  text-transform:uppercase;
}
.dt-name{
  font-size:17px;font-weight:700;color:var(--gray-900);
  line-height:1.35;letter-spacing:-.3px;margin-bottom:6px;
}
.dt-loc{
  font-size:12.5px;color:var(--gray-400);
  display:flex;align-items:center;gap:5px;
}
.dt-loc i{color:var(--green);font-size:12px}

/* Detail sections */
.dt-section{padding:16px 20px;border-bottom:1px solid var(--gray-100)}
.dt-sec-title{
  font-size:10px;font-weight:700;letter-spacing:.8px;
  color:var(--gray-400);text-transform:uppercase;margin-bottom:12px;
}
.dt-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.dt-kv{
  background:var(--gray-50);border-radius:var(--radius-sm);
  padding:10px 12px;border:1px solid var(--gray-100);
}
.dt-kv .k{font-size:10.5px;color:var(--gray-400);margin-bottom:3px;font-weight:500}
.dt-kv .v{font-size:14px;font-weight:700;color:var(--gray-800)}
.dt-kv .u{font-size:11px;color:var(--gray-400);font-weight:400}

/* Progress */
.dt-progress-wrap{padding:0 20px 16px}
.dt-prog-head{
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:8px;
}
.dt-prog-label{font-size:12px;font-weight:600;color:var(--gray-700)}
.dt-prog-pct{font-size:13px;font-weight:700}
.dt-prog-track{
  height:8px;background:var(--gray-100);border-radius:4px;overflow:hidden;
}
.dt-prog-fill{
  height:100%;border-radius:4px;
  transition:width .5s cubic-bezier(.4,0,.2,1);
  background:linear-gradient(90deg,var(--green),#34d399);
}
.dt-prog-fill.waiting{background:var(--gray-300)}
.dt-prog-fill.active {background:linear-gradient(90deg,var(--orange),#fbbf24)}
.dt-prog-fill.done   {background:linear-gradient(90deg,var(--green),#34d399)}
.dt-prog-steps{
  display:flex;justify-content:space-between;margin-top:8px;
}
.dt-prog-step{
  font-size:10px;color:var(--gray-400);text-align:center;flex:1;
  position:relative;
}
.dt-prog-step::before{
  content:'';display:block;
  width:6px;height:6px;border-radius:50%;
  background:var(--gray-300);margin:0 auto 3px;
}
.dt-prog-step.done-step::before{background:var(--green)}

/* Contact card */
.dt-contact{
  margin:0 20px 16px;padding:14px;
  background:linear-gradient(135deg,var(--green-lite),#f0fdf9);
  border:1px solid #a7f3d0;border-radius:var(--radius);
}
.dt-contact-top{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.dt-contact-av{
  width:38px;height:38px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:13px;font-weight:700;
}
.dt-contact-name{font-size:13px;font-weight:600;color:var(--gray-800)}
.dt-contact-role{font-size:11px;color:var(--gray-500);margin-top:1px}
.dt-contact-phone{
  display:flex;align-items:center;gap:8px;
  font-size:12.5px;color:var(--green-dark);font-weight:500;
}
.dt-contact-phone i{font-size:12px}

/* ── LOADING ── */
#loading{
  position:fixed;inset:0;background:rgba(255,255,255,.85);
  backdrop-filter:blur(4px);z-index:9000;
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;
}
#loading.gone{display:none}
.ld-spinner{
  width:44px;height:44px;
  border:3px solid var(--gray-200);border-top-color:var(--green);
  border-radius:50%;animation:spin .7s linear infinite;
}
.ld-text{font-size:13px;color:var(--gray-500);font-weight:500}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── LEGEND ── */
#legend{
  position:absolute;bottom:28px;left:14px;
  background:rgba(255,255,255,.95);backdrop-filter:blur(8px);
  border:1px solid var(--gray-200);border-radius:var(--radius);
  padding:12px 14px;z-index:900;box-shadow:var(--shadow-sm);
}
.leg-title{font-size:10px;font-weight:700;letter-spacing:.8px;color:var(--gray-400);text-transform:uppercase;margin-bottom:8px}
.leg-item{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gray-600);margin-bottom:5px}
.leg-item:last-child{margin:0}
.leg-dot{width:12px;height:12px;border-radius:50%;border:2px solid rgba(255,255,255,.9);box-shadow:0 1px 3px rgba(0,0,0,.2)}

/* ── MAP BTNS ── */
.map-fab{
  position:absolute;z-index:900;
  background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius-sm);
  width:34px;height:34px;display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:var(--gray-600);font-size:13px;
  box-shadow:var(--shadow-sm);transition:all .18s;
}
.map-fab:hover{background:var(--gray-50);color:var(--green);border-color:var(--green)}
#btn-home{top:14px;left:14px}
#btn-fs  {top:54px;left:14px}

/* Cluster */
.clu{
  border-radius:50%;background:var(--green);color:#fff;
  display:flex;align-items:center;justify-content:center;
  font-weight:700;border:2.5px solid #fff;
  box-shadow:0 2px 8px rgba(22,163,127,.45);
}

/* Pulse animation on selected marker */
@keyframes pulse{
  0%  {box-shadow:0 0 0 0 rgba(22,163,127,.6)}
  70% {box-shadow:0 0 0 10px rgba(22,163,127,0)}
  100%{box-shadow:0 0 0 0 rgba(22,163,127,0)}
}
.marker-selected div{animation:pulse 1.5s ease infinite!important}

/* Map adjusts when detail panel open */
#map-wrap.panel-open{right:var(--panel-w)}
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
      <p>Nối đúng nguồn – Dùng nơi cần</p>
    </div>
  </a>
  <div class="tb-sep"></div>
  <div id="tb-search">
    <i class="fa fa-magnifying-glass"></i>
    <input id="search-input" type="text" placeholder="Tìm công trình, xã, huyện..."/>
  </div>
  <div class="tb-ticker">
    <span class="tb-ticker-badge">LIVE</span>
    <span class="tb-ticker-text" id="ticker-text">Đang tải...</span>
  </div>
  <div class="tb-avatar" title="Quản trị">AD</div>
</div>

<!-- SIDEBAR LEFT -->
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
      <label>Huyện / Thành phố</label>
      <select id="filter-huyen" onchange="applyFilters()">
        <option value="">Tất cả huyện</option>
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

  <!-- FAB buttons -->
  <div class="map-fab" id="btn-home" title="Về Đồng Tháp" onclick="map.flyTo([10.58,105.63],10,{duration:1.2})">
    <i class="fa fa-house"></i>
  </div>
  <div class="map-fab" id="btn-fs" title="Toàn màn hình" onclick="toggleFS()">
    <i class="fa fa-expand" id="fs-icon"></i>
  </div>

  <!-- Legend -->
  <div id="legend">
    <div class="leg-title">Mức ưu tiên</div>
    <div class="leg-item"><div class="leg-dot" style="background:#ef4444"></div>Khẩn cấp</div>
    <div class="leg-item"><div class="leg-dot" style="background:#f59e0b"></div>Ưu tiên cao</div>
    <div class="leg-item"><div class="leg-dot" style="background:#16a37f"></div>Bình thường</div>
  </div>
</div>

<!-- DETAIL PANEL (slide-in) -->
<div id="detail">
  <div class="dt-header">
    <div class="dt-hero" id="dt-hero">
      <div class="dt-hero-pattern"></div>
      <div class="dt-hero-icon"><i class="fa-solid fa-bridge" id="dt-icon"></i></div>
      <button class="dt-close" onclick="closeDetail()"><i class="fa fa-xmark"></i></button>
    </div>
    <div class="dt-meta">
      <div class="dt-badge" id="dt-badge"></div>
      <div class="dt-name"  id="dt-name"></div>
      <div class="dt-loc">
        <i class="fa fa-location-dot"></i>
        <span id="dt-loc"></span>
      </div>
    </div>
  </div>

  <!-- Thông số kỹ thuật -->
  <div class="dt-section">
    <div class="dt-sec-title">Thông số kỹ thuật</div>
    <div class="dt-grid">
      <div class="dt-kv"><div class="k">Chiều dài</div><div class="v" id="dt-dai">–<span class="u"></span></div></div>
      <div class="dt-kv"><div class="k">Chiều rộng</div><div class="v" id="dt-rong">–<span class="u"></span></div></div>
      <div class="dt-kv"><div class="k">Tải trọng</div><div class="v" id="dt-tai">–<span class="u"></span></div></div>
      <div class="dt-kv"><div class="k">Năm đầu tư</div><div class="v" id="dt-nam">–</div></div>
      <div class="dt-kv"><div class="k">Mã CT</div><div class="v" id="dt-ma" style="font-size:12px">–</div></div>
      <div class="dt-kv"><div class="k">Trạng thái</div><div class="v" id="dt-tt" style="font-size:12px">–</div></div>
    </div>
  </div>

  <!-- Tiến độ -->
  <div class="dt-progress-wrap" style="padding-top:16px">
    <div class="dt-prog-head">
      <span class="dt-prog-label">Tiến độ thực hiện</span>
      <span class="dt-prog-pct" id="dt-pct">0%</span>
    </div>
    <div class="dt-prog-track">
      <div class="dt-prog-fill" id="dt-fill" style="width:0%"></div>
    </div>
    <div class="dt-prog-steps">
      <div class="dt-prog-step" id="step-0">Chờ đầu tư</div>
      <div class="dt-prog-step" id="step-1">Thi công</div>
      <div class="dt-prog-step" id="step-2">Hoàn thành</div>
    </div>
  </div>

  <!-- Liên hệ -->
  <div id="dt-contact-wrap" class="dt-section" style="border:none">
    <div class="dt-sec-title">Đầu mối liên hệ</div>
    <div class="dt-contact" id="dt-contact">
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
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
const API = {
  ct:    '/api/congtrinh',
  tk:    '/api/thongke',
  ranh:  '/api/ranh-tinh',
  xa:    '/api/phuong-xa',
  huyen: '/api/danh-sach-huyen',
};

/* ── MAP ── */
const map = L.map('map',{zoomControl:false}).setView([10.58,105.63],10);

// CartoDB Positron – clean, minimal basemap
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{
  attribution:'© OpenStreetMap © CARTO',
  subdomains:'abcd',maxZoom:19,
}).addTo(map);

L.control.zoom({position:'bottomright'}).addTo(map);

/* ── LAYERS ── */
let lRanh=null, lXa=null, selMarker=null;
const cluster = L.markerClusterGroup({
  iconCreateFunction(c){
    const n=c.getChildCount();
    const s=n>50?46:n>10?40:34;
    return L.divIcon({
      html:`<div class="clu" style="width:${s}px;height:${s}px;font-size:${s>40?14:12}px">${n}</div>`,
      iconSize:[s,s],className:'',
    });
  },
  maxClusterRadius:55,showCoverageOnHover:false,
  spiderfyOnMaxZoom:true,
});
map.addLayer(cluster);

/* ── STATE ── */
const st = { priority:new Set(['khan_cap','cao','binh_thuong']), huyen:'', nam:'', q:'' };

/* ── ICONS ── */
const COL = {khan_cap:'#ef4444', cao:'#f59e0b', binh_thuong:'#16a37f'};
function mkIcon(p,sel=false){
  const c=COL[p]||'#16a37f';
  const s=sel?26:22;
  const ring=sel?`<div style="position:absolute;inset:-4px;border-radius:50%;border:2px solid ${c};opacity:.5;animation:pulse 1.5s ease infinite"></div>`:'';
  return L.divIcon({
    html:`<div style="position:relative;width:${s}px;height:${s}px">${ring}<div style="width:${s}px;height:${s}px;border-radius:50%;background:${c};border:2.5px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.25);display:flex;align-items:center;justify-content:center"><div style="width:${Math.round(s*.32)}px;height:${Math.round(s*.32)}px;border-radius:50%;background:rgba(255,255,255,.85)"></div></div></div>`,
    iconSize:[s,s],iconAnchor:[s/2,s/2],className:'',
  });
}

/* ── RENDER ── */
let allMarkers=[];
function renderMarkers(features){
  cluster.clearLayers();
  allMarkers=[];
  features.forEach(f=>{
    const [lng,lat]=f.geometry.coordinates;
    const p=f.properties;
    const m=L.marker([lat,lng],{icon:mkIcon(p.muc_uu_tien)});
    m._props=p;
    m.on('click',()=>onMarkerClick(m,p));
    cluster.addLayer(m);
    allMarkers.push(m);
  });
  document.getElementById('rc').textContent=features.length;
}

function onMarkerClick(m,p){
  // Reset prev selected
  if(selMarker&&selMarker!==m){
    selMarker.setIcon(mkIcon(selMarker._props.muc_uu_tien,false));
  }
  selMarker=m;
  m.setIcon(mkIcon(p.muc_uu_tien,true));
  showDetail(p);
  // Pan map to make room
  map.panTo(m.getLatLng(),{animate:true,duration:.5});
}

/* ── LOAD ── */
async function loadCT(){
  const p=new URLSearchParams();
  if(st.huyen) p.set('huyen',st.huyen);
  if(st.nam)   p.set('nam',st.nam);
  if(st.q)     p.set('q',st.q);
  if(st.priority.size<3)   p.set('priority',[...st.priority].join(','));
  if(activeTypes.size < 4) p.set('loai_ct',[...activeTypes].join(','));
  const res=await fetch(`${API.ct}?${p}`);
  const d=await res.json();
  renderMarkers(d.features||[]);
}

async function loadTK(){
  const res=await fetch(API.tk);
  const d=await res.json();
  const t=d.tong;
  document.getElementById('st-t').textContent=t.tong??'–';
  document.getElementById('st-h').textContent=t.hoan_thanh??'–';
  document.getElementById('st-d').textContent=t.dang_thi_cong??'–';
  document.getElementById('st-c').textContent=t.cho_dau_tu??'–';
  document.getElementById('st-k').textContent=t.khan_cap??'–';
  // ticker
  const hs=d.theo_huyen||[];
  document.getElementById('ticker-text').textContent=
    hs.map(h=>`${h.ten_huyen}: ${h.so_luong} công trình`).join('  ·  ')||'Hệ thống WebGIS công trình tình nguyện';
}

async function loadRanh(){
  const res=await fetch(API.ranh);
  const d=await res.json();
  lRanh=L.geoJSON(d,{
    style:{color:'#16a37f',weight:2.5,fillOpacity:0,dashArray:'6,4',opacity:.8},
  }).addTo(map);
}

async function loadXa(){
  const res=await fetch(API.xa);
  const d=await res.json();
  lXa=L.geoJSON(d,{
    style:{color:'#6366f1',weight:.8,fillColor:'#6366f1',fillOpacity:.04},
    onEachFeature(f,l){
      l.bindTooltip(f.properties.ten_xa,{
        permanent:false,direction:'center',
        className:'leaflet-tooltip-plain',
      });
    },
  });
}

async function loadHuyen(){
  const res=await fetch(API.huyen);
  const list=await res.json();
  const sel=document.getElementById('filter-huyen');
  list.forEach(h=>{
    const o=document.createElement('option');
    o.value=h;o.textContent=h;sel.appendChild(o);
  });
}

/* ── DETAIL ── */
const BADGE={
  khan_cap:   {bg:'#fef2f2',c:'#b91c1c',dot:'#ef4444',label:'Khẩn cấp',grad:'135deg,#7f1d1d,#ef4444'},
  cao:        {bg:'#fffbeb',c:'#92400e',dot:'#f59e0b',label:'Ưu tiên cao',grad:'135deg,#78350f,#f59e0b'},
  binh_thuong:{bg:'#f0fdf9',c:'#065f46',dot:'#16a37f',label:'Bình thường',grad:'135deg,#0d7a5f,#34d399'},
};
const ST_LABEL={cho_dau_tu:'Chờ đầu tư',dang_thi_cong:'Đang thi công',hoan_thanh:'Hoàn thành'};

function showDetail(p){
  const b=BADGE[p.muc_uu_tien]||BADGE.binh_thuong;

  // Hero gradient
  document.getElementById('dt-hero').style.background=`linear-gradient(${b.grad})`;

  // Badge
  const badge=document.getElementById('dt-badge');
  badge.style.background=b.bg; badge.style.color=b.c;
  badge.innerHTML=`<span style="width:7px;height:7px;border-radius:50%;background:${b.dot};display:inline-block"></span>${b.label}`;

  document.getElementById('dt-name').textContent=p.ten_ct;
  document.getElementById('dt-loc').textContent=`Xã ${p.ten_xa}, Huyện ${p.ten_huyen}`;

  // Specs
  document.getElementById('dt-dai').innerHTML=p.chieu_dai?`${p.chieu_dai}<span class="u"> m</span>`:'–';
  document.getElementById('dt-rong').innerHTML=p.chieu_rong?`${p.chieu_rong}<span class="u"> m</span>`:'–';
  document.getElementById('dt-tai').innerHTML=p.tai_trong?`${p.tai_trong}<span class="u"> tấn</span>`:'–';
  document.getElementById('dt-nam').textContent=p.nam_dau_tu||'–';
  document.getElementById('dt-ma').textContent=p.ma_ct||'–';
  document.getElementById('dt-tt').textContent=ST_LABEL[p.trang_thai]||p.trang_thai||'–';

  // Progress
  const pct=p.tien_do||0;
  const fill=document.getElementById('dt-fill');
  fill.style.width=pct+'%';
  fill.className='dt-prog-fill '+(pct===100?'done':pct>0?'active':'waiting');
  document.getElementById('dt-pct').textContent=pct+'%';
  document.getElementById('dt-pct').style.color=pct===100?'#16a37f':pct>0?'#f59e0b':'#9ca3af';
  // Steps
  ['step-0','step-1','step-2'].forEach((id,i)=>{
    const el=document.getElementById(id);
    const thresh=[0,1,100];
    el.classList.toggle('done-step', pct>=thresh[i]);
  });

  // Contact
  const cw=document.getElementById('dt-contact-wrap');
  if(p.lien_he){
    cw.style.display='block';
    const initials=p.lien_he.split(' ').slice(-2).map(w=>w[0]).join('').toUpperCase();
    document.getElementById('dt-av').textContent=initials;
    document.getElementById('dt-cname').textContent=p.lien_he;
    document.getElementById('dt-crole').textContent=p.chuc_vu||'–';
    document.getElementById('dt-csdt').textContent=p.sdt||'Chưa có';
    document.getElementById('dt-cphone').style.display=p.sdt?'flex':'none';
  } else {
    cw.style.display='none';
  }

  // Open panel + shrink map
  document.getElementById('detail').classList.add('open');
  document.getElementById('map-wrap').classList.add('panel-open');
  setTimeout(()=>map.invalidateSize(),350);
}

function closeDetail(){
  document.getElementById('detail').classList.remove('open');
  document.getElementById('map-wrap').classList.remove('panel-open');
  if(selMarker){ selMarker.setIcon(mkIcon(selMarker._props.muc_uu_tien,false)); selMarker=null; }
  setTimeout(()=>map.invalidateSize(),350);
}

/* ── FILTERS ── */
function applyFilters(){
  st.huyen=document.getElementById('filter-huyen').value;
  st.nam=document.getElementById('filter-nam').value;
  loadCT();
}
window.toggleChip=function(el){
  const p=el.dataset.p;
  if(st.priority.has(p)){
    if(st.priority.size>1){st.priority.delete(p);el.style.opacity='.4';}
  } else {
    st.priority.add(p);el.style.opacity='1';
  }
  loadCT();
};
// Multi-select loại công trình
const activeTypes = new Set(['cau','duong','truong','nuoc']);
function updateTypeCount(){
  const total = document.querySelectorAll('.type-btn').length;
  const cnt   = document.getElementById('type-count');
  if(cnt) cnt.textContent = activeTypes.size + '/' + total;
}
window.toggleType = function(el){
  const type = el.dataset.type;
  const check = el.querySelector('.type-check');
  if(activeTypes.has(type)){
    // Không cho bỏ chọn hết — tối thiểu 1
    if(activeTypes.size <= 1) return;
    activeTypes.delete(type);
    el.classList.remove('active');
    if(check) check.style.opacity = '0';
  } else {
    activeTypes.add(type);
    el.classList.add('active');
    if(check) check.style.opacity = '1';
  }
  updateTypeCount();
  loadCT();
};
window.toggleLayer=function(el,name){
  el.classList.toggle('on');
  const on=el.classList.contains('on');
  if(name==='ranh'){
    if(on&&lRanh) map.addLayer(lRanh);
    else if(lRanh) map.removeLayer(lRanh);
  } else {
    if(on){
      if(!lXa) loadXa().then(()=>{if(lXa)map.addLayer(lXa)});
      else map.addLayer(lXa);
    } else if(lXa) map.removeLayer(lXa);
  }
};

/* ── SEARCH ── */
let stimer=null;
document.getElementById('search-input').addEventListener('input',function(){
  clearTimeout(stimer);
  st.q=this.value.trim();
  stimer=setTimeout(loadCT,320);
});

/* ── FULLSCREEN ── */
function toggleFS(){
  const ico=document.getElementById('fs-icon');
  if(!document.fullscreenElement){
    document.documentElement.requestFullscreen();
    ico.className='fa fa-compress';
  } else {
    document.exitFullscreen();
    ico.className='fa fa-expand';
  }
}

/* ── INIT ── */
(async()=>{
  try{
    await Promise.all([loadRanh(),loadHuyen(),loadTK()]);
    await loadCT();
  }finally{
    document.getElementById('loading').classList.add('gone');
  }
})();
</script>
</body>
</html>