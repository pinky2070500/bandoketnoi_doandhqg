<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Quản lý công trình';

$priorityLabel = ['khan_cap'=>'Khẩn cấp','cao'=>'Ưu tiên cao','binh_thuong'=>'Bình thường'];
$priorityClass = ['khan_cap'=>'badge-red','cao'=>'badge-amber','binh_thuong'=>'badge-green'];
$statusLabel   = ['cho_dau_tu'=>'Chờ đầu tư','dang_thi_cong'=>'Đang thi công','hoan_thanh'=>'Hoàn thành'];
$statusClass   = ['cho_dau_tu'=>'badge-gray','dang_thi_cong'=>'badge-blue','hoan_thanh'=>'badge-green'];
?>

<!-- STATS -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon" style="background:#eff6ff;color:#3b82f6"><i class="fa fa-bridge"></i></div>
    <div class="stat-label">Tổng công trình</div>
    <div class="stat-val"><?= $thongke['tong'] ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#fef2f2;color:#ef4444"><i class="fa fa-location-crosshairs"></i></div>
    <div class="stat-label">Chưa có tọa độ</div>
    <div class="stat-val" style="color:#ef4444"><?= $thongke['chua_toa_do'] ?></div>
    <div class="stat-sub">Cần nhập tọa độ</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#f0fdf9;color:#16a37f"><i class="fa fa-check-circle"></i></div>
    <div class="stat-label">Hoàn thành</div>
    <div class="stat-val" style="color:#16a37f"><?= $thongke['hoan_thanh'] ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#fff7ed;color:#f59e0b"><i class="fa fa-triangle-exclamation"></i></div>
    <div class="stat-label">Khẩn cấp</div>
    <div class="stat-val" style="color:#f59e0b"><?= $thongke['khan_cap'] ?></div>
  </div>
</div>

<!-- FILTER + LIST -->
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">Danh sách công trình</div>
      <div style="font-size:12px;color:var(--gray-400);margin-top:2px">Tổng: <?= $total ?> công trình</div>
    </div>
    <a class="btn btn-primary" href="<?= Url::to(['/cong-trinh/create']) ?>">
      <i class="fa fa-plus"></i> Thêm công trình
    </a>
  </div>

  <!-- Filter bar -->
  <form method="get" style="padding:14px 18px;border-bottom:1px solid var(--gray-100);display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end">
    <div style="flex:1;min-width:200px">
      <div class="form-label">Tìm kiếm</div>
      <div style="position:relative">
        <i class="fa fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--gray-400);font-size:12px"></i>
        <input type="text" name="q" value="<?= Html::encode($q) ?>" placeholder="Tên CT, xã, huyện..." class="form-control" style="padding-left:30px">
      </div>
    </div>
    <div style="min-width:140px">
      <div class="form-label">Huyện</div>
      <select name="huyen" class="form-control">
        <option value="">Tất cả</option>
        <?php foreach($huyens as $h): ?>
          <option value="<?= Html::encode($h) ?>" <?= $huyen===$h?'selected':'' ?>><?= Html::encode($h) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="min-width:100px">
      <div class="form-label">Năm</div>
      <select name="nam" class="form-control">
        <option value="">Tất cả</option>
        <?php foreach(range(2025,2030) as $y): ?>
          <option value="<?=$y?>" <?=$nam==$y?'selected':''?>><?=$y?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="min-width:130px">
      <div class="form-label">Ưu tiên</div>
      <select name="priority" class="form-control">
        <option value="">Tất cả</option>
        <option value="khan_cap"    <?=$priority==='khan_cap'?'selected':''?>>Khẩn cấp</option>
        <option value="cao"         <?=$priority==='cao'?'selected':''?>>Cao</option>
        <option value="binh_thuong" <?=$priority==='binh_thuong'?'selected':''?>>Bình thường</option>
      </select>
    </div>
    <div style="min-width:130px">
      <div class="form-label">Tọa độ</div>
      <select name="coords" class="form-control">
        <option value="">Tất cả</option>
        <option value="yes" <?=$coords==='yes'?'selected':''?>>Đã có</option>
        <option value="no"  <?=$coords==='no'?'selected':''?>>Chưa có</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Lọc</button>
    <a href="<?= Url::to(['/cong-trinh/index']) ?>" class="btn btn-secondary"><i class="fa fa-rotate-left"></i></a>
  </form>

  <!-- Table -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Mã CT</th>
          <th>Tên công trình</th>
          <th>Xã / Huyện</th>
          <th>Năm</th>
          <th>Ưu tiên</th>
          <th>Trạng thái</th>
          <th>Tiến độ</th>
          <th>Tọa độ</th>
          <th style="width:110px">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($rows)): ?>
          <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--gray-400)">
            <i class="fa fa-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>
            Không có công trình nào
          </td></tr>
        <?php else: ?>
          <?php foreach($rows as $r): ?>
          <tr>
            <td><span style="font-family:monospace;font-size:12px;background:var(--gray-100);padding:2px 7px;border-radius:5px"><?= Html::encode($r['ma_ct']??'–') ?></span></td>
            <td>
              <div style="font-weight:500;color:var(--gray-900);max-width:220px"><?= Html::encode($r['ten_ct']) ?></div>
              <?php if($r['lien_he_ho_ten']): ?>
                <div style="font-size:11px;color:var(--gray-400);margin-top:2px"><i class="fa fa-user" style="font-size:10px"></i> <?= Html::encode($r['lien_he_ho_ten']) ?></div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-size:12px"><?= Html::encode($r['ten_xa']??'–') ?></div>
              <div style="font-size:11px;color:var(--gray-400)"><?= Html::encode($r['ten_huyen']??'–') ?></div>
            </td>
            <td style="font-weight:600"><?= $r['nam_dau_tu'] ?></td>
            <td><span class="badge <?= $priorityClass[$r['muc_uu_tien']]??'badge-gray' ?>"><?= $priorityLabel[$r['muc_uu_tien']]??$r['muc_uu_tien'] ?></span></td>
            <td><span class="badge <?= $statusClass[$r['trang_thai']]??'badge-gray' ?>"><?= $statusLabel[$r['trang_thai']]??$r['trang_thai'] ?></span></td>
            <td>
              <div style="display:flex;align-items:center;gap:7px">
                <div style="flex:1;height:5px;background:var(--gray-100);border-radius:3px;overflow:hidden;min-width:50px">
                  <div style="height:100%;width:<?= $r['tien_do'] ?>%;background:<?= $r['tien_do']==100?'#16a37f':($r['tien_do']>0?'#f59e0b':'#e5e7eb') ?>;border-radius:3px"></div>
                </div>
                <span style="font-size:11px;font-weight:600;color:var(--gray-500);flex-shrink:0"><?= $r['tien_do'] ?>%</span>
              </div>
            </td>
            <td>
              <?php if($r['has_geom']): ?>
                <span style="font-size:11px;color:#16a37f;font-weight:500"><i class="fa fa-location-dot"></i> Có</span>
              <?php else: ?>
                <span style="font-size:11px;color:#ef4444;font-weight:500"><i class="fa fa-location-dot"></i> Chưa</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex;gap:5px">
                <a href="<?= Url::to(['/cong-trinh/update', 'id'=>$r['id']]) ?>" class="btn btn-secondary btn-sm" title="Sửa">
                  <i class="fa fa-pen"></i>
                </a>
                <?= Html::beginForm(['/cong-trinh/delete','id'=>$r['id']],'post',['style'=>'display:inline']) ?>
                  <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa công trình này?')" title="Xóa">
                    <i class="fa fa-trash"></i>
                  </button>
                <?= Html::endForm() ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if($pages->pageCount > 1): ?>
  <div style="padding:12px 18px;border-top:1px solid var(--gray-100);display:flex;align-items:center;justify-content:space-between">
    <div style="font-size:12px;color:var(--gray-500)">
      Trang <?= $pages->page+1 ?>/<?= $pages->pageCount ?>
    </div>
    <div class="pagination">
      <?php for($i=0;$i<$pages->pageCount;$i++): ?>
        <a href="?page=<?=$i+1?>&q=<?=urlencode($q)?>&huyen=<?=urlencode($huyen)?>&nam=<?=$nam?>&priority=<?=$priority?>&coords=<?=$coords?>"
           class="page-btn <?=$i===$pages->page?'active':''?>"><?=$i+1?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>