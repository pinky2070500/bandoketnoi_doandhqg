<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\Dhqg;

/** @var yii\web\View $this */
/** @var string $module */
/** @var array $cfg */
/** @var array $rows */
/** @var yii\data\Pagination $pages */
/** @var int $total */
/** @var array $thongke */
/** @var string $q, $loai, $nam, $trang_thai, $coords */

$this->title = $cfg['label'];
$mau = $cfg['mau'];
?>
<style>
.dt-wrap{padding:4px 2px}
.dt-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px}
.dt-title{font-size:20px;font-weight:700;color:#111827;display:flex;align-items:center;gap:10px}
.dt-title .ic{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff}
.dt-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}
.dt-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px}
.dt-card .n{font-size:24px;font-weight:800;color:#111827}
.dt-card .l{font-size:12px;color:#6b7280;margin-top:2px}
.dt-filter{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px;margin-bottom:14px;display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.dt-filter input,.dt-filter select{padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px}
.dt-table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb}
.dt-table th{background:#f9fafb;text-align:left;padding:10px 12px;font-size:12px;color:#6b7280;font-weight:600;border-bottom:1px solid #e5e7eb}
.dt-table td{padding:10px 12px;font-size:13px;border-bottom:1px solid #f3f4f6;vertical-align:middle}
.dt-badge{display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600}
.btn{padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:none;cursor:pointer}
.btn-primary{background:<?= $mau ?>;color:#fff}
.btn-sm{padding:5px 9px;font-size:12px}
.btn-light{background:#f3f4f6;color:#374151}
.btn-danger{background:#fee2e2;color:#dc2626}
.dt-page a,.dt-page span{padding:6px 11px;border-radius:7px;border:1px solid #e5e7eb;text-decoration:none;color:#374151;font-size:13px}
.dt-page .on{background:<?= $mau ?>;color:#fff;border-color:<?= $mau ?>}
</style>

<div class="dt-wrap">
  <div class="dt-head">
    <div class="dt-title">
      <span class="ic" style="background:<?= $mau ?>"><i class="fa <?= $cfg['icon'] ?>"></i></span>
      <?= Html::encode($cfg['label']) ?>
    </div>
    <a href="<?= Url::to(['create']) ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Thêm mới</a>
  </div>

  <div class="dt-stats">
    <div class="dt-card"><div class="n"><?= (int)$thongke['tong'] ?></div><div class="l">Tổng số</div></div>
    <div class="dt-card"><div class="n" style="color:<?= $mau ?>"><?= (int)$thongke['hoan_thanh'] ?></div><div class="l">Hoàn thành</div></div>
    <div class="dt-card"><div class="n" style="color:#f59e0b"><?= (int)$thongke['chua_toa_do'] ?></div><div class="l">Chưa có toạ độ</div></div>
  </div>

  <form class="dt-filter" method="get">
    <input type="text" name="q" value="<?= Html::encode($q) ?>" placeholder="Tìm theo tên / mã / mô tả…" style="flex:1;min-width:180px">
    <select name="loai">
      <option value="">— Loại —</option>
      <?php foreach ($cfg['loai'] as $k => $v): ?>
        <option value="<?= $k ?>" <?= $loai === $k ? 'selected' : '' ?>><?= Html::encode($v) ?></option>
      <?php endforeach ?>
    </select>
    <?php if ($cfg['co_trang_thai']): ?>
    <select name="trang_thai">
      <option value="">— Trạng thái —</option>
      <?php foreach (Dhqg::TRANG_THAI as $k => $v): ?>
        <option value="<?= $k ?>" <?= $trang_thai === $k ? 'selected' : '' ?>><?= Html::encode($v) ?></option>
      <?php endforeach ?>
    </select>
    <?php endif ?>
    <select name="coords">
      <option value="">— Toạ độ —</option>
      <option value="yes" <?= $coords === 'yes' ? 'selected' : '' ?>>Đã có</option>
      <option value="no" <?= $coords === 'no' ? 'selected' : '' ?>>Chưa có</option>
    </select>
    <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-filter"></i> Lọc</button>
    <a class="btn btn-light btn-sm" href="<?= Url::to(['index']) ?>">Xoá lọc</a>
  </form>

  <table class="dt-table">
    <thead><tr>
      <th>Mã</th><th>Tên</th><th>Loại</th><th>Năm</th>
      <?php if ($cfg['co_trang_thai']): ?><th>Trạng thái</th><?php endif ?>
      <th>Đơn vị thực hiện</th><th>Ảnh</th><th>Toạ độ</th><th style="text-align:right">Thao tác</th>
    </tr></thead>
    <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:26px">Chưa có dữ liệu. Bấm “Thêm mới”.</td></tr>
    <?php endif ?>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><code><?= Html::encode($r['ma']) ?></code></td>
        <td><strong><?= Html::encode($r['ten']) ?></strong></td>
        <td><?= Html::encode(Dhqg::loaiLabel($module, $r['loai'])) ?></td>
        <td><?= $r['nam'] ? (int)$r['nam'] : '—' ?></td>
        <?php if ($cfg['co_trang_thai']): ?>
          <td><span class="dt-badge" style="background:<?= (Dhqg::MAU_TRANG_THAI[$r['trang_thai']] ?? '#6b7280') ?>22;color:<?= Dhqg::MAU_TRANG_THAI[$r['trang_thai']] ?? '#6b7280' ?>"><?= Html::encode(Dhqg::trangThaiLabel($r['trang_thai'])) ?></span></td>
        <?php endif ?>
        <td><?= Html::encode($r['don_vi_thuc_hien'] ?? '—') ?></td>
        <td><?= (int)$r['so_anh'] ? '<i class="fa fa-image" style="color:'.$mau.'"></i> '.(int)$r['so_anh'] : '—' ?></td>
        <td><?= $r['has_geom'] ? '<span style="color:#16a37f">● Có</span>' : '<span style="color:#ef4444">○ Chưa</span>' ?></td>
        <td style="text-align:right;white-space:nowrap">
          <a class="btn btn-light btn-sm" href="<?= Url::to(['view', 'id' => $r['id']]) ?>"><i class="fa fa-eye"></i></a>
          <a class="btn btn-light btn-sm" href="<?= Url::to(['update', 'id' => $r['id']]) ?>"><i class="fa fa-pen"></i></a>
          <?= Html::beginForm(['delete', 'id' => $r['id']], 'post', ['style' => 'display:inline']) ?>
            <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá “<?= Html::encode(addslashes($r['ten'])) ?>”?')"><i class="fa fa-trash"></i></button>
          <?= Html::endForm() ?>
        </td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>

  <?php if ($pages->pageCount > 1): ?>
  <div class="dt-page" style="display:flex;gap:6px;margin-top:14px;justify-content:center">
    <?php for ($p = 0; $p < $pages->pageCount; $p++): ?>
      <?php $params = Yii::$app->request->queryParams; $params['page'] = $p + 1; ?>
      <a class="<?= $p === $pages->page ? 'on' : '' ?>" href="<?= Url::to(array_merge(['index'], $params)) ?>"><?= $p + 1 ?></a>
    <?php endfor ?>
  </div>
  <?php endif ?>

  <p style="color:#9ca3af;font-size:12px;margin-top:10px">Tổng <?= $total ?> bản ghi.</p>
</div>
