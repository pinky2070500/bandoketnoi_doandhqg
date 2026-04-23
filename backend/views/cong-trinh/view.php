<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Chi tiết: ' . ($data['ten_ct'] ?? '');

$priorityLabel = ['khan_cap'=>'Khẩn cấp','cao'=>'Ưu tiên cao','binh_thuong'=>'Bình thường'];
$priorityClass = ['khan_cap'=>'pr','cao'=>'pa','binh_thuong'=>'pg'];
$statusLabel   = ['cho_dau_tu'=>'Chờ đầu tư','dang_thi_cong'=>'Đang thi công','hoan_thanh'=>'Hoàn thành'];
$statusColor   = ['cho_dau_tu'=>'#9ca3af','dang_thi_cong'=>'#f59e0b','hoan_thanh'=>'#16a37f'];
$loaiLabel     = ['cau'=>'🌉 Cầu giao thông','duong'=>'🛣️ Đường bê tông','truong'=>'🏫 Trường học','nuoc'=>'💧 Nước sạch','khac'=>'📋 Khác'];
$prio = $data['muc_uu_tien'] ?? 'cao';
$heroGrad = ['khan_cap'=>'135deg,#7f1d1d,#ef4444','cao'=>'135deg,#78350f,#f59e0b','binh_thuong'=>'135deg,#0d7a5f,#34d399'];
?>
<style>
.vw-wrap{max-width:980px;margin:0 auto}
.vw-hero{
  position:relative;border-radius:var(--radius);overflow:hidden;
  height:160px;margin-bottom:20px;
  background:linear-gradient(<?= $heroGrad[$prio] ?? $heroGrad['binh_thuong'] ?>);
}
.vw-hero-pat{
  position:absolute;inset:0;opacity:.1;
  background-image:radial-gradient(circle at 15% 50%,#fff 1px,transparent 1px),
                   radial-gradient(circle at 85% 25%,#fff 1px,transparent 1px);
  background-size:28px 28px;
}
.vw-hero-content{position:absolute;inset:0;display:flex;align-items:flex-end;padding:24px 28px;gap:20px}
.vw-hero-icon{
  width:64px;height:64px;border-radius:16px;background:rgba(255,255,255,.2);
  backdrop-filter:blur(8px);border:2px solid rgba(255,255,255,.3);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.vw-hero-icon i{font-size:28px;color:#fff}
.vw-hero-info{flex:1;min-width:0}
.vw-hero-badge{
  display:inline-flex;align-items:center;gap:5px;
  font-size:10px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;
  background:rgba(255,255,255,.2);backdrop-filter:blur(4px);
  color:#fff;padding:3px 10px;border-radius:20px;
  border:1px solid rgba(255,255,255,.3);margin-bottom:8px;
}
.vw-hero-name{font-size:22px;font-weight:700;color:#fff;line-height:1.3;letter-spacing:-.3px}
.vw-hero-loc{font-size:13px;color:rgba(255,255,255,.8);margin-top:4px;display:flex;align-items:center;gap:5px}
.vw-hero-actions{display:flex;flex-direction:column;gap:8px;align-items:flex-end;padding-bottom:4px}
.vw-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.vcard{background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius);overflow:hidden}
.vcard.full{grid-column:1/-1}
.vcard-head{
  display:flex;align-items:center;gap:10px;
  padding:13px 18px;border-bottom:1px solid var(--gray-100);background:var(--gray-50);
}
.vcard-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.vcard-title{font-size:13px;font-weight:600;color:var(--gray-800)}
.vcard-body{padding:16px 18px}
.kv-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.kv{padding:9px 12px;background:var(--gray-50);border-radius:var(--radius-sm);border:1px solid var(--gray-100)}
.kv .k{font-size:10px;color:var(--gray-400);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px}
.kv .v{font-size:14px;font-weight:600;color:var(--gray-800)}
.kv .v.big{font-size:22px;font-weight:700}
.kv .u{font-size:11px;font-weight:400;color:var(--gray-400)}
/* Progress */
.prog-wrap{padding:0 2px}
.prog-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.prog-label{font-size:12px;font-weight:600;color:var(--gray-700)}
.prog-pct{font-size:15px;font-weight:700}
.prog-track{height:10px;background:var(--gray-100);border-radius:5px;overflow:hidden}
.prog-fill{height:100%;border-radius:5px;transition:width .5s ease}
.prog-steps{display:flex;margin-top:10px}
.prog-step{flex:1;text-align:center;position:relative}
.prog-step::before{
  content:'';display:block;width:8px;height:8px;border-radius:50%;
  background:var(--gray-300);margin:0 auto 4px;border:2px solid #fff;
  box-shadow:0 0 0 2px var(--gray-300);
}
.prog-step.on::before{background:var(--green);box-shadow:0 0 0 2px var(--green)}
.prog-step span{font-size:10px;color:var(--gray-400)}
.prog-step.on span{color:var(--green-dark);font-weight:600}
/* Contact */
.contact-card{
  display:flex;align-items:center;gap:14px;
  padding:14px;background:var(--green-lite);
  border:1px solid #a7f3d0;border-radius:var(--radius-sm);
}
.contact-av{
  width:46px;height:46px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:16px;font-weight:700;
}
.contact-name{font-size:14px;font-weight:700;color:var(--gray-800)}
.contact-role{font-size:12px;color:var(--gray-500);margin-top:2px}
.contact-phone{
  display:flex;align-items:center;gap:6px;margin-top:6px;
  font-size:13px;font-weight:600;color:var(--green-dark);
}
/* Map view */
#view-map{height:280px;border-radius:var(--radius-sm);border:1.5px solid var(--gray-200)}
.no-data{color:var(--gray-400);font-size:13px;font-style:italic}
</style>

<!-- Breadcrumb -->
<div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;font-size:13px;color:var(--gray-400)">
  <a href="<?= Url::to(['/cong-trinh/index']) ?>" style="color:var(--green)">
    <i class="fa fa-bridge"></i> Công trình
  </a>
  <i class="fa fa-chevron-right" style="font-size:10px"></i>
  <span style="color:var(--gray-600)"><?= Html::encode($data['ten_ct'] ?? '') ?></span>
</div>

<div class="vw-wrap">

  <!-- HERO -->
  <div class="vw-hero">
    <div class="vw-hero-pat"></div>
    <div class="vw-hero-content">
      <div class="vw-hero-icon">
        <i class="fa fa-bridge"></i>
      </div>
      <div class="vw-hero-info">
        <div class="vw-hero-badge">
          <span style="width:7px;height:7px;border-radius:50%;background:rgba(255,255,255,.7);display:inline-block"></span>
          <?= Html::encode($priorityLabel[$prio] ?? $prio) ?>
        </div>
        <div class="vw-hero-name"><?= Html::encode($data['ten_ct'] ?? '') ?></div>
        <div class="vw-hero-loc">
          <i class="fa fa-location-dot"></i>
          <?= Html::encode(($data['ten_xa'] ?? '') . ', ' . ($data['ten_huyen'] ?? '')) ?>
        </div>
      </div>
      <div class="vw-hero-actions">
        <a href="<?= Url::to(['/cong-trinh/update', 'id' => $data['id']]) ?>"
           class="btn btn-primary" style="background:rgba(255,255,255,.2);backdrop-filter:blur(8px);border:1.5px solid rgba(255,255,255,.4);white-space:nowrap">
          <i class="fa fa-pen"></i> Chỉnh sửa
        </a>
        <a href="<?= Url::to(['/cong-trinh/index']) ?>"
           class="btn btn-secondary" style="background:rgba(0,0,0,.15);border-color:rgba(255,255,255,.2);color:#fff;white-space:nowrap">
          <i class="fa fa-arrow-left"></i> Danh sách
        </a>
      </div>
    </div>
  </div>

  <div class="vw-grid">

    <!-- Thông tin cơ bản -->
    <div class="vcard">
      <div class="vcard-head">
        <div class="vcard-icon" style="background:#eff6ff;color:#3b82f6"><i class="fa fa-id-card"></i></div>
        <div class="vcard-title">Thông tin cơ bản</div>
      </div>
      <div class="vcard-body">
        <div class="kv-grid">
          <div class="kv"><div class="k">Mã công trình</div><div class="v" style="font-family:monospace"><?= Html::encode($data['ma_ct'] ?? '–') ?></div></div>
          <div class="kv"><div class="k">Năm đầu tư</div><div class="v big"><?= Html::encode($data['nam_dau_tu'] ?? '–') ?></div></div>
          <div class="kv"><div class="k">Loại công trình</div><div class="v" style="font-size:13px"><?= Html::encode($loaiLabel[$data['loai_ct'] ?? ''] ?? ($data['loai_ct'] ?? '–')) ?></div></div>
          <div class="kv"><div class="k">Mức ưu tiên</div>
            <div class="v">
              <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;
                <?php
                $bc=['khan_cap'=>'background:#fef2f2;color:#b91c1c','cao'=>'background:#fffbeb;color:#92400e','binh_thuong'=>'background:var(--green-lite);color:var(--green-dark)'];
                echo $bc[$prio] ?? '';
                ?>">
                <?= Html::encode($priorityLabel[$prio] ?? $prio) ?>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Thông số kỹ thuật -->
    <div class="vcard">
      <div class="vcard-head">
        <div class="vcard-icon" style="background:#fdf4ff;color:#9333ea"><i class="fa fa-ruler-combined"></i></div>
        <div class="vcard-title">Thông số kỹ thuật</div>
      </div>
      <div class="vcard-body">
        <div class="kv-grid">
          <div class="kv">
            <div class="k">Chiều dài lòng sông</div>
            <div class="v big"><?= $data['chieu_dai'] ?? '–' ?><span class="u"> m</span></div>
          </div>
          <div class="kv">
            <div class="k">Chiều dài cầu</div>
            <div class="v big"><?= $data['chieu_rong'] ?? '–' ?><span class="u"> m</span></div>
          </div>
          <div class="kv">
            <div class="k">Tải trọng thiết kế</div>
            <div class="v big"><?= $data['tai_trong'] ?? '–' ?><span class="u"> tấn</span></div>
          </div>
          <div class="kv">
            <div class="k">Kinh phí đối ứng</div>
            <div class="v" style="font-size:13px">
              <?= $data['kinh_phi_dc'] ? number_format($data['kinh_phi_dc']) . ' đ' : '–' ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tiến độ -->
    <div class="vcard full">
      <div class="vcard-head">
        <div class="vcard-icon" style="background:#f0fdf9;color:#16a37f"><i class="fa fa-chart-line"></i></div>
        <div class="vcard-title">Tiến độ thực hiện</div>
        <div style="margin-left:auto">
          <span style="font-size:12px;padding:4px 12px;border-radius:20px;font-weight:600;
            background:<?= $statusColor[$data['trang_thai']??''] ?? '#9ca3af' ?>20;
            color:<?= $statusColor[$data['trang_thai']??''] ?? '#9ca3af' ?>">
            <?= Html::encode($statusLabel[$data['trang_thai'] ?? ''] ?? ($data['trang_thai'] ?? '–')) ?>
          </span>
        </div>
      </div>
      <div class="vcard-body">
        <?php $pct = (int)($data['tien_do'] ?? 0); ?>
        <div class="prog-wrap">
          <div class="prog-head">
            <span class="prog-label">Hoàn thành</span>
            <span class="prog-pct" style="color:<?= $pct===100?'#16a37f':($pct>0?'#f59e0b':'#9ca3af') ?>"><?= $pct ?>%</span>
          </div>
          <div class="prog-track">
            <div class="prog-fill" style="width:<?= $pct ?>%;background:<?= $pct===100?'linear-gradient(90deg,#16a37f,#34d399)':($pct>0?'linear-gradient(90deg,#f59e0b,#fbbf24)':'#e5e7eb') ?>"></div>
          </div>
          <div class="prog-steps">
            <div class="prog-step on"><span>Chờ đầu tư</span></div>
            <div class="prog-step <?= $pct>0?'on':'' ?>"><span>Thi công</span></div>
            <div class="prog-step <?= $pct===100?'on':'' ?>"><span>Hoàn thành</span></div>
          </div>
        </div>
        <?php if(!empty($data['mo_ta'])): ?>
          <div style="margin-top:14px;padding:12px;background:var(--gray-50);border-radius:var(--radius-sm);font-size:13px;color:var(--gray-600);line-height:1.6;border-left:3px solid var(--green)">
            <?= nl2br(Html::encode($data['mo_ta'])) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Vị trí & Bản đồ -->
    <div class="vcard">
      <div class="vcard-head">
        <div class="vcard-icon" style="background:#fef2f2;color:#ef4444"><i class="fa fa-map-location-dot"></i></div>
        <div class="vcard-title">Vị trí trên bản đồ</div>
        <?php if($data['lat'] && $data['lng']): ?>
          <div style="margin-left:auto;font-size:11px;color:var(--green);font-weight:600;display:flex;align-items:center;gap:4px">
            <i class="fa fa-circle-check"></i> Có tọa độ
          </div>
        <?php endif; ?>
      </div>
      <div class="vcard-body" style="padding:14px">
        <?php if($data['lat'] && $data['lng']): ?>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
            <div class="kv"><div class="k">Vĩ độ (Lat)</div><div class="v" style="font-family:monospace;font-size:13px"><?= number_format((float)$data['lat'],6) ?></div></div>
            <div class="kv"><div class="k">Kinh độ (Lng)</div><div class="v" style="font-family:monospace;font-size:13px"><?= number_format((float)$data['lng'],6) ?></div></div>
          </div>
          <div id="view-map"></div>
        <?php else: ?>
          <div style="height:280px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:var(--gray-50);border-radius:var(--radius-sm);border:2px dashed var(--gray-200)">
            <i class="fa fa-map-location-dot" style="font-size:40px;color:var(--gray-300);margin-bottom:12px"></i>
            <div style="font-size:13px;color:var(--gray-400);font-weight:500">Chưa có tọa độ</div>
            <a href="<?= Url::to(['/cong-trinh/update','id'=>$data['id']]) ?>"
               style="margin-top:10px;font-size:12px;color:var(--green);font-weight:600;text-decoration:none">
              <i class="fa fa-plus"></i> Thêm tọa độ
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Liên hệ -->
    <div class="vcard">
      <div class="vcard-head">
        <div class="vcard-icon" style="background:#eff6ff;color:#3b82f6"><i class="fa fa-address-card"></i></div>
        <div class="vcard-title">Đầu mối liên hệ</div>
      </div>
      <div class="vcard-body">
        <?php if(!empty($data['lien_he_ho_ten'])): ?>
          <div class="contact-card">
            <div class="contact-av">
              <?= strtoupper(implode('',array_map(fn($w)=>$w[0], array_slice(explode(' ',$data['lien_he_ho_ten']),- 2)))) ?>
            </div>
            <div style="flex:1">
              <div class="contact-name"><?= Html::encode($data['lien_he_ho_ten']) ?></div>
              <?php if($data['lien_he_chuc_vu']): ?>
                <div class="contact-role"><?= Html::encode($data['lien_he_chuc_vu']) ?></div>
              <?php endif; ?>
              <?php if($data['lien_he_sdt']): ?>
                <div class="contact-phone">
                  <i class="fa fa-phone"></i>
                  <a href="tel:<?= Html::encode($data['lien_he_sdt']) ?>" style="color:inherit;text-decoration:none">
                    <?= Html::encode($data['lien_he_sdt']) ?>
                  </a>
                </div>
              <?php endif; ?>
              <?php if($data['lien_he_email']): ?>
                <div style="display:flex;align-items:center;gap:6px;margin-top:4px;font-size:12px;color:var(--gray-600)">
                  <i class="fa fa-envelope" style="color:var(--gray-400)"></i>
                  <a href="mailto:<?= Html::encode($data['lien_he_email']) ?>" style="color:inherit">
                    <?= Html::encode($data['lien_he_email']) ?>
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php else: ?>
          <div class="no-data"><i class="fa fa-user-slash" style="margin-right:6px"></i>Chưa có thông tin liên hệ</div>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /vw-grid -->

  <!-- Action bar bottom -->
  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;padding:16px 20px;background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius)">
    <div style="font-size:12px;color:var(--gray-400)">
      <i class="fa fa-clock"></i> Cập nhật lần cuối: <?= Html::encode($data['updated_at'] ?? '–') ?>
    </div>
    <div style="display:flex;gap:10px">
      <a href="<?= Url::to(['/cong-trinh/index']) ?>" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Danh sách
      </a>
      <a href="<?= Url::to(['/cong-trinh/update','id'=>$data['id']]) ?>" class="btn btn-primary">
        <i class="fa fa-pen"></i> Chỉnh sửa
      </a>
    </div>
  </div>

</div>

<?php if(!empty($data['lat']) && !empty($data['lng'])): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const lat = <?= (float)$data['lat'] ?>, lng = <?= (float)$data['lng'] ?>;
const vm = L.map('view-map', {zoomControl:true, dragging:true, scrollWheelZoom:false}).setView([lat,lng],15);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{
  attribution:'© CARTO', subdomains:'abcd', maxZoom:19
}).addTo(vm);
const COL = {khan_cap:'#ef4444', cao:'#f59e0b', binh_thuong:'#16a37f'};
const c = COL[<?= json_encode($prio) ?>] || '#16a37f';
L.divIcon({
  html:`<div style="width:24px;height:24px;border-radius:50%;background:${c};border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3)"></div>`,
  iconSize:[24,24], iconAnchor:[12,12], className:''
});
L.marker([lat, lng], {
  icon: L.divIcon({
    html:`<div style="position:relative"><div style="width:28px;height:28px;border-radius:50%;background:${c};border:3px solid #fff;box-shadow:0 3px 10px rgba(0,0,0,.25);display:flex;align-items:center;justify-content:center"><i class="fa fa-bridge" style="color:#fff;font-size:12px"></i></div></div>`,
    iconSize:[28,28], iconAnchor:[14,14], className:''
  })
}).addTo(vm).bindPopup(`<strong><?= Html::encode($data['ten_ct'] ?? '') ?></strong><br><small><?= Html::encode(($data['ten_xa']??'').' - '.($data['ten_huyen']??'')) ?></small>`, {maxWidth:220}).openPopup();
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<?php endif; ?>