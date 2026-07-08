<?php
/** @var yii\web\View $this */
$this->title = 'Bản đồ số Khu đô thị ĐHQG-HCM';
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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/dhqg.css"/>
<style>
html,body{height:100%;overflow:hidden}
:root{--top:58px;--side:300px}
#topbar{position:fixed;top:0;left:0;right:0;height:var(--top);background:#fff;border-bottom:1px solid var(--line);
  display:flex;align-items:center;gap:14px;padding:0 16px;z-index:1100;box-shadow:var(--shadow-sm)}
.brand{display:flex;align-items:center;gap:11px}
.emblem{width:46px;height:46px;border-radius:11px;background:#fff;display:flex;align-items:center;justify-content:center;
  padding:5px;border:1px solid var(--line);box-shadow:var(--shadow-sm);flex-shrink:0}
.emblem img{max-width:100%;max-height:100%;object-fit:contain}
.brand h1{font-size:15px;font-weight:800;letter-spacing:-.3px;line-height:1.15}
.brand p{font-size:10.5px;color:var(--gray);font-weight:500}
#search{display:flex;align-items:center;gap:8px;background:var(--bg);border:1.5px solid transparent;border-radius:22px;
  padding:0 14px;height:38px;flex:1;max-width:440px;transition:.2s}
#search:focus-within{background:#fff;border-color:var(--brand-2);box-shadow:0 0 0 3px var(--ring)}
#search input{border:none;background:transparent;outline:none;flex:1;font-size:13.5px;color:var(--ink)}
#search i{color:var(--gray-400)}
.tb-sp{flex:1}
#sidebar{position:fixed;top:var(--top);left:0;bottom:0;width:var(--side);background:#fff;border-right:1px solid var(--line);overflow-y:auto;z-index:1000;padding:16px}
.sec{margin-bottom:20px}
.sec h3{font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--gray);margin-bottom:10px;font-weight:700}
.lyr{display:flex;align-items:center;gap:11px;padding:10px 12px;border:1px solid var(--line);border-radius:12px;margin-bottom:8px;cursor:pointer;transition:.15s}
.lyr:hover{background:var(--bg);border-color:#cbd5e1}
.lyr.off{opacity:.4}
.lyr .dot{width:13px;height:13px;border-radius:50%;flex-shrink:0;box-shadow:0 0 0 3px rgba(0,0,0,.04)}
.lyr .nm{flex:1;font-weight:600;font-size:13px}
.lyr .ct{font-size:12px;color:#fff;font-weight:700;background:var(--brand);min-width:24px;text-align:center;padding:2px 7px;border-radius:20px}
.fld{margin-bottom:11px}
.fld label{display:block;font-size:12px;color:var(--gray);margin-bottom:5px;font-weight:600}
.fld select{width:100%;padding:9px 11px;border:1px solid var(--line);border-radius:var(--radius-sm);font-size:13px;background:#fff;color:var(--ink)}
.fld select:focus{outline:none;border-color:var(--brand-2);box-shadow:0 0 0 3px var(--ring)}
.chk{display:flex;align-items:center;gap:9px;font-size:13px;padding:7px 0;cursor:pointer;font-weight:500}
.chk input{accent-color:var(--brand-2);width:16px;height:16px}
#map{position:fixed;top:var(--top);left:var(--side);right:0;bottom:0}
#detail{position:fixed;top:calc(var(--top) + 16px);right:16px;width:352px;max-height:calc(100% - var(--top) - 32px);
  background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);z-index:1200;overflow:auto;display:none}
#detail.show{display:block}
.d-hero{height:158px;background:var(--brand-grad);position:relative;display:flex;align-items:flex-end;padding:14px;color:#fff}
.d-hero img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.d-hero::after{content:"";position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55),rgba(0,0,0,0) 60%)}
.d-hero .cap{position:relative;z-index:2}
.d-hero .cap .t{font-size:17px;font-weight:800;line-height:1.2}
.d-hero .cap .s{font-size:12px;opacity:.92;margin-top:3px}
.d-x{position:absolute;top:12px;right:12px;z-index:3;width:32px;height:32px;border-radius:50%;background:rgba(0,0,0,.4);color:#fff;border:none;cursor:pointer}
.d-x:hover{background:rgba(0,0,0,.6)}
.d-body{padding:16px}
.d-row{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px solid var(--line-2);font-size:13px}
.d-row .k{color:var(--gray)}
.d-row .v{font-weight:600;text-align:right}
.gal{display:flex;gap:7px;flex-wrap:wrap;margin-top:10px}
.gal img{width:74px;height:56px;object-fit:cover;border-radius:8px;cursor:pointer;border:1px solid var(--line)}
.qr-box{text-align:center;margin-top:16px;padding-top:14px;border-top:1px dashed var(--line)}
.qr-box>div{display:flex;justify-content:center}
.qr-box .lbl{font-size:11px;color:var(--gray);margin-top:8px}
.legend{position:fixed;bottom:18px;left:calc(var(--side) + 16px);background:#fff;border-radius:12px;box-shadow:var(--shadow);padding:11px 14px;z-index:900;font-size:12px}
.legend .h{font-weight:700;margin-bottom:6px;color:var(--ink)}
.legend .it{display:flex;align-items:center;gap:8px;margin:4px 0;color:var(--gray-700)}
.legend .it .dot{width:11px;height:11px;border-radius:50%}
.mk{border:2px solid #fff;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,.35);width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px}
.px-lbl{background:none;border:none}
.px-lbl span{display:inline-block;transform:translate(-50%,-50%);white-space:nowrap;font-size:11.5px;font-weight:700;color:var(--brand-d);
  text-shadow:0 1px 3px #fff,0 -1px 3px #fff,1px 0 3px #fff,-1px 0 3px #fff;letter-spacing:.2px}
.px-tip{font-size:12.5px;line-height:1.5}
.px-tip b{color:var(--brand-d)}
#load{position:fixed;inset:0;background:#fff;display:flex;align-items:center;justify-content:center;z-index:2000;flex-direction:column;gap:14px;color:var(--gray)}
.mob-btn{display:none}
@media(max-width:768px){#sidebar{transform:translateX(-100%);transition:.25s;box-shadow:var(--shadow-lg)}#sidebar.open{transform:none}#map{left:0}.legend{left:16px}.mob-btn{display:inline-flex}#detail{width:calc(100% - 32px)}}
</style>
</head>
<body>
<div id="load"><div class="spin"></div><div>Đang tải bản đồ…</div></div>

<div id="topbar">
  <button class="btn btn-light btn-sm mob-btn" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fa fa-bars"></i></button>
  <div class="brand"><div class="emblem"><img src="/img/logo-mark-160.png" alt="ĐHQG-HCM"></div>
    <div><h1>Bản đồ số Khu đô thị</h1><p>Đại học Quốc gia TP. Hồ Chí Minh</p></div></div>
  <div id="search"><i class="fa fa-search"></i><input id="q" placeholder="Tìm công trình, địa điểm, mã…"></div>
  <div class="tb-sp"></div>
  <a class="btn btn-primary btn-sm" href="/dashboard"><i class="fa fa-chart-pie"></i> Dashboard</a>
</div>

<div id="sidebar">
  <div class="sec">
    <h3>Lớp dữ liệu</h3>
    <div id="layers"></div>
  </div>
  <div class="sec">
    <h3>Bộ lọc</h3>
    <div class="fld"><label>Năm thực hiện</label><select id="f-nam"><option value="">Tất cả các năm</option></select></div>
    <div class="fld"><label>Đơn vị</label><select id="f-dv"><option value="">Tất cả đơn vị</option></select></div>
    <div class="fld"><label>Trạng thái (công trình)</label><select id="f-tt"><option value="">Tất cả trạng thái</option></select></div>
  </div>
  <div class="sec">
    <h3>Nền bản đồ</h3>
    <label class="chk"><input type="checkbox" id="t-ranh" checked> Ranh giới khu đô thị</label>
    <label class="chk"><input type="checkbox" id="t-phuongxa"> Ranh giới phường/xã</label>
    <label class="chk"><input type="checkbox" id="t-phankhu"> Phân khu / trường thành viên</label>
  </div>
</div>

<div id="map"></div>
<div class="legend" id="legend"></div>

<div id="detail">
  <div class="d-hero"><button class="d-x" onclick="closeDetail()"><i class="fa fa-xmark"></i></button>
    <div class="cap"><div class="t" id="d-ten"></div><div class="s" id="d-loai"></div></div></div>
  <div class="d-body" id="d-body"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
<script>
let CFG, map, layers={}, active={}, ranhLayer, phanKhuLayer, pxLayer, pxLabels;
const state={q:'',nam:'',dv:'',tt:''};

function mkIcon(color){
  return L.divIcon({className:'',html:`<div class="mk" style="background:${color}"><i class="fa fa-map-marker-alt"></i></div>`,iconSize:[24,24],iconAnchor:[12,12]});
}

async function init(){
  CFG = await (await fetch('/api/cau-hinh')).json();
  map = L.map('map',{zoomControl:false}).setView(CFG.center, CFG.zoom);
  L.control.zoom({position:'bottomright'}).addTo(map);
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{attribution:'© OpenStreetMap, © CARTO',subdomains:'abcd',maxZoom:20}).addTo(map);

  const lyrBox=document.getElementById('layers'), leg=document.getElementById('legend');
  leg.innerHTML='<div class="h">Chú giải lớp</div>';
  for(const [k,m] of Object.entries(CFG.modules)){
    active[k]=true;
    layers[k]=L.markerClusterGroup({maxClusterRadius:46,iconCreateFunction:c=>L.divIcon({html:`<div class="mk" style="background:${m.mau};width:36px;height:36px;font-size:13px;font-weight:700">${c.getChildCount()}</div>`,className:'',iconSize:[36,36]})});
    map.addLayer(layers[k]);
    lyrBox.insertAdjacentHTML('beforeend',
      `<div class="lyr" data-m="${k}" onclick="toggleModule('${k}')"><span class="dot" style="background:${m.mau}"></span><span class="nm">${m.label_ngan}</span><span class="ct" id="ct-${k}" style="background:${m.mau}">0</span></div>`);
    leg.insertAdjacentHTML('beforeend',`<div class="it"><span class="dot" style="background:${m.mau}"></span>${m.label_ngan}</div>`);
  }

  const nam=document.getElementById('f-nam');
  for(let y=2023;y<=2030;y++) nam.insertAdjacentHTML('beforeend',`<option>${y}</option>`);
  const tt=document.getElementById('f-tt');
  for(const [k,v] of Object.entries(CFG.trang_thai)) tt.insertAdjacentHTML('beforeend',`<option value="${k}">${v}</option>`);
  const dv=await (await fetch('/api/don-vi')).json();
  const dvSel=document.getElementById('f-dv');
  dv.forEach(d=>dvSel.insertAdjacentHTML('beforeend',`<option value="${d.id}">${d.ten}</option>`));

  document.getElementById('q').addEventListener('input',e=>{state.q=e.target.value;deb(loadDiem)});
  document.getElementById('f-nam').addEventListener('change',e=>{state.nam=e.target.value;loadDiem()});
  document.getElementById('f-dv').addEventListener('change',e=>{state.dv=e.target.value;loadDiem()});
  document.getElementById('f-tt').addEventListener('change',e=>{state.tt=e.target.value;loadDiem()});
  document.getElementById('t-ranh').addEventListener('change',e=>{e.target.checked?map.addLayer(ranhLayer):map.removeLayer(ranhLayer);});
  document.getElementById('t-phuongxa').addEventListener('change',e=>togglePhuongXa(e.target.checked));
  document.getElementById('t-phankhu').addEventListener('change',e=>togglePhanKhu(e.target.checked));

  await loadRanh();
  await loadDiem();

  // Bật sẵn lớp theo URL (chia sẻ link), vd /ban-do?px=1 hoặc ?pk=1
  const usp=new URLSearchParams(location.search);
  if(usp.get('px')==='1'){document.getElementById('t-phuongxa').checked=true;await togglePhuongXa(true);}
  if(usp.get('pk')==='1'){document.getElementById('t-phankhu').checked=true;await togglePhanKhu(true);}

  document.getElementById('load').style.display='none';
}

let debT; function deb(fn){clearTimeout(debT);debT=setTimeout(fn,320);}

async function loadRanh(){
  const gj=await (await fetch('/api/ranh-khu')).json();
  ranhLayer=L.geoJSON(gj,{style:{color:CFG.brand.main,weight:2.5,fillColor:CFG.brand.alt,fillOpacity:.05,dashArray:'6,4'}}).addTo(map);
  if(gj.features.length) map.fitBounds(ranhLayer.getBounds(),{padding:[36,36]});
}

async function togglePhuongXa(on){
  if(on){
    if(!pxLayer){
      const gj=await (await fetch('/api/phuong-xa')).json();
      const max=Math.max(1,...gj.features.map(f=>f.properties.so_dt));
      pxLayer=L.geoJSON(gj,{
        style:f=>({color:CFG.brand.main,weight:1.6,fillColor:CFG.brand.alt,fillOpacity:0.07+0.30*(f.properties.so_dt/max)}),
        onEachFeature:(f,l)=>{
          const p=f.properties;
          const ds=p.dan_so!=null?(p.dan_so*1000).toLocaleString('vi-VN'):'—';
          l.bindTooltip(`<div class="px-tip"><b>${p.ten}</b><br>Dân số: ${ds} người · DT: ${p.dien_tich??'—'} km²<br>Đối tượng quản lý: <b>${p.so_dt}</b></div>`,{sticky:true});
          l.on('mouseover',()=>l.setStyle({weight:3,fillOpacity:0.45}));
          l.on('mouseout',()=>pxLayer.resetStyle(l));
        }
      });
      pxLabels=L.layerGroup(gj.features.map(f=>{
        const c=L.geoJSON(f).getBounds().getCenter();
        return L.marker(c,{interactive:false,icon:L.divIcon({className:'px-lbl',html:`<span>${f.properties.ten.replace('Phường ','P. ').replace('Xã ','X. ')}</span>`})});
      }));
    }
    map.addLayer(pxLayer); map.addLayer(pxLabels);
  }else{ if(pxLayer)map.removeLayer(pxLayer); if(pxLabels)map.removeLayer(pxLabels); }
}

async function togglePhanKhu(on){
  if(on){
    if(!phanKhuLayer){
      const gj=await (await fetch('/api/phan-khu')).json();
      phanKhuLayer=L.geoJSON(gj,{style:{color:CFG.brand.alt,weight:1.5,fillColor:CFG.brand.alt,fillOpacity:.08},
        onEachFeature:(f,l)=>l.bindTooltip(f.properties.ten,{sticky:true})});
    }
    map.addLayer(phanKhuLayer);
  }else if(phanKhuLayer) map.removeLayer(phanKhuLayer);
}

function qs(){
  const p=new URLSearchParams();
  if(state.q)p.set('q',state.q); if(state.nam)p.set('nam',state.nam);
  if(state.dv)p.set('don_vi',state.dv); if(state.tt)p.set('trang_thai',state.tt);
  return p.toString();
}

async function loadDiem(){
  const gj=await (await fetch('/api/diem?'+qs())).json();
  const cnt={}; Object.keys(layers).forEach(k=>{layers[k].clearLayers();cnt[k]=0;});
  gj.features.forEach(f=>{
    const p=f.properties, m=p.module; if(!layers[m])return;
    const c=f.geometry.coordinates;
    const mk=L.marker([c[1],c[0]],{icon:mkIcon(CFG.modules[m].mau)});
    mk.on('click',()=>showDetail(p.id));
    layers[m].addLayer(mk); cnt[m]++;
  });
  for(const k in cnt){const el=document.getElementById('ct-'+k); if(el)el.textContent=cnt[k];}
}

function toggleModule(k){
  active[k]=!active[k];
  document.querySelector(`.lyr[data-m="${k}"]`).classList.toggle('off',!active[k]);
  active[k]?map.addLayer(layers[k]):map.removeLayer(layers[k]);
}

async function showDetail(id){
  const d=await (await fetch('/api/diem-chi-tiet?id='+id)).json();
  if(!d.ok)return;
  const m=CFG.modules[d.module], hero=document.querySelector('.d-hero');
  hero.style.background=m.mau;
  hero.querySelector('img')?.remove();
  if(d.anh.length){const im=document.createElement('img');im.src=d.anh[0].url;hero.prepend(im);}
  document.getElementById('d-ten').textContent=d.ten;
  document.getElementById('d-loai').textContent=m.label_ngan+(d.loai_label?' · '+d.loai_label:'');
  const tt=d.trang_thai?`<div class="d-row"><span class="k">Trạng thái</span><span class="v"><span class="badge" style="background:${(CFG.mau_trang_thai[d.trang_thai]||'#888')}22;color:${CFG.mau_trang_thai[d.trang_thai]||'#888'}"><span class="dot"></span>${d.trang_thai_label}</span></span></div>`:'';
  let g=''; if(d.anh.length){g='<div class="gal">'+d.anh.map(a=>`<img src="${a.url}" onclick="window.open('${a.url}')">`).join('')+'</div>';}
  document.getElementById('d-body').innerHTML=`
    ${d.ma?`<div class="d-row"><span class="k">Mã</span><span class="v">${d.ma}</span></div>`:''}
    ${d.nam?`<div class="d-row"><span class="k">Năm</span><span class="v">${d.nam}</span></div>`:''}
    ${tt}
    ${d.don_vi_thuc_hien?`<div class="d-row"><span class="k">Đơn vị thực hiện</span><span class="v">${d.don_vi_thuc_hien}</span></div>`:''}
    ${d.don_vi_quan_ly?`<div class="d-row"><span class="k">Đơn vị quản lý</span><span class="v">${d.don_vi_quan_ly}</span></div>`:''}
    ${d.mo_ta?`<div style="margin-top:10px;font-size:13px;color:var(--gray-700);line-height:1.55">${d.mo_ta}</div>`:''}
    ${d.noi_dung?`<div style="margin-top:8px;font-size:13px;color:var(--gray-700);line-height:1.55"><b>Nội dung:</b> ${d.noi_dung}</div>`:''}
    ${g}
    <div class="qr-box"><div id="qr"></div><div class="lbl">Quét mã QR để xem trang công khai</div>
      <a href="/chi-tiet/${d.id}" target="_blank" style="font-size:12.5px;color:${m.mau};font-weight:700">Mở trang chi tiết ↗</a></div>`;
  document.getElementById('qr').innerHTML='';
  new QRCode(document.getElementById('qr'),{text:location.origin+'/chi-tiet/'+d.id,width:118,height:118,colorDark:CFG.brand.dark});
  document.getElementById('detail').classList.add('show');
}
function closeDetail(){document.getElementById('detail').classList.remove('show');}

init();
</script>
</body>
</html>
