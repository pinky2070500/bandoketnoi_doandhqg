<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Dashboard';

$loaiLabel   = ['cau'=>'Cầu giao thông','duong'=>'Đường bê tông','truong'=>'Trường học','nuoc'=>'Nước sạch','khac'=>'Khác'];
$loaiIcon    = ['cau'=>'fa-bridge','duong'=>'fa-road','truong'=>'fa-school','nuoc'=>'fa-droplet','khac'=>'fa-box'];
$statusLabel = ['cho_dau_tu'=>'Chờ đầu tư','dang_thi_cong'=>'Đang thi công','hoan_thanh'=>'Hoàn thành'];
$statusColor = ['cho_dau_tu'=>'#94a3b8','dang_thi_cong'=>'#f59e0b','hoan_thanh'=>'#10b981'];
$statusBg    = ['cho_dau_tu'=>'#f1f5f9','dang_thi_cong'=>'#fffbeb','hoan_thanh'=>'#ecfdf5'];

$pctHT  = $tong['tong'] > 0 ? round($tong['hoan_thanh'] / $tong['tong'] * 100) : 0;
$chuaTD = (int)$tong['tong'] - (int)$tong['co_toa_do'];

// JSON cho charts & modal
$jHuyenLabels = json_encode(array_column($theoHuyen,'ten_huyen'));
$jHuyenTong   = json_encode(array_map('intval',array_column($theoHuyen,'tong')));
$jHuyenHT     = json_encode(array_map('intval',array_column($theoHuyen,'hoan_thanh')));
$jHuyenKC     = json_encode(array_map('intval',array_column($theoHuyen,'khan_cap')));
$jNamLabels   = json_encode(array_map(fn($r)=>(string)$r['nam_dau_tu'],$theoNam));
$jNamTong     = json_encode(array_map('intval',array_column($theoNam,'tong')));
$jNamHT       = json_encode(array_map('intval',array_column($theoNam,'hoan_thanh')));
$jLoaiLabels  = json_encode(array_map(fn($r)=>$loaiLabel[$r['loai_ct']]??$r['loai_ct'],$theoLoai));
$jLoaiData    = json_encode(array_map('intval',array_column($theoLoai,'tong')));
$jLoaiColors  = json_encode(['#10b981','#3b82f6','#f59e0b','#8b5cf6','#94a3b8']);

$urgentJson   = json_encode(array_values($urgent));
$recentJson   = json_encode(array_values($recent));
$huyenJson    = json_encode(array_values($theoHuyen));
$namJson      = json_encode(array_values($theoNam));
$loaiJson     = json_encode(array_values($theoLoai));
$ttJson       = json_encode([
  ['label'=>'Hoàn thành',   'val'=>(int)$tong['hoan_thanh'],    'color'=>'#10b981','status'=>'hoan_thanh'],
  ['label'=>'Đang thi công','val'=>(int)$tong['dang_thi_cong'], 'color'=>'#f59e0b','status'=>'dang_thi_cong'],
  ['label'=>'Chờ đầu tư',  'val'=>(int)$tong['cho_dau_tu'],    'color'=>'#e2e8f0','status'=>'cho_dau_tu'],
]);
?>
<style>
/* ── FONT ── */
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap');

/* ── RESET LAYOUT CONTEXT ── */
#content { font-family:'DM Sans',sans-serif !important }
#content * { box-sizing:border-box }

/* ── TOKENS ── */
:root {
  --c-bg:       #f8fafc;
  --c-surface:  #ffffff;
  --c-border:   #e2e8f0;
  --c-border2:  #f1f5f9;
  --c-text:     #0f172a;
  --c-text2:    #475569;
  --c-text3:    #94a3b8;
  --c-green:    #10b981;
  --c-green2:   #059669;
  --c-green-bg: #ecfdf5;
  --c-blue:     #3b82f6;
  --c-blue-bg:  #eff6ff;
  --c-red:      #ef4444;
  --c-red-bg:   #fef2f2;
  --c-amber:    #f59e0b;
  --c-amber-bg: #fffbeb;
  --c-purple:   #8b5cf6;
  --r:          12px;
  --r-sm:       8px;
  --shadow:     0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
  --shadow-md:  0 4px 16px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.05);
  --shadow-lg:  0 20px 48px rgba(0,0,0,.12),0 8px 16px rgba(0,0,0,.08);
}

/* ── DASHBOARD LAYOUT ── */
.db { display:flex; flex-direction:column; gap:16px; max-width:1280px; padding-bottom:32px }

/* ── SECTION LABEL ── */
.sec-label {
  font-size:10px; font-weight:700; letter-spacing:1.2px;
  text-transform:uppercase; color:var(--c-text3);
  display:flex; align-items:center; gap:8px; margin-bottom:-4px;
}
.sec-label::after { content:''; flex:1; height:1px; background:var(--c-border2) }

/* ── KPI ROW ── */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:12px }
@media(max-width:900px) { .kpi-row { grid-template-columns:1fr 1fr } }
@media(max-width:480px) { .kpi-row { grid-template-columns:1fr } }

.kpi {
  background:var(--c-surface); border:1px solid var(--c-border);
  border-radius:var(--r); padding:18px 20px;
  cursor:pointer; transition:all .2s; position:relative; overflow:hidden;
  box-shadow:var(--shadow);
}
.kpi:hover { box-shadow:var(--shadow-md); transform:translateY(-2px) }
.kpi:active { transform:translateY(0); box-shadow:var(--shadow) }
.kpi-stripe {
  position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:2px 0 0 2px;
}
.kpi-inner { padding-left:12px }
.kpi-label { font-size:11.5px; font-weight:600; color:var(--c-text3); margin-bottom:8px; letter-spacing:.2px }
.kpi-main  { display:flex; align-items:flex-end; gap:10px; margin-bottom:8px }
.kpi-num   {
  font-size:40px; font-weight:800; color:var(--c-text);
  letter-spacing:-2px; line-height:1; font-family:'DM Mono',monospace;
}
.kpi-unit  { font-size:13px; color:var(--c-text3); font-weight:500; padding-bottom:5px }
.kpi-bar-wrap { height:4px; background:var(--c-border2); border-radius:2px; overflow:hidden; margin-bottom:6px }
.kpi-bar    { height:100%; border-radius:2px; transition:width 1.2s cubic-bezier(.4,0,.2,1) }
.kpi-foot  { font-size:11.5px; color:var(--c-text3) }
.kpi-foot strong { font-weight:700 }
.kpi-badge {
  display:inline-flex; align-items:center; gap:4px;
  font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:20px;
}

/* ── GRID 2-COL ── */
.row2 { display:grid; grid-template-columns:1fr 1fr; gap:12px }
.row2-6040 { display:grid; grid-template-columns:3fr 2fr; gap:12px }
@media(max-width:800px) { .row2,.row2-6040 { grid-template-columns:1fr } }

/* ── PANEL ── */
.panel {
  background:var(--c-surface); border:1px solid var(--c-border);
  border-radius:var(--r); overflow:hidden; box-shadow:var(--shadow);
}
.ph {
  padding:14px 18px; border-bottom:1px solid var(--c-border2);
  display:flex; align-items:center; justify-content:space-between; gap:12px;
}
.ph-left { min-width:0 }
.ph-title { font-size:13px; font-weight:700; color:var(--c-text) }
.ph-sub   { font-size:11px; color:var(--c-text3); margin-top:1px }
.ph-link  {
  font-size:11.5px; font-weight:600; color:var(--c-green);
  text-decoration:none; white-space:nowrap; flex-shrink:0;
  display:flex; align-items:center; gap:4px;
  transition:color .15s;
}
.ph-link:hover { color:var(--c-green2) }
.pb { padding:18px }
.pb-0 { padding:0 }

/* ── CHART WRAPPER ── */
.chart-box { position:relative; height:220px }
@media(max-width:600px) { .chart-box { height:180px } }

/* ── STAT DONUT ── */
.donut-wrap { display:flex; align-items:center; gap:20px; padding:18px }
@media(max-width:480px) { .donut-wrap { flex-direction:column; align-items:center } }
.donut-center {
  position:relative; flex-shrink:0; cursor:pointer;
  transition:transform .2s; width:150px; height:150px;
}
.donut-center:hover { transform:scale(1.04) }
.donut-mid {
  position:absolute; inset:0; display:flex; flex-direction:column;
  align-items:center; justify-content:center; pointer-events:none;
}
.donut-pct  { font-size:28px; font-weight:800; font-family:'DM Mono',monospace; color:var(--c-text) }
.donut-hint { font-size:9px; font-weight:700; letter-spacing:.8px; color:var(--c-text3); text-transform:uppercase }
.donut-legend { flex:1; display:flex; flex-direction:column; gap:10px }
.dl-row {
  display:flex; align-items:center; gap:8px; cursor:pointer;
  padding:6px 8px; border-radius:var(--r-sm); transition:background .15s;
}
.dl-row:hover { background:var(--c-bg) }
.dl-dot  { width:9px; height:9px; border-radius:50%; flex-shrink:0 }
.dl-name { font-size:12.5px; color:var(--c-text2); flex:1 }
.dl-num  { font-size:13px; font-weight:700; color:var(--c-text); font-family:'DM Mono',monospace }
.dl-pct  { font-size:11px; color:var(--c-text3); min-width:34px; text-align:right }

/* ── HBAR ── */
.hbar-list { padding:14px 18px; display:flex; flex-direction:column; gap:10px }
.hbar-row  { display:grid; align-items:center; gap:10px; grid-template-columns:80px 1fr 52px }
@media(max-width:480px) { .hbar-row { grid-template-columns:64px 1fr 40px } }
.hbar-lbl  {
  font-size:11.5px; color:var(--c-text2); text-align:right;
  overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-weight:500;
}
.hbar-track {
  height:28px; background:var(--c-border2); border-radius:6px;
  position:relative; overflow:hidden; cursor:pointer; transition:opacity .15s;
}
.hbar-track:hover { opacity:.85 }
.hbar-ht {
  position:absolute; inset:0; border-radius:6px;
  transition:width 1.2s cubic-bezier(.4,0,.2,1);
}
.hbar-fill {
  position:absolute; inset:0; border-radius:6px;
  display:flex; align-items:center; padding:0 10px;
  transition:width 1.2s cubic-bezier(.4,0,.2,1);
}
.hbar-fill span { font-size:12px; font-weight:700; color:#fff; white-space:nowrap }
.hbar-meta {
  font-size:11px; font-weight:700; text-align:center;
  display:flex; align-items:center; justify-content:center;
}

/* ── LOẠI CT bars ── */
.loai-list { padding:16px 18px; display:flex; flex-direction:column; gap:12px }
.loai-row  { cursor:pointer; border-radius:var(--r-sm); padding:8px; margin:-8px; transition:background .15s }
.loai-row:hover { background:var(--c-bg) }
.loai-top  { display:flex; align-items:center; gap:10px; margin-bottom:6px }
.loai-icon { width:28px; height:28px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0 }
.loai-name { font-size:12.5px; font-weight:600; color:var(--c-text); flex:1 }
.loai-num  { font-size:14px; font-weight:800; font-family:'DM Mono',monospace }
.loai-bar  { height:5px; background:var(--c-border2); border-radius:3px; overflow:hidden }
.loai-fill { height:100%; border-radius:3px; transition:width 1.2s cubic-bezier(.4,0,.2,1) }
.loai-pct  { font-size:10px; color:var(--c-text3); margin-top:3px }

/* ── MINI TABLE ── */
.mtbl { width:100%; border-collapse:collapse }
.mtbl th {
  font-size:10px; font-weight:700; color:var(--c-text3);
  text-transform:uppercase; letter-spacing:.6px;
  padding:9px 16px; border-bottom:1px solid var(--c-border2); text-align:left;
}
.mtbl td { padding:10px 16px; border-bottom:1px solid var(--c-border2); font-size:12.5px; color:var(--c-text2); vertical-align:middle }
.mtbl tr:last-child td { border:none }
.mtbl tr:hover td { background:var(--c-bg) }
.mtbl tr { cursor:pointer; transition:background .12s }
.ct-n { font-weight:600; color:var(--c-text); max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap }
.ct-s { font-size:10.5px; color:var(--c-text3); margin-top:2px }

/* ── STATUS BADGE ── */
.sbadge {
  display:inline-flex; align-items:center; gap:4px;
  font-size:10.5px; font-weight:600; padding:3px 9px; border-radius:20px;
}

/* ── ALERT LIST ── */
.al-list { display:flex; flex-direction:column }
.al-row {
  display:flex; align-items:center; gap:12px;
  padding:11px 16px; border-bottom:1px solid var(--c-border2);
  cursor:pointer; transition:background .12s;
}
.al-row:last-child { border:none }
.al-row:hover { background:var(--c-bg) }
.al-pulse {
  width:8px; height:8px; border-radius:50%; background:var(--c-red); flex-shrink:0;
  box-shadow:0 0 0 0 rgba(239,68,68,.4); animation:aring 2s ease infinite;
}
@keyframes aring {
  0%   { box-shadow:0 0 0 0 rgba(239,68,68,.4) }
  70%  { box-shadow:0 0 0 8px rgba(239,68,68,0) }
  100% { box-shadow:0 0 0 0 rgba(239,68,68,0) }
}
.al-name { font-size:12.5px; font-weight:600; color:var(--c-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; flex:1 }
.al-meta { font-size:10.5px; color:var(--c-text3); margin-top:1px }
.al-btn  {
  flex-shrink:0; width:28px; height:28px; border-radius:7px;
  background:var(--c-red-bg); color:var(--c-red); border:none;
  display:flex; align-items:center; justify-content:center;
  cursor:pointer; font-size:11px; transition:all .15s;
  text-decoration:none;
}
.al-btn:hover { background:var(--c-red); color:#fff }

/* ── EMPTY STATE ── */
.empty-state {
  padding:40px 20px; text-align:center; display:flex;
  flex-direction:column; align-items:center; gap:10px;
}
.empty-icon { font-size:32px; opacity:.3 }
.empty-text { font-size:13px; font-weight:600; color:var(--c-text2) }
.empty-sub  { font-size:11.5px; color:var(--c-text3) }

/* ══════════════════════════════
   MODAL
══════════════════════════════ */
.modal-overlay {
  position:fixed; inset:0; background:rgba(15,23,42,.5);
  backdrop-filter:blur(4px); z-index:9000;
  display:flex; align-items:center; justify-content:center; padding:16px;
  opacity:0; pointer-events:none; transition:opacity .25s;
}
.modal-overlay.open { opacity:1; pointer-events:all }
.modal-box {
  background:#fff; border-radius:16px; box-shadow:var(--shadow-lg);
  width:100%; max-width:560px; max-height:90vh; overflow-y:auto;
  transform:translateY(24px) scale(.97); transition:transform .25s cubic-bezier(.4,0,.2,1);
}
.modal-overlay.open .modal-box { transform:translateY(0) scale(1) }
.modal-head {
  position:sticky; top:0; background:#fff; z-index:1;
  padding:18px 20px; border-bottom:1px solid var(--c-border2);
  display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
}
.modal-title { font-size:15px; font-weight:700; color:var(--c-text) }
.modal-sub   { font-size:12px; color:var(--c-text3); margin-top:2px }
.modal-close {
  width:32px; height:32px; border-radius:8px; border:1px solid var(--c-border);
  background:#fff; cursor:pointer; flex-shrink:0; font-size:14px; color:var(--c-text2);
  display:flex; align-items:center; justify-content:center; transition:all .15s;
}
.modal-close:hover { background:var(--c-bg); color:var(--c-text) }
.modal-body { padding:20px }
.modal-list { display:flex; flex-direction:column; gap:8px }
.modal-item {
  display:flex; align-items:center; gap:12px;
  padding:10px 14px; border:1px solid var(--c-border2);
  border-radius:var(--r-sm); cursor:pointer; transition:all .15s;
  text-decoration:none;
}
.modal-item:hover { background:var(--c-bg); border-color:var(--c-border) }
.modal-item-icon {
  width:34px; height:34px; border-radius:9px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; font-size:14px;
}
.modal-item-main { flex:1; min-width:0 }
.modal-item-name { font-size:13px; font-weight:600; color:var(--c-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap }
.modal-item-sub  { font-size:11px; color:var(--c-text3); margin-top:2px }
.modal-item-badge { flex-shrink:0 }
.modal-kv { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px }
.modal-kv-item { background:var(--c-bg); border-radius:var(--r-sm); padding:12px 14px }
.mkv-k { font-size:10px; font-weight:700; color:var(--c-text3); text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px }
.mkv-v { font-size:20px; font-weight:800; font-family:'DM Mono',monospace; color:var(--c-text) }
.modal-section-title { font-size:11px; font-weight:700; color:var(--c-text3); text-transform:uppercase; letter-spacing:.6px; margin-bottom:10px }

/* Scroll mobile */
@media(max-width:600px) {
  .modal-box { max-height:85vh; border-radius:16px 16px 0 0 }
  .modal-overlay { align-items:flex-end; padding:0 }
}

/* ── ANIMATE IN ── */
@keyframes fadeUp {
  from { opacity:0; transform:translateY(16px) }
  to   { opacity:1; transform:translateY(0) }
}
.kpi { animation:fadeUp .4s ease both }
.kpi:nth-child(1){animation-delay:.05s}
.kpi:nth-child(2){animation-delay:.1s}
.kpi:nth-child(3){animation-delay:.15s}
.kpi:nth-child(4){animation-delay:.2s}
.panel { animation:fadeUp .4s ease both; animation-delay:.25s }
</style>

<div class="db">

  <!-- KPI ROW -->
  <div class="kpi-row">

    <!-- Tổng -->
    <div class="kpi" onclick="openModal('tong')">
      <div class="kpi-stripe" style="background:linear-gradient(180deg,#3b82f6,#60a5fa)"></div>
      <div class="kpi-inner">
        <div class="kpi-label">Tổng công trình</div>
        <div class="kpi-main">
          <div class="kpi-num" data-count="<?= $tong['tong'] ?>">0</div>
          <div class="kpi-unit">CT</div>
        </div>
        <div class="kpi-bar-wrap">
          <div class="kpi-bar" data-w="<?= $pctHT ?>%" style="background:linear-gradient(90deg,#3b82f6,#60a5fa);width:0%"></div>
        </div>
        <div class="kpi-foot"><?= $pctHT ?>% hoàn thành · <strong><?= $tong['dang_thi_cong'] ?></strong> đang thi công</div>
      </div>
    </div>

    <!-- Hoàn thành -->
    <div class="kpi" onclick="openModal('hoan_thanh')">
      <div class="kpi-stripe" style="background:linear-gradient(180deg,#10b981,#34d399)"></div>
      <div class="kpi-inner">
        <div class="kpi-label">Hoàn thành</div>
        <div class="kpi-main">
          <div class="kpi-num" style="color:#10b981" data-count="<?= $tong['hoan_thanh'] ?>">0</div>
          <div class="kpi-unit">CT</div>
        </div>
        <div class="kpi-bar-wrap">
          <div class="kpi-bar" data-w="<?= $pctHT ?>%" style="background:linear-gradient(90deg,#10b981,#34d399);width:0%"></div>
        </div>
        <div class="kpi-foot">Còn <strong><?= (int)$tong['tong']-(int)$tong['hoan_thanh'] ?></strong> CT cần hoàn thiện</div>
      </div>
    </div>

    <!-- Khẩn cấp -->
    <div class="kpi" onclick="openModal('khan_cap')">
      <div class="kpi-stripe" style="background:linear-gradient(180deg,#ef4444,#f87171)"></div>
      <div class="kpi-inner">
        <div class="kpi-label">Khẩn cấp</div>
        <div class="kpi-main">
          <div class="kpi-num" style="color:#ef4444" data-count="<?= $tong['khan_cap'] ?>">0</div>
          <div class="kpi-unit">CT</div>
        </div>
        <div class="kpi-bar-wrap">
          <div class="kpi-bar" data-w="<?= $tong['tong']>0?round($tong['khan_cap']/$tong['tong']*100):0 ?>%" style="background:linear-gradient(90deg,#ef4444,#f87171);width:0%"></div>
        </div>
        <div class="kpi-foot"><strong><?= $tong['cao'] ?></strong> ưu tiên cao · <strong><?= $tong['binh_thuong'] ?></strong> bình thường</div>
      </div>
    </div>

    <!-- Chưa tọa độ -->
    <div class="kpi" onclick="openModal('chua_toa_do')">
      <div class="kpi-stripe" style="background:linear-gradient(180deg,#f59e0b,#fbbf24)"></div>
      <div class="kpi-inner">
        <div class="kpi-label">Chưa có tọa độ</div>
        <div class="kpi-main">
          <div class="kpi-num" style="color:#f59e0b" data-count="<?= $chuaTD ?>">0</div>
          <div class="kpi-unit">CT</div>
        </div>
        <div class="kpi-bar-wrap">
          <div class="kpi-bar" data-w="<?= $tong['tong']>0?round($chuaTD/$tong['tong']*100):0 ?>%" style="background:linear-gradient(90deg,#f59e0b,#fbbf24);width:0%"></div>
        </div>
        <div class="kpi-foot"><strong><?= $tong['co_toa_do'] ?></strong> CT đã có · bấm để nhập</div>
      </div>
    </div>

  </div>

  <!-- ROW 2: NĂM + TRẠNG THÁI -->
  <div class="row2-6040">

    <div class="panel">
      <div class="ph">
        <div class="ph-left">
          <div class="ph-title">Kế hoạch theo năm</div>
          <div class="ph-sub">Phân bổ công trình 2025–2030</div>
        </div>
      </div>
      <div class="pb">
        <div class="chart-box">
          <canvas id="ch-nam"></canvas>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="ph">
        <div class="ph-left">
          <div class="ph-title">Trạng thái thực hiện</div>
          <div class="ph-sub">Tiến độ toàn tỉnh</div>
        </div>
      </div>
      <div class="donut-wrap">
        <div class="donut-center" onclick="openModal('trang_thai')">
          <canvas id="ch-tt" width="150" height="150"></canvas>
          <div class="donut-mid">
            <div class="donut-pct"><?= $pctHT ?>%</div>
            <div class="donut-hint">Xong</div>
          </div>
        </div>
        <div class="donut-legend">
          <?php
          $ttItems=[
            ['Hoàn thành',(int)$tong['hoan_thanh'],'#10b981','hoan_thanh'],
            ['Đang thi công',(int)$tong['dang_thi_cong'],'#f59e0b','dang_thi_cong'],
            ['Chờ đầu tư',(int)$tong['cho_dau_tu'],'#e2e8f0','cho_dau_tu'],
          ];
          foreach($ttItems as [$lbl,$val,$col,$key]):
            $p=$tong['tong']>0?round($val/$tong['tong']*100):0;
          ?>
            <div class="dl-row" onclick="openModal('<?= $key ?>')">
              <div class="dl-dot" style="background:<?= $col ?>"></div>
              <div class="dl-name"><?= $lbl ?></div>
              <div class="dl-num"><?= $val ?></div>
              <div class="dl-pct"><?= $p ?>%</div>
            </div>
          <?php endforeach; ?>
          <div style="padding:10px 8px 0;border-top:1px solid var(--c-border2);margin-top:4px">
            <div style="font-size:10px;color:var(--c-text3);font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px">Tiến độ TB</div>
            <div style="font-size:24px;font-weight:800;color:var(--c-green);font-family:'DM Mono',monospace"><?= $tong['avg_tien_do']??0 ?>%</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- ROW 3: HUYỆN + LOẠI -->
  <div class="row2">

    <div class="panel">
      <div class="ph">
        <div class="ph-left">
          <div class="ph-title">Phân bổ theo huyện</div>
          <div class="ph-sub">Bấm để xem chi tiết từng huyện</div>
        </div>
      </div>
      <div class="hbar-list">
        <?php
        $maxV=max(array_column($theoHuyen,'tong')?:[1]);
        $pal=['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899','#14b8a6'];
        foreach($theoHuyen as $i=>$h):
          $pct=round($h['tong']/$maxV*100);
          $htPct=$h['tong']>0?round($h['hoan_thanh']/$h['tong']*100):0;
          $col=$pal[$i%count($pal)];
        ?>
          <div class="hbar-row" onclick="openModal('huyen_<?= $i ?>')">
            <div class="hbar-lbl" title="<?= Html::encode($h['ten_huyen']) ?>"><?= Html::encode($h['ten_huyen']) ?></div>
            <div class="hbar-track">
              <div class="hbar-ht" style="width:0%;background:<?= $col ?>22" data-w="<?= $htPct ?>%"></div>
              <div class="hbar-fill" style="width:0%;background:<?= $col ?>" data-w="<?= $pct ?>%">
                <span><?= $h['tong'] ?></span>
              </div>
            </div>
            <div class="hbar-meta">
              <?php if($h['khan_cap']>0): ?>
                <span style="color:var(--c-red);font-size:11px">🔴 <?= $h['khan_cap'] ?></span>
              <?php else: ?>
                <span style="color:var(--c-green);font-size:11px">✓ <?= $htPct ?>%</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="display:flex;gap:14px;padding:10px 18px 14px;border-top:1px solid var(--c-border2)">
        <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--c-text3)">
          <div style="width:10px;height:10px;border-radius:2px;background:#3b82f6"></div>Tổng
        </div>
        <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--c-text3)">
          <div style="width:10px;height:10px;border-radius:2px;background:rgba(59,130,246,.13)"></div>Hoàn thành
        </div>
        <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--c-text3)">
          <span style="color:var(--c-red)">🔴</span>Khẩn cấp
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="ph">
        <div class="ph-left">
          <div class="ph-title">Loại công trình</div>
          <div class="ph-sub">Bấm để xem danh sách</div>
        </div>
      </div>
      <div class="loai-list">
        <?php
        $loaiCols=['cau'=>'#10b981','duong'=>'#3b82f6','truong'=>'#f59e0b','nuoc'=>'#8b5cf6','khac'=>'#94a3b8'];
        $loaiBg  =['cau'=>'#ecfdf5','duong'=>'#eff6ff','truong'=>'#fffbeb','nuoc'=>'#f5f3ff','khac'=>'#f8fafc'];
        $maxL=max(array_column($theoLoai,'tong')?:[1]);
        foreach($theoLoai as $l):
          $col=$loaiCols[$l['loai_ct']]??'#94a3b8';
          $bg =$loaiBg[$l['loai_ct']]??'#f8fafc';
          $ico=$loaiIcon[$l['loai_ct']]??'fa-box';
          $lbl=$loaiLabel[$l['loai_ct']]??$l['loai_ct'];
          $pct=$tong['tong']>0?round($l['tong']/$tong['tong']*100):0;
          $barW=round($l['tong']/$maxL*100);
        ?>
          <div class="loai-row" onclick="openModal('loai_<?= $l['loai_ct'] ?>')">
            <div class="loai-top">
              <div class="loai-icon" style="background:<?= $bg ?>;color:<?= $col ?>">
                <i class="fa <?= $ico ?>"></i>
              </div>
              <div class="loai-name"><?= Html::encode($lbl) ?></div>
              <div class="loai-num" style="color:<?= $col ?>"><?= $l['tong'] ?></div>
            </div>
            <div class="loai-bar">
              <div class="loai-fill" style="width:0%;background:<?= $col ?>" data-w="<?= $barW ?>%"></div>
            </div>
            <div class="loai-pct"><?= $pct ?>% tổng số · <?= $l['tong'] ?> công trình</div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

  <!-- ROW 4: RECENT + URGENT -->
  <div class="row2">

    <div class="panel">
      <div class="ph">
        <div class="ph-left">
          <div class="ph-title">Nhập gần đây</div>
          <div class="ph-sub">5 công trình mới nhất</div>
        </div>
        <a class="ph-link" href="<?= Url::to(['/cong-trinh/index']) ?>">
          Xem tất cả <i class="fa fa-arrow-right"></i>
        </a>
      </div>
      <div class="pb-0">
        <table class="mtbl">
          <thead>
            <tr>
              <th>Công trình</th>
              <th>Năm</th>
              <th>Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($recent as $r): ?>
              <tr onclick="window.location='<?= Url::to(['/cong-trinh/view','id'=>$r['id']]) ?>'">
                <td>
                  <div class="ct-n"><?= Html::encode($r['ten_ct']) ?></div>
                  <div class="ct-s"><?= Html::encode(($r['ten_xa']??'').' · '.($r['ten_huyen']??'')) ?></div>
                </td>
                <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--c-text)"><?= $r['nam_dau_tu'] ?></td>
                <td>
                  <span class="sbadge" style="background:<?= $statusBg[$r['trang_thai']]??'#f1f5f9' ?>;color:<?= $statusColor[$r['trang_thai']]??'#94a3b8' ?>">
                    <?= $statusLabel[$r['trang_thai']]??$r['trang_thai'] ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <div class="ph">
        <div class="ph-left" style="display:flex;align-items:center;gap:8px">
          <div class="al-pulse" style="flex-shrink:0"></div>
          <div>
            <div class="ph-title">Cần xử lý</div>
            <div class="ph-sub">Khẩn cấp · Chưa có tọa độ</div>
          </div>
        </div>
        <?php if(!empty($urgent)): ?>
          <a class="ph-link" style="color:var(--c-red)"
             href="<?= Url::to(['/cong-trinh/index','priority'=>'khan_cap','coords'=>'no']) ?>">
            Xem tất cả <i class="fa fa-arrow-right"></i>
          </a>
        <?php endif; ?>
      </div>
      <?php if(empty($urgent)): ?>
        <div class="empty-state">
          <div class="empty-icon">✅</div>
          <div class="empty-text">Không có tồn đọng!</div>
          <div class="empty-sub">Tất cả CT khẩn cấp đã có tọa độ</div>
        </div>
      <?php else: ?>
        <div class="al-list">
          <?php foreach($urgent as $u): ?>
            <div class="al-row" onclick="window.location='<?= Url::to(['/cong-trinh/update','id'=>$u['id']]) ?>'">
              <div class="al-pulse"></div>
              <div style="flex:1;min-width:0">
                <div class="al-name"><?= Html::encode($u['ten_ct']) ?></div>
                <div class="al-meta"><?= Html::encode(($u['ten_xa']??'').' · '.($u['ten_huyen']??'').' · '.$u['nam_dau_tu']) ?></div>
              </div>
              <a href="<?= Url::to(['/cong-trinh/update','id'=>$u['id']]) ?>"
                 class="al-btn" title="Nhập tọa độ" onclick="event.stopPropagation()">
                <i class="fa fa-map-pin"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>

</div><!-- /db -->

<!-- ══ MODAL ══ -->
<div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <div class="modal-head">
      <div>
        <div class="modal-title" id="modal-title">Chi tiết</div>
        <div class="modal-sub"  id="modal-sub"></div>
      </div>
      <button class="modal-close" onclick="closeModal()"><i class="fa fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="modal-body"></div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color = '#94a3b8';

// ── COUNTER ANIMATION ──
function animateCount(el){
  const target=parseInt(el.dataset.count)||0;
  if(target===0){ el.textContent='0'; return; }
  let cur=0, step=Math.max(1,Math.ceil(target/50));
  const t=setInterval(()=>{ cur=Math.min(cur+step,target); el.textContent=cur; if(cur>=target)clearInterval(t); },20);
}
document.querySelectorAll('[data-count]').forEach(animateCount);

// ── BAR ANIMATIONS ──
setTimeout(()=>{
  document.querySelectorAll('[data-w]').forEach(el=>{ el.style.width=el.dataset.w; });
},300);

// ── CHART: NĂM ──
new Chart(document.getElementById('ch-nam'),{
  type:'bar',
  data:{
    labels:<?= $jNamLabels ?>,
    datasets:[
      {label:'Tổng CT',data:<?= $jNamTong ?>,backgroundColor:'rgba(59,130,246,.1)',borderColor:'#3b82f6',borderWidth:2,borderRadius:8,borderSkipped:false},
      {label:'Hoàn thành',data:<?= $jNamHT ?>,backgroundColor:'rgba(16,185,129,.85)',borderRadius:8,borderSkipped:false},
    ]
  },
  options:{
    responsive:true,maintainAspectRatio:false,
    animation:{duration:1000,easing:'easeOutQuart'},
    onClick:(e,el,chart)=>{
      if(el.length){
        const idx=el[0].index;
        const lbl=chart.data.labels[idx];
        openModal('nam_'+lbl);
      }
    },
    plugins:{
      legend:{position:'bottom',labels:{usePointStyle:true,pointStyle:'circle',padding:16,font:{size:11}}},
      tooltip:{cornerRadius:8,padding:10},
    },
    scales:{
      x:{grid:{display:false},border:{display:false}},
      y:{grid:{color:'#f1f5f9'},border:{display:false},ticks:{stepSize:1,padding:6},beginAtZero:true},
    },
  }
});

// ── CHART: TRẠNG THÁI ──
new Chart(document.getElementById('ch-tt'),{
  type:'doughnut',
  data:{
    labels:['Hoàn thành','Đang thi công','Chờ đầu tư'],
    datasets:[{
      data:[<?= $tong['hoan_thanh'] ?>,<?= $tong['dang_thi_cong'] ?>,<?= $tong['cho_dau_tu'] ?>],
      backgroundColor:['#10b981','#f59e0b','#e2e8f0'],
      borderWidth:4,borderColor:'#fff',hoverOffset:6,
    }]
  },
  options:{
    cutout:'72%',
    animation:{duration:1200,easing:'easeOutQuart'},
    plugins:{
      legend:{display:false},
      tooltip:{cornerRadius:8,callbacks:{label:c=>` ${c.label}: ${c.raw} CT`}},
    },
    onClick:(e,el)=>{ if(el.length){ const keys=['hoan_thanh','dang_thi_cong','cho_dau_tu']; openModal(keys[el[0].index]); } },
  }
});

// ── DATA ──
const DATA = {
  recent:   <?= $recentJson ?>,
  urgent:   <?= $urgentJson ?>,
  huyen:    <?= $huyenJson ?>,
  nam:      <?= $namJson ?>,
  loai:     <?= $loaiJson ?>,
  tt:       <?= $ttJson ?>,
  loaiLabel:<?= json_encode($loaiLabel) ?>,
  statusLabel:{'cho_dau_tu':'Chờ đầu tư','dang_thi_cong':'Đang thi công','hoan_thanh':'Hoàn thành'},
  statusColor:{'cho_dau_tu':'#94a3b8','dang_thi_cong':'#f59e0b','hoan_thanh':'#10b981'},
  statusBg:{'cho_dau_tu':'#f1f5f9','dang_thi_cong':'#fffbeb','hoan_thanh':'#ecfdf5'},
  prioLabel:{'khan_cap':'Khẩn cấp','cao':'Cao','binh_thuong':'Bình thường'},
  prioColor:{'khan_cap':'#ef4444','cao':'#f59e0b','binh_thuong':'#10b981'},
  prioBg:{'khan_cap':'#fef2f2','cao':'#fffbeb','binh_thuong':'#ecfdf5'},
  viewBase:'<?= Url::to(['/cong-trinh/index']) ?>',
};

// ── MODAL ──
function openModal(key){
  const ov=document.getElementById('modal');
  const title=document.getElementById('modal-title');
  const sub=document.getElementById('modal-sub');
  const body=document.getElementById('modal-body');

  let html='', t='', s='';

  // Helper: item link
  const item=(r,extra='')=>`
    <a class="modal-item" href="${DATA.viewBase}?${extra}">
      <div class="modal-item-icon" style="background:${DATA.statusBg[r.trang_thai]||'#f1f5f9'};color:${DATA.statusColor[r.trang_thai]||'#94a3b8'}">
        <i class="fa fa-bridge"></i>
      </div>
      <div class="modal-item-main">
        <div class="modal-item-name">${r.ten_ct||'–'}</div>
        <div class="modal-item-sub">${r.ten_xa||''} · ${r.ten_huyen||''} · ${r.nam_dau_tu||''}</div>
      </div>
      <div class="modal-item-badge">
        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:${DATA.statusBg[r.trang_thai]||'#f1f5f9'};color:${DATA.statusColor[r.trang_thai]||'#94a3b8'}">${DATA.statusLabel[r.trang_thai]||r.trang_thai||''}</span>
      </div>
    </a>`;

  const statBox=(label,val,color='#0f172a')=>`
    <div class="modal-kv-item">
      <div class="mkv-k">${label}</div>
      <div class="mkv-v" style="color:${color}">${val}</div>
    </div>`;

  if(key==='tong'){
    t='Tổng quan công trình'; s='Tất cả '+DATA.recent.length>0?'20':'0'+' công trình';
    const kv=`<div class="modal-kv">
      ${statBox('Tổng số','<?= $tong["tong"] ?>','#0f172a')}
      ${statBox('Hoàn thành','<?= $tong["hoan_thanh"] ?>','#10b981')}
      ${statBox('Đang thi công','<?= $tong["dang_thi_cong"] ?>','#f59e0b')}
      ${statBox('Chờ đầu tư','<?= $tong["cho_dau_tu"] ?>','#94a3b8')}
    </div>`;
    html=kv+`<div class="modal-section-title">Gần đây nhất</div><div class="modal-list">`+DATA.recent.map(r=>item(r)).join('')+`</div>`;
  }
  else if(key==='hoan_thanh'||key==='dang_thi_cong'||key==='cho_dau_tu'){
    const lbl=DATA.statusLabel[key]; const col=DATA.statusColor[key];
    t=lbl; s='Danh sách công trình';
    html=`<div class="modal-list">`+DATA.recent.filter(r=>r.trang_thai===key).map(r=>item(r)).join('')
      +`</div><div style="margin-top:14px;text-align:center"><a href="${DATA.viewBase}?status=${key}" style="font-size:13px;font-weight:600;color:#10b981;text-decoration:none">Xem đầy đủ →</a></div>`;
    if(DATA.recent.filter(r=>r.trang_thai===key).length===0)
      html=`<div class="empty-state"><div class="empty-icon">📋</div><div class="empty-text">Không có dữ liệu mẫu</div><a href="${DATA.viewBase}?status=${key}" style="font-size:13px;font-weight:600;color:#10b981;text-decoration:none;margin-top:6px;display:block">Xem danh sách đầy đủ →</a></div>`;
  }
  else if(key==='khan_cap'){
    t='Công trình khẩn cấp'; s=DATA.urgent.length+' cần xử lý';
    if(DATA.urgent.length===0)
      html=`<div class="empty-state"><div class="empty-icon">✅</div><div class="empty-text">Không có tồn đọng</div></div>`;
    else
      html=`<div class="modal-list">`+DATA.urgent.map(r=>item(r,'priority=khan_cap')).join('')+`</div>`;
  }
  else if(key==='chua_toa_do'){
    t='Chưa có tọa độ'; s='Cần bổ sung vị trí';
    html=`<div class="modal-list">`+DATA.urgent.map(r=>item(r,'coords=no')).join('')
      +`</div><div style="margin-top:14px;text-align:center"><a href="${DATA.viewBase}?coords=no" style="font-size:13px;font-weight:600;color:#f59e0b;text-decoration:none">Xem tất cả →</a></div>`;
  }
  else if(key==='trang_thai'){
    t='Tổng quan trạng thái'; s='Phân bổ theo tiến độ';
    html=`<div class="modal-kv">`+DATA.tt.map(d=>statBox(d.label,d.val,d.color)).join('')+`</div>
    <div style="text-align:center"><a href="${DATA.viewBase}" style="font-size:13px;font-weight:600;color:#10b981;text-decoration:none">Xem danh sách đầy đủ →</a></div>`;
  }
  else if(key.startsWith('huyen_')){
    const idx=parseInt(key.split('_')[1]);
    const h=DATA.huyen[idx]; if(!h) return;
    t=h.ten_huyen; s=h.tong+' công trình · '+h.hoan_thanh+' hoàn thành';
    html=`<div class="modal-kv">
      ${statBox('Tổng số',h.tong,'#0f172a')}
      ${statBox('Hoàn thành',h.hoan_thanh,'#10b981')}
      ${statBox('Khẩn cấp',h.khan_cap,'#ef4444')}
      ${statBox('Tỷ lệ HT',h.tong>0?Math.round(h.hoan_thanh/h.tong*100)+'%':'0%','#3b82f6')}
    </div>
    <div style="text-align:center"><a href="${DATA.viewBase}?huyen=${encodeURIComponent(h.ten_huyen)}" style="font-size:13px;font-weight:600;color:#10b981;text-decoration:none">Xem CT tại ${h.ten_huyen} →</a></div>`;
  }
  else if(key.startsWith('nam_')){
    const nam=key.split('_')[1];
    const n=DATA.nam.find(d=>d.nam_dau_tu==nam); if(!n) return;
    t='Năm '+nam; s=n.tong+' công trình kế hoạch';
    html=`<div class="modal-kv">
      ${statBox('Tổng số',n.tong,'#0f172a')}
      ${statBox('Hoàn thành',n.hoan_thanh,'#10b981')}
      ${statBox('Có tọa độ',n.co_toa_do,'#3b82f6')}
      ${statBox('Tỷ lệ HT',n.tong>0?Math.round(n.hoan_thanh/n.tong*100)+'%':'0%','#8b5cf6')}
    </div>
    <div style="text-align:center"><a href="${DATA.viewBase}?nam=${nam}" style="font-size:13px;font-weight:600;color:#10b981;text-decoration:none">Xem CT năm ${nam} →</a></div>`;
  }
  else if(key.startsWith('loai_')){
    const loai=key.replace('loai_','');
    const l=DATA.loai.find(d=>d.loai_ct===loai); if(!l) return;
    t=DATA.loaiLabel[loai]||loai; s=l.tong+' công trình';
    html=`<div class="modal-kv">
      ${statBox('Tổng số',l.tong,'#0f172a')}
    </div>
    <div style="text-align:center"><a href="${DATA.viewBase}?loai_ct=${loai}" style="font-size:13px;font-weight:600;color:#10b981;text-decoration:none">Xem danh sách →</a></div>`;
  }

  title.textContent=t; sub.textContent=s; body.innerHTML=html;
  ov.classList.add('open');
  document.body.style.overflow='hidden';
}

function closeModal(){
  document.getElementById('modal').classList.remove('open');
  document.body.style.overflow='';
}
document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeModal(); });
</script>