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
.top{background:var(--brand-grad);color:#fff;padding:16px 28px;display:flex;align-items:center;gap:14px;position:sticky;top:0;z-index:20;box-shadow:var(--shadow)}
.top .emblem{width:48px;height:48px;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center;padding:6px;box-shadow:var(--shadow-sm)}
.top .emblem img{max-width:100%;max-height:100%;object-fit:contain}
.top h1{font-size:19px;font-weight:800;letter-spacing:-.3px}
.top p{font-size:12px;opacity:.85}
.top .sp{flex:1}
.top a.home{color:#fff;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.28);padding:9px 15px;border-radius:var(--radius-sm);font-weight:600;font-size:13px}
.top a.home:hover{background:rgba(255,255,255,.24)}
.wrap{max-width:1320px;margin:0 auto;padding:20px 28px 50px}
.filterbar{padding:16px 18px;display:flex;gap:14px;flex-wrap:wrap;align-items:end;margin-bottom:10px}
.filterbar .fld{flex:1;min-width:150px}
.filterbar label{display:block;font-size:11px;color:var(--gray);font-weight:700;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
.filterbar select{width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:var(--radius-sm);font-size:13px;background:#fff}
.filterbar select:focus{outline:none;border-color:var(--brand-2);box-shadow:0 0 0 3px var(--ring)}
.hintbar{display:flex;align-items:center;gap:9px;background:var(--brand-l);border:1px solid #d5e2f7;color:var(--brand-d);
  padding:11px 16px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;margin-bottom:24px}
.hintbar i{font-size:15px}
.sec{margin-bottom:30px}
.sec-h{display:flex;align-items:center;gap:12px;margin-bottom:15px}
.sec-h .bar{width:5px;height:26px;border-radius:6px;background:var(--brand)}
.sec-h .tt{font-size:16.5px;font-weight:800}
.sec-h .badge{margin-left:auto}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(178px,1fr));gap:15px}
.cards.six{grid-template-columns:repeat(auto-fit,minmax(165px,1fr))}
.charts{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
.charts.three{grid-template-columns:repeat(3,1fr)}
.chart{padding:20px}
.chart h4{font-size:14px;font-weight:700;margin-bottom:2px;display:flex;align-items:center;gap:8px}
.chart .cd{font-size:11.5px;color:var(--gray-400);margin-bottom:12px}
.chart .cv{position:relative;height:270px}
.chart .cv.sm{height:230px}
@media(max-width:960px){.charts,.charts.three{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="top">
  <div class="emblem"><img src="/img/logo-mark-160.png" alt="ĐHQG-HCM"></div>
  <div><h1>Dashboard tổng hợp</h1><p>Khu đô thị ĐHQG-HCM · Màn hình điều hành</p></div>
  <div class="sp"></div>
  <a class="home" href="/ban-do"><i class="fa fa-map-location-dot"></i> Về bản đồ</a>
</div>

<div class="wrap">
  <div class="filterbar card">
    <div class="fld"><label>Đơn vị</label><select id="f-dv"><option value="">Tất cả đơn vị</option></select></div>
    <div class="fld"><label>Khu vực (phường/xã)</label><select id="f-kv"><option value="">Toàn khu đô thị</option></select></div>
    <div class="fld"><label>Từ năm</label><select id="f-tu"><option value="">—</option></select></div>
    <div class="fld"><label>Đến năm</label><select id="f-den"><option value="">—</option></select></div>
    <button class="btn btn-light" id="f-reset"><i class="fa fa-rotate-left"></i> Đặt lại</button>
  </div>
  <div class="hintbar"><i class="fa fa-hand-pointer"></i> Bấm vào bất kỳ con số hoặc thành phần biểu đồ để xem danh sách chi tiết liên quan.</div>

  <!-- Tổng quan -->
  <div class="sec">
    <div class="sec-h"><span class="bar"></span><span class="tt">Tổng quan hạng mục</span></div>
    <div class="cards six" id="kpi"></div>
  </div>

  <!-- Phân tích tổng hợp -->
  <div class="sec">
    <div class="sec-h"><span class="bar"></span><span class="tt">Phân tích tổng hợp</span></div>
    <div class="charts">
      <div class="chart card"><h4><i class="fa fa-layer-group" style="color:var(--brand-2)"></i> Số lượng theo hạng mục</h4><div class="cd">Bấm một phần để xem danh sách</div><div class="cv"><canvas id="c-module"></canvas></div></div>
      <div class="chart card"><h4><i class="fa fa-chart-line" style="color:var(--brand-2)"></i> Tiến độ theo năm</h4><div class="cd">Bấm một điểm để lọc theo năm</div><div class="cv"><canvas id="c-nam"></canvas></div></div>
      <div class="chart card"><h4><i class="fa fa-people-group" style="color:var(--brand-2)"></i> Theo đơn vị thực hiện</h4><div class="cd">Bấm một cột để xem danh sách</div><div class="cv"><canvas id="c-dv"></canvas></div></div>
      <div class="chart card"><h4><i class="fa fa-location-dot" style="color:var(--brand-2)"></i> Theo khu vực (phường/xã)</h4><div class="cd">Bấm một cột để xem danh sách</div><div class="cv"><canvas id="c-kv"></canvas></div></div>
    </div>
  </div>

  <!-- Module 01 -->
  <div class="sec">
    <div class="sec-h"><span class="bar" style="background:var(--m-cong_trinh)"></span><span class="tt">Module 01 · Công trình thanh niên & Check-in</span></div>
    <div class="charts">
      <div class="chart card"><h4><i class="fa fa-flag" style="color:var(--m-cong_trinh)"></i> Theo trạng thái</h4><div class="cd">Bấm một cột để xem danh sách</div><div class="cv sm"><canvas id="c-ct-tt"></canvas></div></div>
      <div class="chart card"><h4><i class="fa fa-shapes" style="color:var(--m-cong_trinh)"></i> Theo loại</h4><div class="cd">Bấm một phần để xem danh sách</div><div class="cv sm"><canvas id="c-ct-loai"></canvas></div></div>
    </div>
  </div>

  <!-- Module 02 -->
  <div class="sec">
    <div class="sec-h"><span class="bar" style="background:var(--m-an_toan)"></span><span class="tt">Module 02 · An toàn khu đô thị</span></div>
    <div class="cards" id="kpi-at" style="margin-bottom:18px"></div>
    <div class="charts"><div class="chart card"><h4><i class="fa fa-triangle-exclamation" style="color:var(--m-an_toan)"></i> Theo loại điểm</h4><div class="cd">Bấm một cột để xem danh sách</div><div class="cv sm"><canvas id="c-at-loai"></canvas></div></div>
      <div class="chart card"><h4><i class="fa fa-location-dot" style="color:var(--m-an_toan)"></i> Theo khu vực</h4><div class="cd">Số điểm an toàn theo phường/xã</div><div class="cv sm"><canvas id="c-at-kv"></canvas></div></div></div>
  </div>

  <!-- Module 04 -->
  <div class="sec">
    <div class="sec-h"><span class="bar" style="background:var(--m-truyen_thong)"></span><span class="tt">Module 03 · Truyền thông trực quan</span></div>
    <div class="cards" id="kpi-tt" style="margin-bottom:18px"></div>
    <div class="charts">
      <div class="chart card"><h4><i class="fa fa-bullhorn" style="color:var(--m-truyen_thong)"></i> Theo loại hình</h4><div class="cd">Bấm một phần để xem danh sách</div><div class="cv sm"><canvas id="c-tt-loai"></canvas></div></div>
      <div class="chart card"><h4><i class="fa fa-location-dot" style="color:var(--m-truyen_thong)"></i> Theo vị trí (phường/xã)</h4><div class="cd">Bấm một cột để xem danh sách</div><div class="cv sm"><canvas id="c-tt-kv"></canvas></div></div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal-ov" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <div class="modal-head"><div class="ico" id="m-ico"><i class="fa fa-list"></i></div>
      <div><div class="tt" id="m-title">Danh sách</div><div class="st" id="m-sub"></div></div>
      <button class="x" onclick="closeModal()"><i class="fa fa-xmark"></i></button></div>
    <div class="modal-body" id="m-body"></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
let CFG, charts={}, keys={};
const state={dv:'',tu:'',den:'',kv:''};
const PALETTE=['#1b52c0','#16a37f','#f59e0b','#8b5cf6','#ec4899','#0ea5e9','#14b8a6','#ef4444'];
const lblModule=k=>(CFG.modules[k]?.label_ngan)||k;
const lblLoai=(m,k)=>(CFG.modules[m]?.loai?.[k])||k;
const lblTT=k=>CFG.trang_thai[k]||k;
const mauModule=k=>CFG.modules[k]?.mau||CFG.brand.main;

async function boot(){
  CFG=await (await fetch('/api/cau-hinh')).json();
  Chart.defaults.font.family="'Inter',sans-serif"; Chart.defaults.color='#64748b'; Chart.defaults.font.size=12;
  const dv=await (await fetch('/api/don-vi')).json();
  dv.forEach(d=>document.getElementById('f-dv').insertAdjacentHTML('beforeend',`<option value="${d.id}">${d.ten}</option>`));
  const kv=await (await fetch('/api/khu-vuc')).json();
  kv.forEach(k=>document.getElementById('f-kv').insertAdjacentHTML('beforeend',`<option value="${k.fid}">${k.ten}</option>`));
  ['f-tu','f-den'].forEach(id=>{const s=document.getElementById(id);for(let y=2023;y<=2030;y++)s.insertAdjacentHTML('beforeend',`<option>${y}</option>`);});
  const on=(id,key)=>document.getElementById(id).onchange=e=>{state[key]=e.target.value;load();};
  on('f-dv','dv');on('f-kv','kv');on('f-tu','tu');on('f-den','den');
  document.getElementById('f-reset').onclick=()=>{Object.assign(state,{dv:'',tu:'',den:'',kv:''});['f-dv','f-kv','f-tu','f-den'].forEach(i=>document.getElementById(i).value='');load();};
  document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal();});
  load();
}

function thQs(){const p=new URLSearchParams();if(state.dv)p.set('don_vi',state.dv);if(state.kv)p.set('kv',state.kv);if(state.tu)p.set('tu_nam',state.tu);if(state.den)p.set('den_nam',state.den);return p.toString();}

function kpiCard(label,val,icon,color,filter){
  return `<div class="card kpi click" data-f='${JSON.stringify(filter)}' data-c="${color}" data-i="${icon}" data-l="${label}">
    <i class="fa ${icon} ic" style="color:${color}"></i><div class="n" style="color:${color}">${val}</div><div class="l">${label}</div></div>`;
}
function wireKpi(box){box.querySelectorAll('.kpi.click').forEach(el=>{
  el.onclick=()=>openModal(el.dataset.l,el.dataset.c,el.dataset.i,JSON.parse(el.dataset.f));});}

async function load(){
  const d=await (await fetch('/api/tong-hop?'+thQs())).json();
  const th=d.the, mc=CFG.modules;
  // KPI tổng quan
  const box=document.getElementById('kpi');
  box.innerHTML=[
    kpiCard('Công trình thanh niên',th.cong_trinh,'fa-flag',mauModule('cong_trinh'),{module:'cong_trinh'}),
    kpiCard('Điểm check-in',th.check_in,'fa-location-dot','#0ea5e9',{module:'cong_trinh',loai:'check_in'}),
    kpiCard('Điểm an toàn',th.an_toan,'fa-triangle-exclamation',mauModule('an_toan'),{module:'an_toan'}),
    kpiCard('Sản phẩm truyền thông',th.truyen_thong,'fa-bullhorn',mauModule('truyen_thong'),{module:'truyen_thong'}),
    kpiCard('Pano',th.pano,'fa-rectangle-ad','#8b5cf6',{module:'truyen_thong',loai:'pano'}),
    kpiCard('Đã hoàn thành',th.hoan_thanh,'fa-circle-check','#16a37f',{trang_thai:'hoan_thanh'}),
  ].join('');
  wireKpi(box);

  // Tổng hợp
  keys.module=d.theo_module.map(r=>r.module);
  doughnut('c-module',d.theo_module.map(r=>lblModule(r.module)),d.theo_module.map(r=>+r.so),d.theo_module.map(r=>mauModule(r.module)),
    i=>openModal(lblModule(keys.module[i]),mauModule(keys.module[i]),mc[keys.module[i]]?.icon||'fa-layer-group',{module:keys.module[i]}));
  keys.nam=d.theo_nam.map(r=>r.nam);
  line('c-nam',d.theo_nam.map(r=>r.nam),d.theo_nam.map(r=>+r.so),i=>openModal('Năm '+keys.nam[i],CFG.brand.main,'fa-calendar',{nam:keys.nam[i]}));
  keys.dv=d.theo_don_vi.map(r=>r.id);
  bar('c-dv',d.theo_don_vi.map(r=>r.ten||'Chưa gán'),d.theo_don_vi.map(r=>+r.so),d.theo_don_vi.map(()=>CFG.brand.alt),
    i=>keys.dv[i]&&openModal(d.theo_don_vi[i].ten,CFG.brand.main,'fa-people-group',{don_vi:keys.dv[i]}),true);
  keys.kv=d.theo_khu_vuc.map(r=>r.fid);
  bar('c-kv',d.theo_khu_vuc.map(r=>r.ten),d.theo_khu_vuc.map(r=>+r.so),d.theo_khu_vuc.map(()=>'#14b8a6'),
    i=>openModal(d.theo_khu_vuc[i].ten,'#14b8a6','fa-location-dot',{kv:keys.kv[i]}),true);

  // Module 01
  keys.cttt=d.theo_trang_thai.map(r=>r.trang_thai);
  bar('c-ct-tt',d.theo_trang_thai.map(r=>lblTT(r.trang_thai)),d.theo_trang_thai.map(r=>+r.so),d.theo_trang_thai.map(r=>CFG.mau_trang_thai[r.trang_thai]||'#888'),
    i=>openModal('Công trình: '+lblTT(keys.cttt[i]),CFG.mau_trang_thai[keys.cttt[i]],'fa-flag',{module:'cong_trinh',trang_thai:keys.cttt[i]}));
  keys.ctloai=d.theo_loai_ct.map(r=>r.loai);
  doughnut('c-ct-loai',d.theo_loai_ct.map(r=>lblLoai('cong_trinh',r.loai)),d.theo_loai_ct.map(r=>+r.so),PALETTE,
    i=>openModal('Công trình: '+lblLoai('cong_trinh',keys.ctloai[i]),mauModule('cong_trinh'),'fa-shapes',{module:'cong_trinh',loai:keys.ctloai[i]}));

  // Module 02
  const at=d.an_toan, atBox=document.getElementById('kpi-at');
  atBox.innerHTML=[
    kpiCard('Điểm cảnh báo',at.canh_bao,'fa-triangle-exclamation',mauModule('an_toan'),{module:'an_toan',loai:'bien_canh_bao,diem_nguy_hiem,ho_da'}),
    kpiCard('Biển cảnh báo',at.bien_canh_bao,'fa-sign-hanging','#f97316',{module:'an_toan',loai:'bien_canh_bao'}),
    kpiCard('Điểm PCCC',at.pccc,'fa-fire-extinguisher','#dc2626',{module:'an_toan',loai:'diem_pccc'}),
    kpiCard('Camera an ninh',at.camera,'fa-video','#6366f1',{module:'an_toan',loai:'camera'}),
  ].join('');
  wireKpi(atBox);
  keys.atloai=d.theo_loai_at.map(r=>r.loai);
  bar('c-at-loai',d.theo_loai_at.map(r=>lblLoai('an_toan',r.loai)),d.theo_loai_at.map(r=>+r.so),d.theo_loai_at.map(()=>mauModule('an_toan')),
    i=>openModal('An toàn: '+lblLoai('an_toan',keys.atloai[i]),mauModule('an_toan'),'fa-triangle-exclamation',{module:'an_toan',loai:keys.atloai[i]}),true);
  drawAtKv(d);

  // Module 04
  const ttBox=document.getElementById('kpi-tt');
  ttBox.innerHTML=[
    kpiCard('Tổng sản phẩm',th.truyen_thong,'fa-bullhorn',mauModule('truyen_thong'),{module:'truyen_thong'}),
    kpiCard('Pano',th.pano,'fa-rectangle-ad','#8b5cf6',{module:'truyen_thong',loai:'pano'}),
  ].join('');
  wireKpi(ttBox);
  keys.ttloai=d.theo_loai_tt.map(r=>r.loai);
  doughnut('c-tt-loai',d.theo_loai_tt.map(r=>lblLoai('truyen_thong',r.loai)),d.theo_loai_tt.map(r=>+r.so),PALETTE,
    i=>openModal('Truyền thông: '+lblLoai('truyen_thong',keys.ttloai[i]),mauModule('truyen_thong'),'fa-bullhorn',{module:'truyen_thong',loai:keys.ttloai[i]}));
  keys.ttkv=d.tt_theo_khu_vuc.map(r=>r.fid);
  bar('c-tt-kv',d.tt_theo_khu_vuc.map(r=>r.ten),d.tt_theo_khu_vuc.map(r=>+r.so),d.tt_theo_khu_vuc.map(()=>mauModule('truyen_thong')),
    i=>openModal('Truyền thông tại '+d.tt_theo_khu_vuc[i].ten,mauModule('truyen_thong'),'fa-location-dot',{module:'truyen_thong',kv:keys.ttkv[i]}),true);
}

function drawAtKv(d){
  // An toàn theo khu vực = tổng theo_khu_vuc trừ các module khác? Không có sẵn -> gọi API riêng nhẹ.
  const p=new URLSearchParams();if(state.dv)p.set('don_vi',state.dv);
  Promise.all(d.theo_khu_vuc.map(async r=>{
    const q=new URLSearchParams(p);q.set('module','an_toan');q.set('kv',r.fid);
    const g=await (await fetch('/api/diem?'+q)).json();return {ten:r.ten,fid:r.fid,so:g.total};
  })).then(rows=>{
    keys.atkv=rows.map(r=>r.fid);
    bar('c-at-kv',rows.map(r=>r.ten),rows.map(r=>r.so),rows.map(()=>mauModule('an_toan')),
      i=>openModal('An toàn tại '+rows[i].ten,mauModule('an_toan'),'fa-location-dot',{module:'an_toan',kv:keys.atkv[i]}),true);
  });
}

function reset(id){if(charts[id])charts[id].destroy();return document.getElementById(id);}
function ck(fn){return (e,els)=>{if(fn&&els.length)fn(els[0].index);};}
function hover(){return (e,el)=>{if(e.native)e.native.target.style.cursor=el.length?'pointer':'default';};}
function doughnut(id,labels,data,colors,fn){charts[id]=new Chart(reset(id),{type:'doughnut',
  data:{labels,datasets:[{data,backgroundColor:colors,borderWidth:3,borderColor:'#fff',hoverOffset:9}]},
  options:{responsive:true,maintainAspectRatio:false,cutout:'56%',onClick:ck(fn),onHover:hover(),
    plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11.5}}}}}});}
function bar(id,labels,data,colors,fn,horiz){charts[id]=new Chart(reset(id),{type:'bar',
  data:{labels,datasets:[{data,backgroundColor:colors,borderRadius:7,maxBarThickness:52}]},
  options:{indexAxis:horiz?'y':'x',responsive:true,maintainAspectRatio:false,onClick:ck(fn),onHover:hover(),
    plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{precision:0},grid:{color:'#eef2f7'}},y:{beginAtZero:true,ticks:{precision:0},grid:{display:!horiz?false:false,color:'#eef2f7'}}}}});}
function line(id,labels,data,fn){charts[id]=new Chart(reset(id),{type:'line',
  data:{labels,datasets:[{data,borderColor:CFG.brand.alt,backgroundColor:'rgba(27,82,192,.12)',fill:true,tension:.35,pointRadius:5,pointHoverRadius:9,pointBackgroundColor:CFG.brand.main,borderWidth:3}]},
  options:{responsive:true,maintainAspectRatio:false,onClick:ck(fn),onHover:hover(),
    plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0},grid:{color:'#eef2f7'}},x:{grid:{display:false}}}}});}

async function openModal(title,color,icon,filter){
  const mo=document.getElementById('modal');
  document.getElementById('m-ico').style.background=color;
  document.getElementById('m-ico').innerHTML=`<i class="fa ${icon}"></i>`;
  document.getElementById('m-title').textContent=title;
  document.getElementById('m-sub').textContent='Đang tải…';
  document.getElementById('m-body').innerHTML='<div style="display:flex;justify-content:center;padding:34px"><div class="spin"></div></div>';
  mo.classList.add('open');
  const p=new URLSearchParams();
  if(state.dv)p.set('don_vi',state.dv);
  if(state.kv)p.set('kv',state.kv);
  for(const [k,v] of Object.entries(filter)) p.set(k,v);
  const gj=await (await fetch('/api/diem?'+p.toString())).json();
  const feats=gj.features;
  document.getElementById('m-sub').textContent=feats.length+' đối tượng';
  if(!feats.length){document.getElementById('m-body').innerHTML='<div class="mempty"><i class="fa fa-inbox" style="font-size:26px;display:block;margin-bottom:8px"></i>Không có đối tượng phù hợp.</div>';return;}
  document.getElementById('m-body').innerHTML='<div class="mlist">'+feats.map(f=>{
    const pr=f.properties, mm=CFG.modules[pr.module];
    const sub=[lblModule(pr.module),pr.loai_label,pr.nam,pr.trang_thai_label,pr.don_vi].filter(Boolean).join(' · ');
    return `<a class="mrow" href="/chi-tiet/${pr.id}" target="_blank"><span class="mk" style="background:${mm?.mau||'#888'}"><i class="fa ${mm?.icon||'fa-map-marker-alt'}"></i></span>
      <span class="mi"><div class="t">${pr.ten}</div><div class="s">${sub}</div></span><span class="go">Chi tiết ↗</span></a>`;
  }).join('')+'</div>';
}
function closeModal(){document.getElementById('modal').classList.remove('open');}
boot();
</script>
</body>
</html>
