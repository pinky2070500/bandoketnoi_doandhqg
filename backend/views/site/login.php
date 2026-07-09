<?php
/** @var yii\web\View $this */
/** @var \common\models\LoginForm $model */
use yii\helpers\Html;

$this->title = 'Đăng nhập — Quản trị Bản đồ số ĐHQG-HCM';
$fe = Yii::$app->params['frontendUrl'] ?? '';
$req = Yii::$app->request;
$err = $model->hasErrors();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Html::encode($this->title) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--brand:#123c8a;--brand-2:#1b52c0;--brand-d:#0c2a63;--brand-l:#eaf1fb;--ink:#0f172a;--gray:#64748b;--line:#e2e8f0;--ring:rgba(27,82,192,.28)}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:'Inter',system-ui,sans-serif;color:var(--ink);display:flex;min-height:100vh}
.left{flex:1.15;background:linear-gradient(140deg,#1b52c0,#0c2a63);color:#fff;position:relative;overflow:hidden;
  display:flex;flex-direction:column;justify-content:center;padding:56px}
.left::before,.left::after{content:"";position:absolute;border-radius:50%;background:rgba(255,255,255,.06)}
.left::before{width:420px;height:420px;top:-120px;right:-120px}
.left::after{width:300px;height:300px;bottom:-100px;left:-80px;background:rgba(255,255,255,.05)}
.left .in{position:relative;z-index:2;max-width:460px}
.chip{width:74px;height:74px;border-radius:18px;background:#fff;display:flex;align-items:center;justify-content:center;padding:9px;box-shadow:0 12px 30px rgba(0,0,0,.2);margin-bottom:26px}
.chip img{max-width:100%;max-height:100%;object-fit:contain}
.left h1{font-size:30px;font-weight:900;line-height:1.2;letter-spacing:-.5px}
.left .tag{margin-top:12px;font-size:15px;opacity:.9;line-height:1.6}
.left .feats{margin-top:34px;display:flex;flex-direction:column;gap:14px}
.left .feat{display:flex;align-items:center;gap:13px;font-size:14.5px;font-weight:500}
.left .feat .fi{width:38px;height:38px;border-radius:11px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:15px}
.left .foot{position:absolute;bottom:26px;left:56px;z-index:2;font-size:12px;opacity:.7}
.right{flex:1;display:flex;align-items:center;justify-content:center;padding:40px;background:#f8fafc}
.card{width:100%;max-width:400px}
.card .logo{height:66px;margin-bottom:22px}
.card .logo img{height:100%;width:auto;object-fit:contain}
.card h2{font-size:23px;font-weight:800;letter-spacing:-.3px}
.card .sub{color:var(--gray);font-size:14px;margin-top:5px;margin-bottom:26px}
.fld{margin-bottom:16px}
.fld label{display:block;font-size:13px;font-weight:600;margin-bottom:7px}
.inp{position:relative}
.inp i.lead{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--gray);font-size:14px}
.inp input{width:100%;padding:13px 42px 13px 42px;border:1.5px solid var(--line);border-radius:11px;font-size:14.5px;font-family:inherit;transition:.15s;background:#fff}
.inp input:focus{outline:none;border-color:var(--brand-2);box-shadow:0 0 0 4px var(--ring)}
.togglepw{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--gray);font-size:14px;cursor:pointer}
.rowb{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px}
.rowb label{display:flex;align-items:center;gap:8px;font-size:13.5px;color:var(--gray);cursor:pointer}
.rowb input{width:17px;height:17px;accent-color:var(--brand-2)}
.btn{width:100%;padding:14px;border:none;border-radius:11px;background:var(--brand);color:#fff;font-size:15px;font-weight:700;cursor:pointer;transition:.15s;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:9px}
.btn:hover{background:var(--brand-d)}
.btn:active{transform:translateY(1px)}
.alert{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 14px;border-radius:11px;font-size:13.5px;margin-bottom:20px;display:flex;gap:9px;align-items:flex-start}
.foot2{text-align:center;color:var(--gray);font-size:12px;margin-top:26px}
@media(max-width:900px){.left{display:none}.right{flex:1}}
</style>
</head>
<body>
<div class="left">
  <div class="in">
    <div class="chip"><img src="<?= $fe ?>/img/logo-mark-160.png" alt="ĐHQG-HCM"></div>
    <h1>Hệ thống Bản đồ số<br>Khu đô thị ĐHQG-HCM</h1>
    <div class="tag">Nền tảng WebGIS quản lý &amp; phát triển Khu đô thị Đại học Quốc gia TP. Hồ Chí Minh theo định hướng “Xanh – Thông minh – Bản sắc”.</div>
    <div class="feats">
      <div class="feat"><span class="fi"><i class="fa fa-flag"></i></span> Công trình thanh niên &amp; điểm check-in</div>
      <div class="feat"><span class="fi"><i class="fa fa-triangle-exclamation"></i></span> An toàn khu đô thị</div>
      <div class="feat"><span class="fi"><i class="fa fa-bullhorn"></i></span> Truyền thông trực quan</div>
    </div>
  </div>
  <div class="foot">© <?= date('Y') ?> Đại học Quốc gia TP.HCM · Trang quản trị</div>
</div>

<div class="right">
  <div class="card">
    <div class="logo"><img src="<?= $fe ?>/img/logo-dhqg.png" alt="ĐHQG-HCM"></div>
    <h2>Đăng nhập quản trị</h2>
    <div class="sub">Vui lòng đăng nhập bằng tài khoản được cấp.</div>

    <?php if ($err): ?>
      <div class="alert"><i class="fa fa-circle-exclamation" style="margin-top:2px"></i>
        <span><?= Html::encode($model->getFirstError('password') ?: ($model->getFirstError('username') ?: 'Tên đăng nhập hoặc mật khẩu không đúng.')) ?></span></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="<?= $req->csrfParam ?>" value="<?= $req->csrfToken ?>">
      <div class="fld">
        <label>Tên đăng nhập</label>
        <div class="inp"><i class="fa fa-user lead"></i>
          <input name="LoginForm[username]" value="<?= Html::encode($model->username) ?>" autofocus placeholder="Nhập tên đăng nhập"></div>
      </div>
      <div class="fld">
        <label>Mật khẩu</label>
        <div class="inp"><i class="fa fa-lock lead"></i>
          <input id="pw" name="LoginForm[password]" type="password" placeholder="Nhập mật khẩu">
          <i class="fa fa-eye togglepw" onclick="var p=document.getElementById('pw');p.type=p.type==='password'?'text':'password';this.classList.toggle('fa-eye');this.classList.toggle('fa-eye-slash')"></i></div>
      </div>
      <div class="rowb">
        <label><input type="checkbox" name="LoginForm[rememberMe]" value="1" checked> Ghi nhớ đăng nhập</label>
      </div>
      <button class="btn" type="submit"><i class="fa fa-right-to-bracket"></i> Đăng nhập</button>
    </form>

    <div class="foot2">Hệ thống nội bộ · Truy cập được phân quyền theo đơn vị</div>
  </div>
</div>
</body>
</html>
