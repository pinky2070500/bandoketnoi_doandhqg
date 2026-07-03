<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\helpers\Dhqg;

/**
 * Lớp cơ sở cho backend: bắt buộc đăng nhập + phân quyền theo vai trò/đơn vị (RBAC nhẹ).
 *
 * - vai_tro = 'admin'  → toàn quyền mọi module.
 * - vai_tro = 'don_vi' → chỉ các module nằm trong don_vi.modules.
 */
class BaseAdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST'], 'xoa' => ['POST'], 'xoa-anh' => ['POST']],
            ],
        ];
    }

    /** Bản ghi user hiện tại (ActiveRecord) — có vai_tro, don_vi_id. */
    protected function me()
    {
        return Yii::$app->user->identity;
    }

    protected function laAdmin(): bool
    {
        return ($this->me()->vai_tro ?? 'admin') === 'admin';
    }

    /** Danh sách khoá module người dùng hiện tại được phép. */
    public function cacModuleChoPhep(): array
    {
        if ($this->laAdmin()) {
            return array_keys(Dhqg::MODULES);
        }
        $dvId = $this->me()->don_vi_id ?? null;
        if (!$dvId) {
            return [];
        }
        $json = Yii::$app->db->createCommand('SELECT modules FROM don_vi WHERE id=:i', [':i' => $dvId])->queryScalar();
        $arr = $json ? json_decode($json, true) : [];
        return is_array($arr) ? $arr : [];
    }

    public function quyenModule(string $module): bool
    {
        return in_array($module, $this->cacModuleChoPhep(), true);
    }

    /** Chặn nếu không có quyền với module. */
    protected function batBuocQuyen(string $module): void
    {
        if (!$this->quyenModule($module)) {
            throw new ForbiddenHttpException('Bạn không có quyền với module này (' . Dhqg::moduleLabel($module) . ').');
        }
    }
}
