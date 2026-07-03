<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\Dhqg;

/** @var yii\web\View $this */
/** @var string $module */
/** @var array $cfg */
/** @var array $data */
/** @var array $errors */
/** @var bool $isNew */
/** @var int|null $id */
/** @var array $anh */
/** @var array $dsDonVi */

$this->title = ($isNew ? 'Thêm — ' : 'Sửa — ') . $cfg['label'];
$mau = $cfg['mau'];
$v = fn($k, $d = '') => Html::encode($data[$k] ?? $d);
$err = fn($k) => isset($errors[$k]) ? '<div style="color:#dc2626;font-size:12px;margin-top:3px">' . Html::encode($errors[$k]) . '</div>' : '';
?>
<style>
.f-wrap{max-width:1000px}
.f-grid{display:grid;grid-template-columns:1.1fr 1fr;gap:16px}
.f-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:14px}
.f-card h3{font-size:13px;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;margin-bottom:12px}
.f-row{margin-bottom:12px}
.f-row label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px}
.f-row input,.f-row select,.f-row textarea{width:100%;padding:9px 11px;border:1px solid #d1d5db;border-radius:8px;font-size:13px}
.f-2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.btn{padding:10px 16px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:7px;border:none;cursor:pointer}
.btn-primary{background:<?= $mau ?>;color:#fff}
.btn-light{background:#f3f4f6;color:#374151}
.gal{display:flex;flex-wrap:wrap;gap:10px}
.gal .it{position:relative;width:110px}
.gal .it img{width:110px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}
.gal .it .tag{font-size:10px;color:#6b7280;text-align:center;margin-top:2px}
.gal .it button{position:absolute;top:-6px;right:-6px;width:22px;height:22px;border-radius:50%;background:#ef4444;color:#fff;border:none;cursor:pointer;font-size:11px}
</style>

<div class="f-wrap">
  <h2 style="font-size:20px;font-weight:700;margin-bottom:4px"><i class="fa <?= $cfg['icon'] ?>" style="color:<?= $mau ?>"></i> <?= Html::encode($this->title) ?></h2>
  <p style="color:#9ca3af;font-size:13px;margin-bottom:16px"><?= Html::encode($cfg['label']) ?></p>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
    <input type="hidden" name="lat" id="inp-lat" value="<?= $v('lat') ?>">
    <input type="hidden" name="lng" id="inp-lng" value="<?= $v('lng') ?>">

    <div class="f-grid">
      <div>
        <div class="f-card">
          <h3>Thông tin chung</h3>
          <div class="f-2">
            <div class="f-row"><label>Mã</label><input name="ma" value="<?= $v('ma') ?>"></div>
            <div class="f-row"><label>Năm thực hiện</label><input name="nam" type="number" value="<?= $v('nam') ?>"><?= $err('nam') ?></div>
          </div>
          <div class="f-row"><label>Tên <span style="color:#ef4444">*</span></label><input name="ten" value="<?= $v('ten') ?>"><?= $err('ten') ?></div>
          <div class="f-2">
            <div class="f-row">
              <label>Loại</label>
              <select name="loai">
                <?php foreach ($cfg['loai'] as $k => $lab): ?>
                  <option value="<?= $k ?>" <?= ($data['loai'] ?? '') === $k ? 'selected' : '' ?>><?= Html::encode($lab) ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <?php if ($cfg['co_trang_thai']): ?>
            <div class="f-row">
              <label>Trạng thái</label>
              <select name="trang_thai">
                <?php foreach (Dhqg::TRANG_THAI as $k => $lab): ?>
                  <option value="<?= $k ?>" <?= ($data['trang_thai'] ?? '') === $k ? 'selected' : '' ?>><?= Html::encode($lab) ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <?php endif ?>
          </div>
          <div class="f-2">
            <div class="f-row">
              <label>Đơn vị thực hiện</label>
              <select name="don_vi_thuc_hien_id">
                <option value="">—</option>
                <?php foreach ($dsDonVi as $dv): ?>
                  <option value="<?= $dv['id'] ?>" <?= (string)($data['don_vi_thuc_hien_id'] ?? '') === (string)$dv['id'] ? 'selected' : '' ?>><?= Html::encode($dv['ten']) ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="f-row">
              <label>Đơn vị quản lý</label>
              <select name="don_vi_quan_ly_id">
                <option value="">—</option>
                <?php foreach ($dsDonVi as $dv): ?>
                  <option value="<?= $dv['id'] ?>" <?= (string)($data['don_vi_quan_ly_id'] ?? '') === (string)$dv['id'] ? 'selected' : '' ?>><?= Html::encode($dv['ten']) ?></option>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="f-row"><label>Mô tả</label><textarea name="mo_ta" rows="3"><?= $v('mo_ta') ?></textarea></div>
          <?php if ($module === 'truyen_thong'): ?>
          <div class="f-row"><label>Nội dung tuyên truyền</label><textarea name="noi_dung" rows="3"><?= $v('noi_dung') ?></textarea></div>
          <?php endif ?>
        </div>

        <div class="f-card">
          <h3>Hình ảnh</h3>
          <?php if ($anh): ?>
            <div class="gal" style="margin-bottom:12px">
              <?php foreach ($anh as $a): ?>
                <div class="it">
                  <img src="<?= Html::encode(Dhqg::anhUrl($a['url'])) ?>" alt="">
                  <div class="tag"><?= Html::encode(Dhqg::LOAI_ANH[$a['loai_anh']] ?? '') ?></div>
                  <?= Html::beginForm(['xoa-anh', 'id' => $a['id']], 'post', ['style' => 'display:inline']) ?>
                    <button type="submit" onclick="return confirm('Xoá ảnh này?')" title="Xoá">×</button>
                  <?= Html::endForm() ?>
                </div>
              <?php endforeach ?>
            </div>
          <?php endif ?>
          <div class="f-2">
            <div class="f-row">
              <label>Thêm ảnh (chọn nhiều)</label>
              <input type="file" name="anh_moi[]" accept="image/*" multiple>
            </div>
            <div class="f-row">
              <label>Loại ảnh</label>
              <select name="loai_anh_moi">
                <?php foreach (Dhqg::LOAI_ANH as $k => $lab): ?>
                  <option value="<?= $k ?>"><?= Html::encode($lab) ?></option>
                <?php endforeach ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div>
        <div class="f-card">
          <h3>Vị trí trên bản đồ</h3>
          <div class="f-2">
            <div class="f-row"><label>Vĩ độ (lat)</label><input id="manual-lat" value="<?= $v('lat') ?>" onchange="setManualCoords()"><?= $err('lat') ?></div>
            <div class="f-row"><label>Kinh độ (lng)</label><input id="manual-lng" value="<?= $v('lng') ?>" onchange="setManualCoords()"><?= $err('lng') ?></div>
          </div>
          <div id="pick-map" style="height:340px;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb"></div>
          <div id="map-hint" style="margin-top:8px;padding:8px;background:#f0fdf9;border-radius:8px;font-size:12px;color:<?= $mau ?>">
            <i class="fa fa-circle-info"></i> Click lên bản đồ để chọn vị trí.
          </div>
          <?php if (!$isNew): ?>
            <label style="display:flex;gap:6px;align-items:center;margin-top:10px;font-size:13px">
              <input type="checkbox" name="clear_geom" value="1" style="width:auto"> Xoá toạ độ hiện tại
            </label>
          <?php endif ?>
        </div>

        <div style="display:flex;gap:10px">
          <button class="btn btn-primary" type="submit" style="flex:1"><i class="fa fa-floppy-disk"></i> <?= $isNew ? 'Thêm mới' : 'Lưu thay đổi' ?></button>
          <a class="btn btn-light" href="<?= Url::to(['index']) ?>">Huỷ</a>
        </div>
      </div>
    </div>
  </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const initLat = <?= !empty($data['lat']) ? (float)$data['lat'] : Dhqg::MAP_CENTER[0] ?>;
const initLng = <?= !empty($data['lng']) ? (float)$data['lng'] : Dhqg::MAP_CENTER[1] ?>;
const hasCoords = <?= (!empty($data['lat']) && !empty($data['lng'])) ? 'true' : 'false' ?>;
const pickMap = L.map('pick-map').setView([initLat, initLng], hasCoords ? 16 : <?= Dhqg::MAP_ZOOM ?>);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{attribution:'© CARTO',subdomains:'abcd',maxZoom:19}).addTo(pickMap);
let marker = null;
function place(lat,lng){ if(marker) pickMap.removeLayer(marker); marker=L.marker([lat,lng],{draggable:true}).addTo(pickMap); marker.on('dragend',e=>setCoords(e.target.getLatLng().lat,e.target.getLatLng().lng)); }
if(hasCoords) place(initLat, initLng);
pickMap.on('click', e => { setCoords(e.latlng.lat, e.latlng.lng); place(e.latlng.lat, e.latlng.lng); });
function setCoords(lat,lng){
  lat=parseFloat(lat.toFixed(7)); lng=parseFloat(lng.toFixed(7));
  document.getElementById('inp-lat').value=lat; document.getElementById('inp-lng').value=lng;
  document.getElementById('manual-lat').value=lat; document.getElementById('manual-lng').value=lng;
  document.getElementById('map-hint').innerHTML='<i class="fa fa-check-circle"></i> Đã chọn: '+lat.toFixed(6)+', '+lng.toFixed(6);
}
function setManualCoords(){
  const lat=parseFloat(document.getElementById('manual-lat').value), lng=parseFloat(document.getElementById('manual-lng').value);
  if(!isNaN(lat)&&!isNaN(lng)){ setCoords(lat,lng); place(lat,lng); pickMap.setView([lat,lng],16); }
}
</script>
