<?php
/** @var yii\web\View $this */
$this->title = 'Dashboard tổng hợp — Bản đồ số ĐHQG-HCM';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $this->title ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/dhqg.css"/>
<style>
body{min-height:100vh}
.top{background:var(--brand-grad);color:#fff;padding:16px 26px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;position:sticky;top:0;z-index:10;box-shadow:var(--shadow)}
.top .emblem{width:42px;height:42px;border-radius:11px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:13px}
.top h1{font-size:18px;font-weight:800;letter-spacing:-.3px}
.top p{font-size:12px;opacity:.85}
.top .sp{flex:1}
.top a{color:#fff;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.25);padding:8px 14px;border-radius:var(--radius-sm);font-weight:600;font-size:13px}
.top a:hover{background:rgba(255,255,255,.24)}
.filters{display:flex;gap:12px;flex-wrap:wrap;align-items:end;padding:18px 26px 4px}
.filters .fld label{display:block;font-size:11px;color:var(--gray);font-weight:700;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px}
.filters select{padding:9px 12px;border:1px solid var(--line);border-radius:var(--radius-sm);font-size:13px;background:#fff;min-width:170px}
.filters select:focus{outline:none;border-color:var(--brand-2);box-shadow:0 0 0 3px var(--ring)}
.hint{color:var(--gray);font-size:12.5px;margin-left:auto;display:flex;align-items:center;gap:6px}
.wrap{padding:16px 26px 44px;max-width:1280px}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(184px,1fr));gap:15px;margin-bottom:20px}
.charts{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
.chart{padding:20px}
.chart h3{font-size:14.5px;font-weight:700;margin-bottom:4px;display:flex;align-items:center;gap:8px}
.chart .cd{font-size:12px;color:var(--gray-400);margin-bottom:14px}
.chart .cv{position:relative;height:290px}
@media(max-width:820px){.charts{grid-template-columns:1fr}.filters{padding:16px}.wrap{padding:16px}}
</style>
</head>
<body>
<div class="top">
  <div class="emblem">ĐHQG</div>
  <div><h1>Dashboard tổng hợp</h1><p>Khu đô thị ĐHQG-HCM · Màn hình lãnh đạo</p></div>
  <div class="sp"></div>
  <a href="/ban-do"><i class="fa fa-map"></i> Về bản đồ</a>
</div>

<div class="filters">
  <div class="fld"><label>Đơn vị</label><select id="f-dv"><option value="">Tất cả đơn vị</option></select></div>
  <div class="fld"><label>Từ năm</label><select id="f-tu"><option value="">—</option></select></div>
  <div class="fld"><label>Đến năm</label><select id="f-den"><option value="">—</option></select></div>
  <div class="hint"><i class="fa fa-hand-pointer"></i> Bấm vào bất kỳ con số hoặc phần biểu đồ để xem chi tiết</div>
</div>

<div class="wrap">
  <div class="cards" id="cards"></div>
  <div class="charts">
    <div class="chart card"><h3><i class="fa fa-layer-group" style="color:var(--brand-2)"></i> Số lượng theo hạng mục</h3><div class="cd">Bấm một phần để xem danh sách</div><div class="cv"><canvas id="c-module"></canvas></div></div>
    <div class="chart card"><h3><i class="fa fa-flag" style="color:var(--m-cong_trinh)"></i> Công trình thanh niên theo trạng thái</h3><div class="cd">Bấm một cột để xem danh sách</div><div class="cv"><canvas id="c-tt"></canvas></div></div>
    <div class="chart card"><h3><i class="fa fa-chart-line" style="color:var(--brand-2)"></i> Tiến độ theo năm</h3><div class="cd">Bấm một điểm để xem danh sách theo năm</div><div class="cv"><canvas id="c-nam"></canvas></div></div>
    <div class="chart card"><h3><i class="fa fa-shapes" style="color:var(--m-cong_trinh)"></i> Công trình theo loại</h3><div class="cd">Bấm một phần để xem danh sách</div><div class="cv"><canvas id="c-loai"></canvas></div></div>
  </div>
</div>

<!-- Modal -->
<div class="modal-ov" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <div class="modal-head">
      <div class="ico" id="m-ico"><i class="fa fa-list"></i></div>
      <div><div class="tt" id="m-title">Danh sách</div><div class="st" id="m-sub"></div></div>
      <button class="x" onclick="closeModal()"><i class="fa fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="m-body"></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
let CFG, charts={}, keys={};
const state={dv:'',tu:'',den:''};

const lblModule=k=>(CFG.modules[k]?.label_ngan)||k;
const lblLoai=(m,k)=>(CFG.modules[m]?.loai?.[k])||k;
const lblTT=k=>CFG.trang_thai[k]||k;
const mauModule=k=>CFG.modules[k]?.mau||CFG.brand.main;

async function boot(){
  CFG=await (await fetch('/api/cau-hinh')).json();
  Chart.defaults.font.family="'Inter',sans-serif";
  Chart.defaults.color='#64748b';
  const dv=await (await fetch('/api/don-vi')).json();
  const dvSel=document.getElementById('f-dv');
  dv.forEach(d=>dvSel.insertAdjacentHTML('beforeend',`<option value="${d.id}">${d.ten}</option>`));
  ['f-tu','f-den'].forEach(idp=>{const s=document.getElementById(idp);for(let y=2023;y<=2030;y++)s.insertAdjacentHTML('beforeend',`<option>${y}</option>`);});
  document.getElementById('f-dv').onchange=e=>{state.dv=e.target.value;load();};
  document.getElementById('f-tu').onchange=e=>{state.tu=e.target.value;load();};
  document.getElementById('f-den').onchange=e=>{state.den=e.target.value;load();};
  document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal();});
  load();
}

function tongHopQs(){const p=new URLSearchParams();if(state.dv)p.set('don_vi',state.dv);if(state.tu)p.set('tu_nam',state.tu);if(state.den)p.set('den_nam',state.den);return p.toString();}

async function load(){
  const d=await (await fetch('/api/tong-hop?'+tongHopQs())).json();
  const th=d.the;
  const cards=[
    ['Công trình thanh niên',th.cong_trinh,'fa-flag',mauModule('cong_trinh'),{module:'cong_trinh'}],
    ['Điểm check-in',th.check_in,'fa-location-dot','#0ea5e9',{module:'cong_trinh',loai:'check_in'}],
    ['Điểm an toàn',th.an_toan,'fa-triangle-exclamation',mauModule('an_toan'),{module:'an_toan'}],
    ['Pano truyền thông',th.pano,'fa-bullhorn',mauModule('truyen_thong'),{module:'truyen_thong',loai:'pano'}],
    ['Đã hoàn thành',th.hoan_thanh,'fa-circle-check','#16a37f',{trang_thai:'hoan_thanh'}],
  ];
  document.getElementById('cards').innerHTML=cards.map((c,i)=>
    `<div class="card kpi click" data-i="${i}"><i class="fa ${c[2]} ic" style="color:${c[3]}"></i><div class="n" style="color:${c[3]}">${c[1]}</div><div class="l">${c[0]}</div></div>`).join('');
  document.querySelectorAll('.kpi.click').forEach(el=>{
    const c=cards[+el.dataset.i];
    el.onclick=()=>openModal(c[0],c[3],c[2],c[4]);
  });

  keys.module=d.theo_module.map(r=>r.module);
  drawDoughnut('c-module',d.theo_module.map(r=>lblModule(r.module)),d.theo_module.map(r=>+r.so),
    d.theo_module.map(r=>mauModule(r.module)),
    i=>openModal(lblModule(keys.module[i]),mauModule(keys.module[i]),CFG.modules[keys.module[i]]?.icon||'fa-layer-group',{module:keys.module[i]}));

  keys.tt=d.theo_trang_thai.map(r=>r.trang_thai);
  drawBar('c-tt',d.theo_trang_thai.map(r=>lblTT(r.trang_thai)),d.theo_trang_thai.map(r=>+r.so),
    d.theo_trang_thai.map(r=>CFG.mau_trang_thai[r.trang_thai]||'#888'),
    i=>openModal('Công trình: '+lblTT(keys.tt[i]),CFG.mau_trang_thai[keys.tt[i]]||'#888','fa-flag',{module:'cong_trinh',trang_thai:keys.tt[i]}));

  keys.nam=d.theo_nam.map(r=>r.nam);
  drawLine('c-nam',d.theo_nam.map(r=>r.nam),d.theo_nam.map(r=>+r.so),
    i=>openModal('Năm '+keys.nam[i],CFG.brand.main,'fa-calendar',{nam:keys.nam[i]}));

  keys.loai=d.theo_loai_ct.map(r=>r.loai);
  drawDoughnut('c-loai',d.theo_loai_ct.map(r=>lblLoai('cong_trinh',r.loai)),d.theo_loai_ct.map(r=>+r.so),
    ['#16a37f','#0ea5e9','#f59e0b','#8b5cf6','#ec4899','#14b8a6'],
    i=>openModal('Công trình: '+lblLoai('cong_trinh',keys.loai[i]),mauModule('cong_trinh'),'fa-shapes',{module:'cong_trinh',loai:keys.loai[i]}));
}

function reset(id){if(charts[id])charts[id].destroy();return document.getElementById(id);}
function clickCfg(onIdx){return (e,els)=>{if(els.length)onIdx(els[0].index);};}
function drawDoughnut(id,labels,data,colors,onIdx){charts[id]=new Chart(reset(id),{type:'doughnut',
  data:{labels,datasets:[{data,backgroundColor:colors,borderWidth:3,borderColor:'#fff',hoverOffset:8}]},
  options:{responsive:true,maintainAspectRatio:false,onClick:clickCfg(onIdx),onHover:(e,el)=>e.native.target.style.cursor=el.length?'pointer':'default',
    plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:14,font:{size:12}}}},cutout:'58%'}});}
function drawBar(id,labels,data,colors,onIdx){charts[id]=new Chart(reset(id),{type:'bar',
  data:{labels,datasets:[{data,backgroundColor:colors,borderRadius:8,maxBarThickness:64}]},
  options:{responsive:true,maintainAspectRatio:false,onClick:clickCfg(onIdx),onHover:(e,el)=>e.native.target.style.cursor=el.length?'pointer':'default',
    plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0},grid:{color:'#eef2f7'}},x:{grid:{display:false}}}}});}
function drawLine(id,labels,data,onIdx){charts[id]=new Chart(reset(id),{type:'line',
  data:{labels,datasets:[{data,borderColor:CFG.brand.alt,backgroundColor:'rgba(27,82,192,.12)',fill:true,tension:.35,pointRadius:5,pointHoverRadius:8,pointBackgroundColor:CFG.brand.main}]},
  options:{responsive:true,maintainAspectRatio:false,onClick:clickCfg(onIdx),onHover:(e,el)=>e.native.target.style.cursor=el.length?'pointer':'default',
    plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0},grid:{color:'#eef2f7'}},x:{grid:{display:false}}}}});}

async function openModal(title,color,icon,filter){
  const mo=document.getElementById('modal');
  document.getElementById('m-ico').style.background=color;
  document.getElementById('m-ico').innerHTML=`<i class="fa ${icon}"></i>`;
  document.getElementById('m-title').textContent=title;
  document.getElementById('m-sub').textContent='Đang tải…';
  document.getElementById('m-body').innerHTML='<div style="display:flex;justify-content:center;padding:30px"><div class="spin"></div></div>';
  mo.classList.add('open');

  // Danh sách trong modal: mang theo lọc đơn vị hiện tại + filter của phần được bấm.
  const p=new URLSearchParams();
  if(state.dv)p.set('don_vi',state.dv);
  for(const [k,v] of Object.entries(filter)) p.set(k,v);
  const gj=await (await fetch('/api/diem?'+p.toString())).json();
  const feats=gj.features;
  document.getElementById('m-sub').textContent=feats.length+' đối tượng';
  if(!feats.length){document.getElementById('m-body').innerHTML='<div class="mempty">Không có đối tượng phù hợp.</div>';return;}
  document.getElementById('m-body').innerHTML='<div class="mlist">'+feats.map(f=>{
    const pr=f.properties, mm=CFG.modules[pr.module];
    const sub=[lblModule(pr.module),pr.loai_label,pr.nam,pr.trang_thai_label,pr.don_vi].filter(Boolean).join(' · ');
    return `<a class="mrow" href="/chi-tiet/${pr.id}" target="_blank">
      <span class="mk" style="background:${mm?.mau||'#888'}"><i class="fa ${mm?.icon||'fa-map-marker-alt'}"></i></span>
      <span class="mi"><div class="t">${pr.ten}</div><div class="s">${sub}</div></span>
      <span class="go">Chi tiết ↗</span></a>`;
  }).join('')+'</div>';
}
function closeModal(){document.getElementById('modal').classList.remove('open');}

boot();
</script>
</body>
</html>
