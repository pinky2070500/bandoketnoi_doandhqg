<?php
/** @var yii\web\View $this */
/** @var string $content */
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Html::encode($this->title) ?> – Quản trị Bản đồ kết nối</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
:root{
  --green:#16a37f;--green-dark:#0d7a5f;--green-lite:#e6f7f2;
  --sidebar-w:240px;--top-h:54px;
  --gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;
  --gray-400:#9ca3af;--gray-500:#6b7280;--gray-600:#4b5563;
  --gray-700:#374151;--gray-800:#1f2937;--gray-900:#111827;
  --red:#ef4444;--orange:#f59e0b;--blue:#3b82f6;
  --shadow-sm:0 1px 3px rgba(0,0,0,.08);
  --shadow-md:0 4px 12px rgba(0,0,0,.1);
  --radius:10px;--radius-sm:7px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;font-size:14px;background:var(--gray-50);color:var(--gray-800)}
a{text-decoration:none;color:inherit}

/* TOPBAR */
#topbar{
  position:fixed;top:0;left:0;right:0;height:var(--top-h);
  background:#fff;border-bottom:1px solid var(--gray-200);
  display:flex;align-items:center;padding:0 20px;gap:14px;z-index:200;
}
.tb-logo{display:flex;align-items:center;gap:9px;flex-shrink:0}
.tb-logo-icon{
  width:34px;height:34px;border-radius:9px;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;
}
.tb-logo-icon i{color:#fff;font-size:14px}
.tb-brand{font-size:14px;font-weight:700;color:var(--gray-900)}
.tb-brand span{font-weight:400;color:var(--gray-400);margin-left:6px;font-size:12px}
.tb-spacer{flex:1}
.tb-link{
  display:flex;align-items:center;gap:6px;padding:6px 12px;
  border-radius:var(--radius-sm);font-size:13px;color:var(--gray-600);
  transition:all .18s;
}
.tb-link:hover{background:var(--gray-100);color:var(--gray-900)}
.tb-link i{font-size:13px}
.tb-avatar{
  width:32px;height:32px;border-radius:50%;
  background:linear-gradient(135deg,var(--green),var(--green-dark));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:11px;font-weight:700;cursor:pointer;
}

/* SIDEBAR */
#sidebar{
  position:fixed;top:var(--top-h);left:0;bottom:0;
  width:var(--sidebar-w);background:#fff;
  border-right:1px solid var(--gray-200);
  overflow-y:auto;z-index:100;padding:12px 10px;
}
.sb-section{margin-bottom:20px}
.sb-section-title{
  font-size:10px;font-weight:700;letter-spacing:.8px;
  color:var(--gray-400);text-transform:uppercase;
  padding:0 8px;margin-bottom:6px;
}
.nav-item{
  display:flex;align-items:center;gap:9px;
  padding:8px 10px;border-radius:var(--radius-sm);
  font-size:13px;color:var(--gray-600);font-weight:500;
  transition:all .18s;margin-bottom:2px;cursor:pointer;
}
.nav-item:hover{background:var(--gray-100);color:var(--gray-900)}
.nav-item.active{background:var(--green-lite);color:var(--green-dark)}
.nav-item i{width:16px;text-align:center;font-size:14px}
.nav-badge{
  margin-left:auto;font-size:10px;font-weight:700;
  padding:1px 7px;border-radius:10px;
  background:var(--red);color:#fff;
}

/* CONTENT */
#content{
  margin-left:var(--sidebar-w);margin-top:var(--top-h);
  min-height:calc(100vh - var(--top-h));padding:24px;
}

/* PAGE HEADER */
.page-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:20px;
}
.page-title{font-size:20px;font-weight:700;color:var(--gray-900);letter-spacing:-.3px}
.page-sub{font-size:13px;color:var(--gray-500);margin-top:2px}

/* BUTTONS */
.btn{
  display:inline-flex;align-items:center;gap:7px;
  padding:8px 16px;border-radius:var(--radius-sm);
  font-size:13px;font-weight:500;cursor:pointer;
  border:none;font-family:inherit;transition:all .18s;
}
.btn-primary{background:var(--green);color:#fff}
.btn-primary:hover{background:var(--green-dark)}
.btn-secondary{background:var(--gray-100);color:var(--gray-700);border:1px solid var(--gray-200)}
.btn-secondary:hover{background:var(--gray-200)}
.btn-danger{background:#fef2f2;color:var(--red);border:1px solid #fecaca}
.btn-danger:hover{background:#fee2e2}
.btn-sm{padding:5px 11px;font-size:12px}

/* CARDS */
.card{
  background:#fff;border-radius:var(--radius);
  border:1px solid var(--gray-200);
  box-shadow:var(--shadow-sm);
}
.card-header{
  padding:14px 18px;border-bottom:1px solid var(--gray-100);
  display:flex;align-items:center;justify-content:space-between;
}
.card-title{font-size:14px;font-weight:600;color:var(--gray-800)}
.card-body{padding:18px}

/* TABLE */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
th{
  padding:10px 14px;text-align:left;font-size:11px;
  font-weight:700;color:var(--gray-500);text-transform:uppercase;
  letter-spacing:.5px;background:var(--gray-50);
  border-bottom:1px solid var(--gray-200);white-space:nowrap;
}
td{
  padding:11px 14px;border-bottom:1px solid var(--gray-100);
  font-size:13px;color:var(--gray-700);vertical-align:middle;
}
tr:last-child td{border-bottom:none}
tr:hover td{background:var(--gray-50)}

/* BADGES */
.badge{
  display:inline-flex;align-items:center;gap:4px;
  font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;
}
.badge-red   {background:#fef2f2;color:#b91c1c}
.badge-amber {background:#fffbeb;color:#92400e}
.badge-green {background:var(--green-lite);color:var(--green-dark)}
.badge-gray  {background:var(--gray-100);color:var(--gray-600)}
.badge-blue  {background:#eff6ff;color:#1d4ed8}

/* STATS */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:20px}
.stat-card{
  background:#fff;border-radius:var(--radius);border:1px solid var(--gray-200);
  padding:16px 18px;
}
.stat-label{font-size:11px;color:var(--gray-500);font-weight:500;margin-bottom:6px}
.stat-val{font-size:26px;font-weight:700;color:var(--gray-900);letter-spacing:-.5px}
.stat-sub{font-size:11px;color:var(--gray-400);margin-top:3px}
.stat-icon{
  float:right;width:40px;height:40px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;font-size:18px;
}

/* FLASH */
.flash{
  padding:12px 16px;border-radius:var(--radius-sm);
  margin-bottom:16px;font-size:13px;font-weight:500;
  display:flex;align-items:center;gap:8px;
}
.flash-success{background:#f0fdf9;border:1px solid #a7f3d0;color:#065f46}
.flash-error  {background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.flash-warning{background:#fffbeb;border:1px solid #fde68a;color:#92400e}

/* FORM */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-group{margin-bottom:0}
.form-group.full{grid-column:1/-1}
.form-label{font-size:12px;font-weight:600;color:var(--gray-700);display:block;margin-bottom:5px}
.form-label .req{color:var(--red)}
.form-control{
  width:100%;padding:8px 11px;border:1.5px solid var(--gray-200);
  border-radius:var(--radius-sm);font-size:13px;color:var(--gray-800);
  font-family:inherit;outline:none;transition:border-color .2s;background:#fff;
}
.form-control:focus{border-color:var(--green)}
.form-control.error{border-color:var(--red)}
.form-hint{font-size:11px;color:var(--gray-400);margin-top:4px}
.form-error{font-size:11px;color:var(--red);margin-top:4px}
select.form-control{cursor:pointer}
textarea.form-control{resize:vertical;min-height:80px}

/* PAGINATION */
.pagination{display:flex;gap:4px;align-items:center;margin-top:14px}
.page-btn{
  width:32px;height:32px;display:flex;align-items:center;justify-content:center;
  border-radius:var(--radius-sm);border:1px solid var(--gray-200);
  font-size:13px;color:var(--gray-600);cursor:pointer;transition:all .18s;
}
.page-btn:hover{background:var(--gray-100)}
.page-btn.active{background:var(--green);color:#fff;border-color:var(--green)}
</style>
</head>
<body>

<!-- TOPBAR -->
<div id="topbar">
  <div class="tb-logo">
    <div class="tb-logo-icon"><i class="fa-solid fa-map-location-dot"></i></div>
    <div class="tb-brand">Bản đồ kết nối <span>Quản trị</span></div>
  </div>
  <div class="tb-spacer"></div>
  <a class="tb-link" href="http://bandoketnoi.local" target="_blank">
    <i class="fa fa-arrow-up-right-from-square"></i> Xem bản đồ
  </a>
  <a class="tb-link" href="<?= Url::to(['/site/logout']) ?>" onclick="return confirm('Đăng xuất?')">
    <i class="fa fa-right-from-bracket"></i> Đăng xuất
  </a>
  <div class="tb-avatar"><?= strtoupper(substr($user->identity->username ?? 'AD', 0, 2)) ?></div>
</div>

<!-- SIDEBAR -->
<div id="sidebar">
  <div class="sb-section">
    <div class="sb-section-title">Tổng quan</div>
    <a class="nav-item <?= Yii::$app->controller->id==='site'?'active':'' ?>" href="<?= Url::to(['/site/index']) ?>">
      <i class="fa fa-gauge"></i> Dashboard
    </a>
  </div>
  <div class="sb-section">
    <div class="sb-section-title">Dữ liệu</div>
    <a class="nav-item <?= Yii::$app->controller->id==='cong-trinh'?'active':'' ?>" href="<?= Url::to(['/cong-trinh/index']) ?>">
      <i class="fa fa-bridge"></i> Công trình
      <?php
      $chua = (int)\Yii::$app->db->createCommand("SELECT COUNT(*) FROM congtrinh WHERE geom IS NULL")->queryScalar();
      if($chua > 0): ?>
        <span class="nav-badge"><?= $chua ?></span>
      <?php endif; ?>
    </a>
  </div>
  <div class="sb-section">
    <div class="sb-section-title">Hệ thống</div>
    <a class="nav-item" href="<?= Url::to(['/site/logout']) ?>" onclick="return confirm('Đăng xuất?')">
      <i class="fa fa-right-from-bracket"></i> Đăng xuất
    </a>
  </div>
</div>

<!-- CONTENT -->
<div id="content">
  <?php foreach(Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="flash flash-<?= $type ?>">
      <i class="fa fa-<?= $type==='success'?'check-circle':($type==='error'?'circle-xmark':'triangle-exclamation') ?>"></i>
      <?= Html::encode(is_array($msg) ? implode(', ',$msg) : $msg) ?>
    </div>
  <?php endforeach; ?>
  <?= $content ?>
</div>

</body>
</html>