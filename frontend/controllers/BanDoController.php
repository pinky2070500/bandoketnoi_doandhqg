<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BanDoController extends Controller
{
    public $layout = false; // Dùng HTML thuần trong view

    /** Bản đồ công khai đa lớp. */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /** Dashboard tổng hợp (màn hình lãnh đạo). */
    public function actionDashboard()
    {
        return $this->render('dashboard');
    }

    /** Trang chi tiết công khai — đích của mã QR. */
    public function actionChiTiet($id)
    {
        $exists = Yii::$app->db->createCommand('SELECT 1 FROM doi_tuong WHERE id=:i', [':i' => (int) $id])->queryScalar();
        if (!$exists) {
            throw new NotFoundHttpException('Không tìm thấy đối tượng #' . (int) $id);
        }
        return $this->render('chi-tiet', ['id' => (int) $id]);
    }
}
