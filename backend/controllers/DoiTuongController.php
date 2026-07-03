<?php
namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use common\helpers\Dhqg;
use backend\components\Uploader;

/**
 * CRUD chung cho 1 "đối tượng" trên bản đồ, dùng lại cho cả 3 module.
 * Các controller module (CongTrinh/AnToan/TruyenThong) kế thừa và đặt $module.
 * View dùng chung ở backend/views/doi-tuong/.
 */
class DoiTuongController extends BaseAdminController
{
    /** Khoá module — subclass PHẢI ghi đè. */
    protected $moduleKey = '';

    /** Tiền tố mã theo module. */
    protected function prefixMa(): string
    {
        return ['cong_trinh' => 'CT', 'an_toan' => 'AT', 'truyen_thong' => 'TT'][$this->moduleKey] ?? 'DT';
    }

    public function getViewPath()
    {
        return Yii::getAlias('@backend/views/doi-tuong');
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->batBuocQuyen($this->moduleKey);
        return true;
    }

    /** Cấu hình module hiện tại (label, loai, màu...). */
    protected function cfg(): array
    {
        return Dhqg::MODULES[$this->moduleKey];
    }

    // ── DANH SÁCH ─────────────────────────────────────
    public function actionIndex()
    {
        $db = Yii::$app->db;
        $where = ['module = :m'];
        $params = [':m' => $this->moduleKey];

        $q = trim((string) Yii::$app->request->get('q', ''));
        $loai = trim((string) Yii::$app->request->get('loai', ''));
        $nam = trim((string) Yii::$app->request->get('nam', ''));
        $trang_thai = trim((string) Yii::$app->request->get('trang_thai', ''));
        $coords = Yii::$app->request->get('coords', '');

        if ($q !== '') {
            $where[] = '(ten ILIKE :q OR ma ILIKE :q OR mo_ta ILIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }
        if ($loai !== '') {
            $where[] = 'loai = :loai';
            $params[':loai'] = $loai;
        }
        if ($nam !== '') {
            $where[] = 'nam = :nam';
            $params[':nam'] = (int) $nam;
        }
        if ($trang_thai !== '') {
            $where[] = 'trang_thai = :tt';
            $params[':tt'] = $trang_thai;
        }
        if ($coords === 'yes') {
            $where[] = 'geom IS NOT NULL';
        }
        if ($coords === 'no') {
            $where[] = 'geom IS NULL';
        }
        $whereStr = implode(' AND ', $where);

        $total = (int) $db->createCommand("SELECT COUNT(*) FROM doi_tuong WHERE $whereStr", $params)->queryScalar();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => 20]);

        $rows = $db->createCommand("
            SELECT d.id, d.ma, d.ten, d.loai, d.nam, d.trang_thai,
                   d.geom IS NOT NULL AS has_geom,
                   dv.ten AS don_vi_thuc_hien,
                   (SELECT count(*) FROM hinh_anh h WHERE h.doi_tuong_id = d.id) AS so_anh
            FROM doi_tuong d
            LEFT JOIN don_vi dv ON dv.id = d.don_vi_thuc_hien_id
            WHERE $whereStr
            ORDER BY d.nam DESC NULLS LAST, d.id DESC
            LIMIT :limit OFFSET :offset
        ", array_merge($params, [':limit' => $pages->pageSize, ':offset' => $pages->offset]))->queryAll();

        $thongke = $db->createCommand("
            SELECT COUNT(*) tong,
                   SUM(CASE WHEN geom IS NULL THEN 1 ELSE 0 END) chua_toa_do,
                   SUM(CASE WHEN trang_thai='hoan_thanh' THEN 1 ELSE 0 END) hoan_thanh
            FROM doi_tuong WHERE module = :m
        ", [':m' => $this->moduleKey])->queryOne();

        return $this->render('index', [
            'module' => $this->moduleKey,
            'cfg' => $this->cfg(),
            'rows' => $rows,
            'pages' => $pages,
            'total' => $total,
            'thongke' => $thongke,
            'q' => $q,
            'loai' => $loai,
            'nam' => $nam,
            'trang_thai' => $trang_thai,
            'coords' => $coords,
        ]);
    }

    // ── XEM ───────────────────────────────────────────
    public function actionView($id)
    {
        $data = $this->layDoiTuong((int) $id);
        $anh = $this->layAnh((int) $id);
        return $this->render('view', ['module' => $this->moduleKey, 'cfg' => $this->cfg(), 'data' => $data, 'anh' => $anh]);
    }

    // ── TẠO ───────────────────────────────────────────
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $errors = $this->validate($data);
            if (empty($errors)) {
                try {
                    $id = $this->luu(null, $data);
                    Yii::$app->session->setFlash('success', 'Đã thêm "' . trim($data['ten']) . '" (#' . $id . ').');
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Lỗi: ' . $e->getMessage());
                }
            }
            return $this->render('form', $this->formData($data, $errors, true, null, []));
        }

        $lastId = (int) Yii::$app->db->createCommand('SELECT MAX(id) FROM doi_tuong')->queryScalar();
        $data = [
            'ma' => $this->prefixMa() . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT),
            'ten' => '', 'loai' => array_key_first($this->cfg()['loai']),
            'nam' => date('Y'), 'trang_thai' => 'de_xuat',
            'don_vi_thuc_hien_id' => '', 'don_vi_quan_ly_id' => '',
            'mo_ta' => '', 'noi_dung' => '', 'lat' => null, 'lng' => null,
        ];
        return $this->render('form', $this->formData($data, [], true, null, []));
    }

    // ── SỬA ───────────────────────────────────────────
    public function actionUpdate($id)
    {
        $id = (int) $id;
        $row = $this->layDoiTuong($id);

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $errors = $this->validate($data);
            if (empty($errors)) {
                try {
                    $this->luu($id, $data);
                    Yii::$app->session->setFlash('success', 'Đã cập nhật thành công.');
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Lỗi: ' . $e->getMessage());
                }
            }
            return $this->render('form', $this->formData(array_merge($row, $data), $errors, false, $id, $this->layAnh($id)));
        }
        return $this->render('form', $this->formData($row, [], false, $id, $this->layAnh($id)));
    }

    // ── XOÁ ───────────────────────────────────────────
    public function actionDelete($id)
    {
        $id = (int) $id;
        foreach ($this->layAnh($id) as $a) {
            Uploader::xoaFile($a['url']);
        }
        Yii::$app->db->createCommand()->delete('doi_tuong', 'id=:i', [':i' => $id])->execute();
        Yii::$app->session->setFlash('success', 'Đã xoá #' . $id);
        return $this->redirect(['index']);
    }

    // ── XOÁ 1 ẢNH (AJAX/POST) ─────────────────────────
    public function actionXoaAnh($id)
    {
        $row = Yii::$app->db->createCommand('SELECT h.url, h.doi_tuong_id FROM hinh_anh h WHERE h.id=:i', [':i' => (int) $id])->queryOne();
        if ($row) {
            Uploader::xoaFile($row['url']);
            Yii::$app->db->createCommand()->delete('hinh_anh', 'id=:i', [':i' => (int) $id])->execute();
        }
        Yii::$app->session->setFlash('success', 'Đã xoá ảnh.');
        return $this->redirect(['update', 'id' => $row['doi_tuong_id'] ?? '']);
    }

    // ── HÀM DÙNG CHUNG ────────────────────────────────
    protected function layDoiTuong(int $id): array
    {
        $row = Yii::$app->db->createCommand("
            SELECT *, ST_X(geom::geometry) AS lng, ST_Y(geom::geometry) AS lat
            FROM doi_tuong WHERE id=:i AND module=:m
        ", [':i' => $id, ':m' => $this->moduleKey])->queryOne();
        if (!$row) {
            throw new NotFoundHttpException("Không tìm thấy #$id trong module " . Dhqg::moduleLabel($this->moduleKey));
        }
        return $row;
    }

    /** Tên đơn vị theo id (dùng trong view). */
    public function layTenDonVi($id): string
    {
        if (!$id) {
            return '—';
        }
        $t = Yii::$app->db->createCommand('SELECT ten FROM don_vi WHERE id=:i', [':i' => (int) $id])->queryScalar();
        return $t ?: '—';
    }

    protected function layAnh(int $id): array
    {
        return Yii::$app->db->createCommand(
            'SELECT id, url, loai_anh, thu_tu FROM hinh_anh WHERE doi_tuong_id=:i ORDER BY thu_tu, id',
            [':i' => $id]
        )->queryAll();
    }

    protected function validate(array $d): array
    {
        $e = [];
        if (trim($d['ten'] ?? '') === '') {
            $e['ten'] = 'Tên không được để trống';
        }
        if (!empty($d['nam']) && !is_numeric($d['nam'])) {
            $e['nam'] = 'Năm không hợp lệ';
        }
        if (!empty($d['lat']) && !is_numeric($d['lat'])) {
            $e['lat'] = 'Vĩ độ không hợp lệ';
        }
        if (!empty($d['lng']) && !is_numeric($d['lng'])) {
            $e['lng'] = 'Kinh độ không hợp lệ';
        }
        return $e;
    }

    /** Insert/Update + toạ độ + ảnh. Trả id. */
    protected function luu($id, array $d): int
    {
        $db = Yii::$app->db;
        $fields = [
            'ten' => trim($d['ten']),
            'loai' => $d['loai'] ?: null,
            'trang_thai' => $d['trang_thai'] ?? 'de_xuat',
            'nam' => !empty($d['nam']) ? (int) $d['nam'] : null,
            'don_vi_thuc_hien_id' => !empty($d['don_vi_thuc_hien_id']) ? (int) $d['don_vi_thuc_hien_id'] : null,
            'don_vi_quan_ly_id' => !empty($d['don_vi_quan_ly_id']) ? (int) $d['don_vi_quan_ly_id'] : null,
            'mo_ta' => trim($d['mo_ta'] ?? ''),
            'noi_dung' => trim($d['noi_dung'] ?? ''),
        ];

        if ($id === null) {
            $fields['ma'] = strtoupper(trim($d['ma'] ?? '')) ?: null;
            $fields['module'] = $this->moduleKey;
            $fields['created_by'] = Yii::$app->user->id;
            $db->createCommand()->insert('doi_tuong', $fields)->execute();
            $id = (int) $db->getLastInsertID('doi_tuong_id_seq');
        } else {
            $fields['ma'] = strtoupper(trim($d['ma'] ?? '')) ?: null;
            $fields['updated_at'] = date('Y-m-d H:i:s');
            $db->createCommand()->update('doi_tuong', $fields, 'id=:i AND module=:m', [':i' => (int) $id, ':m' => $this->moduleKey])->execute();
        }
        $id = (int) $id;

        // Toạ độ
        $lat = trim((string) ($d['lat'] ?? ''));
        $lng = trim((string) ($d['lng'] ?? ''));
        if ($lat !== '' && $lng !== '' && is_numeric($lat) && is_numeric($lng)) {
            $db->createCommand(
                'UPDATE doi_tuong SET geom = ST_SetSRID(ST_MakePoint(:lng,:lat),4326) WHERE id=:i',
                [':lng' => (float) $lng, ':lat' => (float) $lat, ':i' => $id]
            )->execute();
        } elseif (!empty($d['clear_geom'])) {
            $db->createCommand('UPDATE doi_tuong SET geom=NULL WHERE id=:i', [':i' => $id])->execute();
        }

        // Ảnh mới (một loại ảnh áp cho tất cả file trong lần lưu này)
        $anhMoi = Uploader::luuNhieu($this->moduleKey, $id, 'anh_moi');
        $loaiAnh = $d['loai_anh_moi'] ?? 'khac';
        if (!isset(Dhqg::LOAI_ANH[$loaiAnh])) {
            $loaiAnh = 'khac';
        }
        $stt = (int) $db->createCommand('SELECT COALESCE(MAX(thu_tu),0) FROM hinh_anh WHERE doi_tuong_id=:i', [':i' => $id])->queryScalar();
        foreach ($anhMoi as $a) {
            $db->createCommand()->insert('hinh_anh', [
                'doi_tuong_id' => $id,
                'url' => $a['path'],
                'loai_anh' => $loaiAnh,
                'thu_tu' => ++$stt,
            ])->execute();
        }
        return $id;
    }

    protected function formData(array $data, array $errors, bool $isNew, $id, array $anh): array
    {
        return [
            'module' => $this->moduleKey,
            'cfg' => $this->cfg(),
            'data' => $data,
            'errors' => $errors,
            'isNew' => $isNew,
            'id' => $id,
            'anh' => $anh,
            'dsDonVi' => Yii::$app->db->createCommand('SELECT id, ten FROM don_vi ORDER BY ten')->queryAll(),
        ];
    }
}
