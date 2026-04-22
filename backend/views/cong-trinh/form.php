<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $isNew ? 'Thêm công trình mới' : 'Cập nhật công trình';

$huyens = ['Hồng Ngự','Tam Nông','Tân Hồng','Thanh Bình','Tháp Mười','Lai Vung','Cao Lãnh','TP.Hồng Ngự','TP.Sa Đéc'];
$v = fn($k) => Html::encode($data[$k] ?? '');
?>

<div style="max-width:960px">

  <!-- Breadcrumb -->
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;font-size:13px;color:var(--gray-400)">
    <a href="<?= Url::to(['/cong-trinh/index']) ?>" style="color:var(--green)">Công trình</a>
    <i class="fa fa-chevron-right" style="font-size:10px"></i>
    <span><?= $this->title ?></span>
  </div>

  <form method="post" action="<?= $isNew ? Url::to(['/cong-trinh/create']) : Url::to(['/cong-trinh/update','id'=>$id]) ?>">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    <input type="hidden" id="inp-lat" name="lat" value="<?= $v('lat') ?>">
    <input type="hidden" id="inp-lng" name="lng" value="<?= $v('lng') ?>">

    <div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start">

      <!-- CỘT TRÁI: Form -->
      <div style="display:flex;flex-direction:column;gap:16px">

        <!-- Thông tin cơ bản -->
        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa fa-bridge" style="color:var(--green);margin-right:7px"></i>Thông tin công trình</div></div>
          <div class="card-body">
            <div class="form-grid">

              <div class="form-group">
                <label class="form-label">Mã công trình</label>
                <input type="text" name="ma_ct" value="<?= $v('ma_ct') ?>" class="form-control" placeholder="CT001" style="text-transform:uppercase">
              </div>

              <div class="form-group">
                <label class="form-label">Năm đầu tư <span class="req">*</span></label>
                <select name="nam_dau_tu" class="form-control <?= isset($errors['nam_dau_tu'])?'error':'' ?>">
                  <?php foreach(range(2025,2030) as $y): ?>
                    <option value="<?=$y?>" <?=($data['nam_dau_tu']??'')==$y?'selected':''?>><?=$y?></option>
                  <?php endforeach; ?>
                </select>
                <?php if(isset($errors['nam_dau_tu'])): ?><div class="form-error"><?= $errors['nam_dau_tu'] ?></div><?php endif; ?>
              </div>

              <div class="form-group full">
                <label class="form-label">Tên công trình <span class="req">*</span></label>
                <input type="text" name="ten_ct" value="<?= $v('ten_ct') ?>" class="form-control <?= isset($errors['ten_ct'])?'error':'' ?>" placeholder="Cầu kênh...">
                <?php if(isset($errors['ten_ct'])): ?><div class="form-error"><?= $errors['ten_ct'] ?></div><?php endif; ?>
              </div>

              <div class="form-group">
                <label class="form-label">Xã / Phường</label>
                <input type="text" name="ten_xa" value="<?= $v('ten_xa') ?>" class="form-control" placeholder="Tên xã">
              </div>

              <div class="form-group">
                <label class="form-label">Huyện / Thành phố</label>
                <select name="ten_huyen" class="form-control">
                  <option value="">-- Chọn huyện --</option>
                  <?php foreach($huyens as $h): ?>
                    <option value="<?= Html::encode($h) ?>" <?=($data['ten_huyen']??'')===$h?'selected':''?>><?= Html::encode($h) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Loại công trình</label>
                <select name="loai_ct" class="form-control">
                  <option value="cau"    <?=($data['loai_ct']??'')==='cau'?'selected':''?>>Cầu giao thông</option>
                  <option value="duong"  <?=($data['loai_ct']??'')==='duong'?'selected':''?>>Đường bê tông</option>
                  <option value="truong" <?=($data['loai_ct']??'')==='truong'?'selected':''?>>Trường học</option>
                  <option value="nuoc"   <?=($data['loai_ct']??'')==='nuoc'?'selected':''?>>Nước sạch</option>
                  <option value="khac"   <?=($data['loai_ct']??'')==='khac'?'selected':''?>>Khác</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Mức ưu tiên</label>
                <select name="muc_uu_tien" class="form-control">
                  <option value="khan_cap"    <?=($data['muc_uu_tien']??'')==='khan_cap'?'selected':''?>>🔴 Khẩn cấp</option>
                  <option value="cao"         <?=($data['muc_uu_tien']??'')==='cao'?'selected':''?>>🟡 Ưu tiên cao</option>
                  <option value="binh_thuong" <?=($data['muc_uu_tien']??'')==='binh_thuong'?'selected':''?>>🟢 Bình thường</option>
                </select>
              </div>

            </div>
          </div>
        </div>

        <!-- Thông số kỹ thuật -->
        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa fa-ruler-combined" style="color:var(--green);margin-right:7px"></i>Thông số kỹ thuật</div></div>
          <div class="card-body">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Chiều dài lòng sông (m)</label>
                <input type="number" name="chieu_dai" value="<?= $v('chieu_dai') ?>" class="form-control" placeholder="30" step="0.1">
              </div>
              <div class="form-group">
                <label class="form-label">Chiều dài cầu (m)</label>
                <input type="number" name="chieu_rong" value="<?= $v('chieu_rong') ?>" class="form-control" placeholder="3.5" step="0.1">
              </div>
              <div class="form-group">
                <label class="form-label">Tải trọng thiết kế (tấn)</label>
                <input type="number" name="tai_trong" value="<?= $v('tai_trong') ?>" class="form-control" placeholder="5" step="0.5">
              </div>
              <div class="form-group">
                <label class="form-label">Kinh phí đối ứng (đồng)</label>
                <input type="number" name="kinh_phi_dc" value="<?= $v('kinh_phi_dc') ?>" class="form-control" placeholder="500000000">
              </div>
            </div>
          </div>
        </div>

        <!-- Tiến độ -->
        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa fa-chart-line" style="color:var(--green);margin-right:7px"></i>Tiến độ thực hiện</div></div>
          <div class="card-body">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Trạng thái</label>
                <select name="trang_thai" class="form-control" id="sel-trang-thai" onchange="onStatusChange()">
                  <option value="cho_dau_tu"    <?=($data['trang_thai']??'')==='cho_dau_tu'?'selected':''?>>Chờ đầu tư</option>
                  <option value="dang_thi_cong" <?=($data['trang_thai']??'')==='dang_thi_cong'?'selected':''?>>Đang thi công</option>
                  <option value="hoan_thanh"    <?=($data['trang_thai']??'')==='hoan_thanh'?'selected':''?>>Hoàn thành</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Tiến độ: <strong id="pct-label"><?= $data['tien_do']??0 ?>%</strong></label>
                <input type="range" name="tien_do" id="inp-tien-do" min="0" max="100" step="5"
                       value="<?= $data['tien_do']??0 ?>"
                       style="width:100%;accent-color:var(--green)"
                       oninput="document.getElementById('pct-label').textContent=this.value+'%'">
              </div>
              <div class="form-group full">
                <label class="form-label">Mô tả / Ghi chú</label>
                <textarea name="mo_ta" class="form-control" rows="3" placeholder="Thông tin thêm về công trình..."><?= $v('mo_ta') ?></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Liên hệ -->
        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa fa-address-card" style="color:var(--green);margin-right:7px"></i>Đầu mối liên hệ</div></div>
          <div class="card-body">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="lien_he_ho_ten" value="<?= $v('lien_he_ho_ten') ?>" class="form-control" placeholder="Nguyễn Văn A">
              </div>
              <div class="form-group">
                <label class="form-label">Chức vụ</label>
                <input type="text" name="lien_he_chuc_vu" value="<?= $v('lien_he_chuc_vu') ?>" class="form-control" placeholder="Chủ tịch xã">
              </div>
              <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="lien_he_sdt" value="<?= $v('lien_he_sdt') ?>" class="form-control" placeholder="0912345678">
              </div>
              <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="lien_he_email" value="<?= $v('lien_he_email') ?>" class="form-control" placeholder="email@example.com">
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- CỘT PHẢI: Bản đồ chọn tọa độ -->
      <div style="position:sticky;top:74px">
        <div class="card">
          <div class="card-header">
            <div class="card-title"><i class="fa fa-map-pin" style="color:var(--green);margin-right:7px"></i>Tọa độ công trình</div>
          </div>
          <div class="card-body" style="padding:14px">

            <!-- Hiển thị tọa độ hiện tại -->
            <div id="coords-display" style="background:var(--gray-50);border:1.5px solid var(--gray-200);border-radius:var(--radius-sm);padding:10px 12px;margin-bottom:12px">
              <div style="font-size:11px;color:var(--gray-500);margin-bottom:6px;font-weight:600">TỌA ĐỘ HIỆN TẠI</div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <div>
                  <div style="font-size:10px;color:var(--gray-400)">Vĩ độ (Lat)</div>
                  <div style="font-size:13px;font-weight:600;color:var(--gray-800)" id="disp-lat">
                    <?= $data['lat'] ? number_format((float)$data['lat'],6) : '–' ?>
                  </div>
                </div>
                <div>
                  <div style="font-size:10px;color:var(--gray-400)">Kinh độ (Lng)</div>
                  <div style="font-size:13px;font-weight:600;color:var(--gray-800)" id="disp-lng">
                    <?= $data['lng'] ? number_format((float)$data['lng'],6) : '–' ?>
                  </div>
                </div>
              </div>
              <?php if($data['lat'] && $data['lng']): ?>
                <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--gray-200)">
                  <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--red);cursor:pointer">
                    <input type="checkbox" name="clear_geom" value="1" style="accent-color:var(--red)">
                    Xóa tọa độ này
                  </label>
                </div>
              <?php endif; ?>
            </div>

            <!-- Nhập tay -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
              <div>
                <div class="form-label" style="font-size:10px">Nhập Lat</div>
                <input type="number" id="manual-lat" class="form-control" step="0.000001" placeholder="10.612..." onchange="setManualCoords()">
              </div>
              <div>
                <div class="form-label" style="font-size:10px">Nhập Lng</div>
                <input type="number" id="manual-lng" class="form-control" step="0.000001" placeholder="105.391..." onchange="setManualCoords()">
              </div>
            </div>

            <div style="font-size:11px;color:var(--gray-500);margin-bottom:8px;text-align:center">
              <i class="fa fa-mouse-pointer"></i> hoặc <strong>click trực tiếp lên bản đồ</strong> để lấy tọa độ
            </div>

            <!-- Map -->
            <div id="pick-map" style="height:320px;border-radius:var(--radius-sm);overflow:hidden;border:1.5px solid var(--gray-200)"></div>

            <div id="map-hint" style="margin-top:8px;padding:8px;background:var(--green-lite);border-radius:var(--radius-sm);font-size:11px;color:var(--green-dark);display:flex;align-items:center;gap:6px">
              <i class="fa fa-circle-info"></i>
              Click vào bản đồ để chọn vị trí công trình. Tọa độ sẽ được tự động gán.
            </div>

          </div>
        </div>

        <!-- Buttons -->
        <div style="display:flex;gap:10px;margin-top:14px">
          <button type="submit" class="btn btn-primary" style="flex:1">
            <i class="fa fa-<?= $isNew?'plus':'floppy-disk' ?>"></i>
            <?= $isNew ? 'Thêm công trình' : 'Lưu thay đổi' ?>
          </button>
          <a href="<?= Url::to(['/cong-trinh/index']) ?>" class="btn btn-secondary">
            <i class="fa fa-xmark"></i> Hủy
          </a>
        </div>
      </div>

    </div>
  </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const initLat = <?= !empty($data['lat']) ? (float)$data['lat'] : 10.58 ?>;
const initLng = <?= !empty($data['lng']) ? (float)$data['lng'] : 105.63 ?>;
const hasCoords = <?= (!empty($data['lat']) && !empty($data['lng'])) ? 'true' : 'false' ?>;

const pickMap = L.map('pick-map').setView([initLat, initLng], hasCoords ? 14 : 10);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{
  attribution:'© CARTO',subdomains:'abcd',maxZoom:19
}).addTo(pickMap);

// Marker draggable
let marker = null;
if(hasCoords){
  marker = L.marker([initLat, initLng], {draggable:true}).addTo(pickMap);
  marker.on('dragend', e => setCoords(e.target.getLatLng().lat, e.target.getLatLng().lng));
}

// Click map để đặt marker
pickMap.on('click', e => {
  setCoords(e.latlng.lat, e.latlng.lng);
  if(marker) pickMap.removeLayer(marker);
  marker = L.marker([e.latlng.lat, e.latlng.lng], {draggable:true}).addTo(pickMap);
  marker.on('dragend', ev => setCoords(ev.target.getLatLng().lat, ev.target.getLatLng().lng));
  // Pulse animation
  marker.getElement()?.classList.add('marker-pulse');
});

function setCoords(lat, lng){
  lat = parseFloat(lat.toFixed(7));
  lng = parseFloat(lng.toFixed(7));
  document.getElementById('inp-lat').value = lat;
  document.getElementById('inp-lng').value = lng;
  document.getElementById('disp-lat').textContent = lat.toFixed(6);
  document.getElementById('disp-lng').textContent = lng.toFixed(6);
  document.getElementById('disp-lat').style.color = '#16a37f';
  document.getElementById('disp-lng').style.color = '#16a37f';
  document.getElementById('map-hint').style.background = '#f0fdf9';
  document.getElementById('map-hint').innerHTML = '<i class="fa fa-check-circle"></i> Đã chọn tọa độ: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
}

function setManualCoords(){
  const lat = parseFloat(document.getElementById('manual-lat').value);
  const lng = parseFloat(document.getElementById('manual-lng').value);
  if(!isNaN(lat) && !isNaN(lng)){
    setCoords(lat, lng);
    if(marker) pickMap.removeLayer(marker);
    marker = L.marker([lat,lng],{draggable:true}).addTo(pickMap);
    marker.on('dragend', e => setCoords(e.target.getLatLng().lat, e.target.getLatLng().lng));
    pickMap.setView([lat,lng], 14);
  }
}

function onStatusChange(){
  const status = document.getElementById('sel-trang-thai').value;
  const slider = document.getElementById('inp-tien-do');
  if(status === 'hoan_thanh'){ slider.value = 100; document.getElementById('pct-label').textContent='100%'; }
  else if(status === 'cho_dau_tu'){ slider.value = 0; document.getElementById('pct-label').textContent='0%'; }
}
</script>