<?php
namespace frontend\controllers;

use yii\web\Controller;

class BanDoController extends Controller
{
    public $layout = false; // Tắt layout Yii2, dùng HTML thuần trong view

    public function actionIndex()
    {
        return $this->render('index');
    }
}