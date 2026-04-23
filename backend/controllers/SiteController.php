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

        // Thống kê tổng quan
        $tong = $db->createCommand("
            SELECT
                COUNT(*)                                                         AS tong,
                SUM(CASE WHEN geom IS NOT NULL THEN 1 ELSE 0 END)               AS co_toa_do,
                SUM(CASE WHEN trang_thai='hoan_thanh'    THEN 1 ELSE 0 END)     AS hoan_thanh,
                SUM(CASE WHEN trang_thai='dang_thi_cong' THEN 1 ELSE 0 END)     AS dang_thi_cong,
                SUM(CASE WHEN trang_thai='cho_dau_tu'    THEN 1 ELSE 0 END)     AS cho_dau_tu,
                SUM(CASE WHEN muc_uu_tien='khan_cap'     THEN 1 ELSE 0 END)     AS khan_cap,
                SUM(CASE WHEN muc_uu_tien='cao'          THEN 1 ELSE 0 END)     AS cao,
                SUM(CASE WHEN muc_uu_tien='binh_thuong'  THEN 1 ELSE 0 END)     AS binh_thuong,
                ROUND(AVG(tien_do),1)                                            AS avg_tien_do
            FROM congtrinh
        ")->queryOne();

        // Theo huyện
        $theoHuyen = $db->createCommand("
            SELECT ten_huyen,
                COUNT(*) AS tong,
                SUM(CASE WHEN trang_thai='hoan_thanh' THEN 1 ELSE 0 END) AS hoan_thanh,
                SUM(CASE WHEN muc_uu_tien='khan_cap'  THEN 1 ELSE 0 END) AS khan_cap
            FROM congtrinh
            WHERE ten_huyen IS NOT NULL
            GROUP BY ten_huyen
            ORDER BY tong DESC
        ")->queryAll();

        // Theo năm
        $theoNam = $db->createCommand("
            SELECT nam_dau_tu,
                COUNT(*) AS tong,
                SUM(CASE WHEN trang_thai='hoan_thanh' THEN 1 ELSE 0 END) AS hoan_thanh,
                SUM(CASE WHEN geom IS NOT NULL THEN 1 ELSE 0 END)        AS co_toa_do
            FROM congtrinh
            WHERE nam_dau_tu IS NOT NULL
            GROUP BY nam_dau_tu
            ORDER BY nam_dau_tu
        ")->queryAll();

        // Theo loại
        $theoLoai = $db->createCommand("
            SELECT loai_ct, COUNT(*) AS tong
            FROM congtrinh
            GROUP BY loai_ct
            ORDER BY tong DESC
        ")->queryAll();

        // 5 công trình mới nhất
        $recent = $db->createCommand("
            SELECT id, ma_ct, ten_ct, ten_xa, ten_huyen,
                   muc_uu_tien, trang_thai, tien_do, nam_dau_tu,
                   geom IS NOT NULL AS has_geom
            FROM congtrinh
            ORDER BY id DESC
            LIMIT 5
        ")->queryAll();

        // Công trình khẩn cấp chưa có tọa độ
        $urgent = $db->createCommand("
            SELECT id, ma_ct, ten_ct, ten_xa, ten_huyen, nam_dau_tu
            FROM congtrinh
            WHERE muc_uu_tien = 'khan_cap' AND geom IS NULL
            ORDER BY nam_dau_tu, id
            LIMIT 5
        ")->queryAll();

        return $this->render('index', compact(
            'tong','theoHuyen','theoNam','theoLoai','recent','urgent'
        ));
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) return $this->goHome();
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