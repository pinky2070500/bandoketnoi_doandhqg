<?php
/** @var yii\web\View $this */
/** @var int $id */
$this->title = 'Chi tiết — Bản đồ số ĐHQG-HCM';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $this->title ?></title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/dhqg.css"/>
<style>
body{background:var(--bg)}
.topbar{background:var(--brand-grad);color:#fff;padding:12px 18px;display:flex;align-items:center;gap:10px;position:sticky;top:0;z-index:5}
.topbar .emblem{width:38px;height:38px;border-radius:9px;background:#fff;display:flex;align-items:center;justify-content:center;padding:4px}
.topbar .emblem img{max-width:100%;max-height:100%;object-fit:contain}
.topbar b{font-size:14px;font-weight:800}
.wrap{max-width:680px;margin:0 auto;padding:16px}
.hero{height:240px;position:relative;background:var(--brand-grad);border-radius:var(--radius-lg);overflow:hidden;display:flex;align-items:flex-end;color:#fff;padding:20px}
.hero img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.hero::after{content:"";position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.6),rgba(0,0,0,0) 62%)}
.hero .cap{position:relative;z-index:2}
.hero .cap .badge{background:rgba(255,255,255,.22);color:#fff;margin-bottom:8px}
.hero .cap h1{font-size:24px;font-weight:900;line-height:1.15}
.hero .cap p{opacity:.92;font-size:13px;margin-top:4px}
.box{margin-top:16px;padding:18px}
.row{display:flex;justify-content:space-between;gap:14px;padding:11px 0;border-bottom:1px solid var(--line-2);font-size:14px}
.row:last-child{border-bottom:none}
.row .k{color:var(--gray)}.row .v{font-weight:600;text-align:right}
.sec-t{font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:var(--gray);font-weight:700;margin:20px 0 10px}
.gal{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px}
.gal a{position:relative}
.gal img{width:100%;height:120px;object-fit:cover;border-radius:var(--radius-sm);border:1px solid var(--line)}
.gal .tag{position:absolute;left:6px;bottom:6px;background:rgba(0,0,0,.6);color:#fff;font-size:10px;padding:2px 7px;border-radius:20px}
#mini{height:240px;border-radius:var(--radius-sm);overflow:hidden;border:1px solid var(--line)}
.miss{padding:70px 20px;text-align:center;color:var(--gray-400)}
</style>
</head>
<body>
<div class="topbar"><div class="emblem"><img src="/img/logo-mark-160.png" alt="ĐHQG-HCM"></div><b>Bản đồ số Khu đô thị ĐHQG-HCM</b></div>
<div class="wrap" id="wrap"><div class="miss"><div class="spin" style="margin:0 auto 12px"></div>Đang tải…</div></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const ID=<?= (int)$id ?>;
const COLOR={cong_trinh:'#16a37f',an_toan:'#ef4444',truyen_thong:'#f59e0b'};
const ICON={cong_trinh:'fa-flag',an_toan:'fa-triangle-exclamation',truyen_thong:'fa-bullhorn'};
const TT={de_xuat:'#64748b',dang_trien_khai:'#f59e0b',hoan_thanh:'#16a37f',bao_tri:'#3b82f6'};
async function load(){
  const d=await (await fetch('/api/diem-chi-tiet?id='+ID)).json();
  const w=document.getElementById('wrap');
  if(!d.ok){w.innerHTML='<div class="miss"><i class="fa fa-circle-exclamation" style="font-size:32px"></i><p style="margin-top:10px">Không tìm thấy dữ liệu.</p></div>';return;}
  const c=COLOR[d.module]||'#123c8a';
  const heroImg=d.anh.length?`<img src="${d.anh[0].url}" alt="">`:'';
  const tt=d.trang_thai?`<div class="row"><span class="k">Trạng thái</span><span class="v"><span class="badge" style="background:${(TT[d.trang_thai]||'#888')}22;color:${TT[d.trang_thai]||'#888'}"><span class="dot"></span>${d.trang_thai_label}</span></span></div>`:'';
  let gal=''; if(d.anh.length){gal=`<div class="sec-t">Hình ảnh (${d.anh.length})</div><div class="gal">${d.anh.map(a=>`<a href="${a.url}" target="_blank"><img src="${a.url}" alt=""><span class="tag">${a.loai||''}</span></a>`).join('')}</div>`;}
  let mapHtml=(d.lat&&d.lng)?`<div class="sec-t">Vị trí</div><div id="mini"></div>`:'';
  w.innerHTML=`
    <div class="hero" style="background:${c}">${heroImg}<div class="cap"><span class="badge"><i class="fa ${ICON[d.module]||''}"></i> ${d.module_label}</span><h1>${d.ten}</h1><p>${d.loai_label||''}</p></div></div>
    <div class="box card">
      ${d.ma?`<div class="row"><span class="k">Mã</span><span class="v">${d.ma}</span></div>`:''}
      ${d.nam?`<div class="row"><span class="k">Năm thực hiện</span><span class="v">${d.nam}</span></div>`:''}
      ${tt}
      ${d.don_vi_thuc_hien?`<div class="row"><span class="k">Đơn vị thực hiện</span><span class="v">${d.don_vi_thuc_hien}</span></div>`:''}
      ${d.don_vi_quan_ly?`<div class="row"><span class="k">Đơn vị quản lý</span><span class="v">${d.don_vi_quan_ly}</span></div>`:''}
      ${d.mo_ta?`<div class="sec-t">Mô tả</div><div style="font-size:14px;line-height:1.65;color:var(--gray-700)">${d.mo_ta}</div>`:''}
      ${d.noi_dung?`<div class="sec-t">Nội dung</div><div style="font-size:14px;line-height:1.65;color:var(--gray-700)">${d.noi_dung}</div>`:''}
      ${gal}
      ${mapHtml}
      <a class="btn btn-light" style="margin-top:20px" href="/ban-do"><i class="fa fa-arrow-left"></i> Về bản đồ</a>
    </div>`;
  if(d.lat&&d.lng){
    const m=L.map('mini').setView([d.lat,d.lng],16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{subdomains:'abcd',maxZoom:20}).addTo(m);
    L.circleMarker([d.lat,d.lng],{radius:10,color:'#fff',weight:3,fillColor:c,fillOpacity:1}).addTo(m);
  }
  document.title=d.ten+' — ĐHQG-HCM';
}
load();
</script>
</body>
</html>
