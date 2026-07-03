<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\Dhqg;

/** @var yii\web\View $this */
/** @var string $module */
/** @var array $cfg */
/** @var array $data */
/** @var array $anh */

$this->title = $data['ten'];
$mau = $cfg['mau'];
$hasGeom = !empty($data['lat']) && !empty($data['lng']);
?>
<style>
.v-wrap{max-width:960px}
.v-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:14px}
.v-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.v-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:14px}
.v-row .k{color:#6b7280}
.v-row .val{font-weight:600;color:#111827}
.gal{display:flex;flex-wrap:wrap;gap:10px}
.gal img{width:150px;height:110px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}
.btn{padding:9px 15px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;display:inline-flex;gap:7px;align-items:center;border:none}
.btn-primary{background:<?= $mau ?>;color:#fff}.btn-light{background:#f3f4f6;color:#374151}
</style>
<div class="v-wrap">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
    <h2 style="font-size:20px;font-weight:700"><i class="fa <?= $cfg['icon'] ?>" style="color:<?= $mau ?>"></i> <?= Html::encode($data['ten']) ?></h2>
    <div>
      <a class="btn btn-light" href="<?= Url::to(['index']) ?>"><i class="fa fa-arrow-left"></i> Danh sách</a>
      <a class="btn btn-primary" href="<?= Url::to(['update', 'id' => $data['id']]) ?>"><i class="fa fa-pen"></i> Sửa</a>
    </div>
  </div>

  <div class="v-grid">
    <div class="v-card">
      <div class="v-row"><span class="k">Mã</span><span class="val"><?= Html::encode($data['ma']) ?></span></div>
      <div class="v-row"><span class="k">Loại</span><span class="val"><?= Html::encode(Dhqg::loaiLabel($module, $data['loai'])) ?></span></div>
      <?php if ($cfg['co_trang_thai']): ?>
        <div class="v-row"><span class="k">Trạng thái</span><span class="val" style="color:<?= Dhqg::MAU_TRANG_THAI[$data['trang_thai']] ?? '#111' ?>"><?= Html::encode(Dhqg::trangThaiLabel($data['trang_thai'])) ?></span></div>
      <?php endif ?>
      <div class="v-row"><span class="k">Năm</span><span class="val"><?= $data['nam'] ? (int)$data['nam'] : '—' ?></span></div>
      <div class="v-row"><span class="k">Đơn vị thực hiện</span><span class="val"><?= Html::encode($this->context->layTenDonVi($data['don_vi_thuc_hien_id'] ?? null)) ?></span></div>
      <div class="v-row"><span class="k">Đơn vị quản lý</span><span class="val"><?= Html::encode($this->context->layTenDonVi($data['don_vi_quan_ly_id'] ?? null)) ?></span></div>
      <?php if (!empty($data['mo_ta'])): ?><div style="padding-top:10px;font-size:14px"><strong>Mô tả:</strong> <?= nl2br(Html::encode($data['mo_ta'])) ?></div><?php endif ?>
      <?php if (!empty($data['noi_dung'])): ?><div style="padding-top:10px;font-size:14px"><strong>Nội dung:</strong> <?= nl2br(Html::encode($data['noi_dung'])) ?></div><?php endif ?>
    </div>

    <div class="v-card">
      <?php if ($hasGeom): ?>
        <div id="view-map" style="height:260px;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb"></div>
        <p style="font-size:12px;color:#6b7280;margin-top:6px">Toạ độ: <?= round($data['lat'],6) ?>, <?= round($data['lng'],6) ?></p>
      <?php else: ?>
        <div style="height:260px;display:flex;align-items:center;justify-content:center;background:#f9fafb;border-radius:8px;color:#9ca3af">Chưa có toạ độ</div>
      <?php endif ?>
    </div>
  </div>

  <div class="v-card">
    <h3 style="font-size:13px;color:#6b7280;text-transform:uppercase;margin-bottom:12px">Hình ảnh (<?= count($anh) ?>)</h3>
    <?php if ($anh): ?>
      <div class="gal">
        <?php foreach ($anh as $a): ?>
          <div style="text-align:center">
            <a href="<?= Html::encode(Dhqg::anhUrl($a['url'])) ?>" target="_blank"><img src="<?= Html::encode(Dhqg::anhUrl($a['url'])) ?>" alt=""></a>
            <div style="font-size:11px;color:#6b7280;margin-top:3px"><?= Html::encode(Dhqg::LOAI_ANH[$a['loai_anh']] ?? '') ?></div>
          </div>
        <?php endforeach ?>
      </div>
    <?php else: ?>
      <p style="color:#9ca3af">Chưa có ảnh.</p>
    <?php endif ?>
  </div>
</div>

<?php if ($hasGeom): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const m = L.map('view-map').setView([<?= (float)$data['lat'] ?>, <?= (float)$data['lng'] ?>], 16);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{attribution:'© CARTO',subdomains:'abcd',maxZoom:19}).addTo(m);
L.circleMarker([<?= (float)$data['lat'] ?>, <?= (float)$data['lng'] ?>],{radius:9,color:'#fff',weight:2,fillColor:'<?= $mau ?>',fillOpacity:1}).addTo(m);
</script>
<?php endif ?>
