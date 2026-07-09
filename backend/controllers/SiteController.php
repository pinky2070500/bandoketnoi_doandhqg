<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['index','logout'],
                'rules' => [
                    ['allow' => true, 'actions' => ['login','error']],
                    ['allow' => true, 'actions' => ['index','logout'], 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
        ];
    }

    public function actionIndex()
    {
        $db = Yii::$app->db;

        // Tổng quan
        $tong = $db->createCommand("
            SELECT COUNT(*) AS tong,
                   SUM(CASE WHEN geom IS NOT NULL THEN 1 ELSE 0 END) AS co_toa_do,
                   SUM(CASE WHEN geom IS NULL THEN 1 ELSE 0 END) AS chua_toa_do,
                   SUM(CASE WHEN trang_thai='hoan_thanh' THEN 1 ELSE 0 END) AS hoan_thanh
            FROM doi_tuong
        ")->queryOne();

        // Theo module
        $theoModule = $db->createCommand("
            SELECT module, COUNT(*) AS tong,
                   SUM(CASE WHEN geom IS NOT NULL THEN 1 ELSE 0 END) AS co_toa_do
            FROM doi_tuong GROUP BY module
        ")->queryAll();
        $theoModule = array_column($theoModule, null, 'module');

        // Mới nhất
        $recent = $db->createCommand("
            SELECT d.id, d.ma, d.ten, d.module, d.loai, d.trang_thai, d.nam,
                   d.geom IS NOT NULL AS has_geom
            FROM doi_tuong d ORDER BY d.id DESC LIMIT 8
        ")->queryAll();

        return $this->render('index', compact('tong', 'theoModule', 'recent'));
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) return $this->goHome();
        $this->layout = false; // trang đăng nhập tự chứa HTML (không sidebar)
        $model = new \common\models\LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}