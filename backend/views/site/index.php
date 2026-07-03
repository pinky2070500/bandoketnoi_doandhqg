<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\Dhqg;

/** @var yii\web\View $this */
/** @var array $tong */
/** @var array $theoModule */
/** @var array $recent */

$this->title = 'Dashboard';
$ctrlMap = ['cong_trinh' => 'cong-trinh', 'an_toan' => 'an-toan', 'truyen_thong' => 'truyen-thong'];
?>
<style>
.dash h2{font-size:20px;font-weight:800;margin-bottom:4px}
.dash .sub{color:#6b7280;font-size:13px;margin-bottom:18px}
.dash .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:20px}
.mcard{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:16px;text-decoration:none;color:inherit;display:block;transition:.15s}
.mcard:hover{box-shadow:0 6px 20px rgba(0,0,0,.08);transform:translateY(-2px)}
.mcard .ic{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;margin-bottom:10px}
.mcard .n{font-size:28px;font-weight:900}
.mcard .l{font-size:13px;color:#374151;font-weight:600;margin-top:2px}
.mcard .meta{font-size:12px;color:#9ca3af;margin-top:6px}
.ov{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.ov .b{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px}
.ov .b .n{font-size:22px;font-weight:800}.ov .b .l{font-size:12px;color:#6b7280}
.tbl{width:100%;border-collapse:collapse;background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden}
.tbl th{background:#f9fafb;text-align:left;padding:10px 12px;font-size:12px;color:#6b7280;font-weight:600}
.tbl td{padding:9px 12px;font-size:13px;border-top:1px solid #f3f4f6}
.badge{display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700}
</style>

<div class="dash">
  <h2>Dashboard</h2>
  <div class="sub">Hệ thống Bản đồ số Khu đô thị ĐHQG-HCM · Tổng quan quản trị</div>

  <div class="ov">
    <div class="b"><div class="n"><?= (int)$tong['tong'] ?></div><div class="l">Tổng đối tượng</div></div>
    <div class="b"><div class="n" style="color:#16a37f"><?= (int)$tong['co_toa_do'] ?></div><div class="l">Đã có toạ độ</div></div>
    <div class="b"><div class="n" style="color:#f59e0b"><?= (int)$tong['chua_toa_do'] ?></div><div class="l">Chưa có toạ độ</div></div>
    <div class="b"><div class="n" style="color:#3b82f6"><?= (int)$tong['hoan_thanh'] ?></div><div class="l">Hoàn thành</div></div>
  </div>

  <div class="grid">
    <?php foreach (Dhqg::MODULES as $mk => $m): $r = $theoModule[$mk] ?? ['tong' => 0, 'co_toa_do' => 0]; ?>
      <a class="mcard" href="<?= Url::to(['/' . $ctrlMap[$mk] . '/index']) ?>">
        <div class="ic" style="background:<?= $m['mau'] ?>"><i class="fa <?= $m['icon'] ?>"></i></div>
        <div class="n" style="color:<?= $m['mau'] ?>"><?= (int)$r['tong'] ?></div>
        <div class="l"><?= Html::encode($m['label_ngan']) ?></div>
        <div class="meta"><?= (int)$r['co_toa_do'] ?> có toạ độ · Bấm để quản lý</div>
      </a>
    <?php endforeach ?>
  </div>

  <h3 style="font-size:15px;font-weight:700;margin-bottom:10px">Mới cập nhật</h3>
  <table class="tbl">
    <thead><tr><th>Mã</th><th>Tên</th><th>Module</th><th>Loại</th><th>Năm</th><th>Toạ độ</th></tr></thead>
    <tbody>
      <?php if (!$recent): ?>
        <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px">Chưa có dữ liệu. Vào từng module để thêm.</td></tr>
      <?php endif ?>
      <?php foreach ($recent as $r): $m = Dhqg::MODULES[$r['module']] ?? null; ?>
        <tr>
          <td><code><?= Html::encode($r['ma']) ?></code></td>
          <td><strong><?= Html::encode($r['ten']) ?></strong></td>
          <td><span class="badge" style="background:<?= ($m['mau'] ?? '#888') ?>22;color:<?= $m['mau'] ?? '#888' ?>"><?= Html::encode($m['label_ngan'] ?? $r['module']) ?></span></td>
          <td><?= Html::encode(Dhqg::loaiLabel($r['module'], $r['loai'])) ?></td>
          <td><?= $r['nam'] ? (int)$r['nam'] : '—' ?></td>
          <td><?= $r['has_geom'] ? '<span style="color:#16a37f">● Có</span>' : '<span style="color:#ef4444">○ Chưa</span>' ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <p style="margin-top:16px"><a href="http://bandoketnoi.local/dashboard" target="_blank" style="color:#16a37f;font-weight:600;text-decoration:none"><i class="fa fa-chart-pie"></i> Xem Dashboard tổng hợp (biểu đồ) ↗</a></p>
</div>
