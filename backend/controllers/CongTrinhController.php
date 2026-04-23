<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\Pagination;

class CongTrinhController extends Controller
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
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    // ── DANH SÁCH ──────────────────────────────────────
    public function actionIndex()
    {
        $db = Yii::$app->db;
        $where = ['1=1'];
        $params = [];

        $q = Yii::$app->request->get('q', '');
        $huyen = Yii::$app->request->get('huyen', '');
        $nam = Yii::$app->request->get('nam', '');
        $priority = Yii::$app->request->get('priority', '');
        $status = Yii::$app->request->get('status', '');
        $coords = Yii::$app->request->get('coords', ''); // 'yes'|'no'|''

        if ($q) {
            $where[] = "(ten_ct ILIKE :q OR ten_xa ILIKE :q OR ten_huyen ILIKE :q OR ma_ct ILIKE :q)";
            $params[':q'] = '%' . $q . '%';
        }
        if ($huyen) {
            $where[] = 'ten_huyen = :huyen';
            $params[':huyen'] = $huyen;
        }
        if ($nam) {
            $where[] = 'nam_dau_tu = :nam';
            $params[':nam'] = (int) $nam;
        }
        if ($priority) {
            $where[] = 'muc_uu_tien = :p';
            $params[':p'] = $priority;
        }
        if ($status) {
            $where[] = 'trang_thai = :s';
            $params[':s'] = $status;
        }
        if ($coords === 'yes')
            $where[] = 'geom IS NOT NULL';
        if ($coords === 'no')
            $where[] = 'geom IS NULL';

        $whereStr = implode(' AND ', $where);

        $total = (int) $db->createCommand("SELECT COUNT(*) FROM congtrinh WHERE $whereStr", $params)->queryScalar();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => 20]);

        $rows = $db->createCommand("
            SELECT id, ma_ct, ten_ct, ten_xa, ten_huyen, nam_dau_tu,
                   loai_ct, muc_uu_tien, trang_thai, tien_do,
                   geom IS NOT NULL AS has_geom,
                   lien_he_ho_ten
            FROM congtrinh
            WHERE $whereStr
            ORDER BY nam_dau_tu, id
            LIMIT :limit OFFSET :offset
        ", array_merge($params, [
                ':limit' => $pages->pageSize,
                ':offset' => $pages->offset,
            ]))->queryAll();

        $huyens = $db->createCommand("SELECT DISTINCT ten_huyen FROM congtrinh ORDER BY ten_huyen")->queryColumn();
        $thongke = $db->createCommand("
            SELECT
                COUNT(*) as tong,
                SUM(CASE WHEN geom IS NULL THEN 1 ELSE 0 END) as chua_toa_do,
                SUM(CASE WHEN trang_thai='hoan_thanh' THEN 1 ELSE 0 END) as hoan_thanh,
                SUM(CASE WHEN muc_uu_tien='khan_cap' THEN 1 ELSE 0 END) as khan_cap
            FROM congtrinh
        ")->queryOne();

        return $this->render('index', compact('rows', 'pages', 'total', 'huyens', 'thongke', 'q', 'huyen', 'nam', 'priority', 'status', 'coords'));
    }

    // ── XEM CHI TIẾT ──────────────────────────────────
    public function actionView($id)
    {
        $db = Yii::$app->db;
        $row = $db->createCommand("
            SELECT *,
                ST_X(geom::geometry) AS lng,
                ST_Y(geom::geometry) AS lat
            FROM congtrinh WHERE id = :id
        ", [':id' => (int) $id])->queryOne();

        if (!$row)
            throw new NotFoundHttpException("Không tìm thấy công trình #$id");

        return $this->render('view', ['data' => $row]);
    }

    // ── TẠO MỚI ──────────────────────────────────────
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $errors = $this->validateData($data);

            if (empty($errors)) {
                try {
                    $db = Yii::$app->db;
                    $geomExpr = null;

                    // Lấy tọa độ từ click map hoặc nhập tay
                    $lat = trim($data['lat'] ?? '');
                    $lng = trim($data['lng'] ?? '');

                    $db->createCommand()->insert('congtrinh', [
                        'ma_ct' => strtoupper(trim($data['ma_ct'] ?? '')),
                        'ten_ct' => trim($data['ten_ct']),
                        'ten_xa' => trim($data['ten_xa'] ?? ''),
                        'ten_huyen' => trim($data['ten_huyen'] ?? ''),
                        'nam_dau_tu' => (int) $data['nam_dau_tu'],
                        'chieu_dai' => $data['chieu_dai'] ?: null,
                        'chieu_rong' => $data['chieu_rong'] ?: null,
                        'tai_trong' => $data['tai_trong'] ?: null,
                        'loai_ct' => $data['loai_ct'] ?? 'cau',
                        'muc_uu_tien' => $data['muc_uu_tien'] ?? 'cao',
                        'trang_thai' => $data['trang_thai'] ?? 'cho_dau_tu',
                        'tien_do' => (int) ($data['tien_do'] ?? 0),
                        'mo_ta' => trim($data['mo_ta'] ?? ''),
                        'lien_he_ho_ten' => trim($data['lien_he_ho_ten'] ?? ''),
                        'lien_he_chuc_vu' => trim($data['lien_he_chuc_vu'] ?? ''),
                        'lien_he_sdt' => trim($data['lien_he_sdt'] ?? ''),
                        'lien_he_email' => trim($data['lien_he_email'] ?? ''),
                    ])->execute();

                    $id = $db->getLastInsertID('congtrinh_id_seq');

                    // Gán tọa độ nếu có
                    if ($lat && $lng && is_numeric($lat) && is_numeric($lng)) {
                        $db->createCommand("
                            UPDATE congtrinh
                            SET geom = ST_SetSRID(ST_MakePoint(:lng, :lat), 4326)
                            WHERE id = :id
                        ", [':lng' => (float) $lng, ':lat' => (float) $lat, ':id' => $id])->execute();

                        // Spatial join gán ma_xa_ref
                        $db->createCommand("
                            UPDATE congtrinh c
                            SET ma_xa_ref = p.ma_xa
                            FROM phuongxa p
                            WHERE c.id = :id
                              AND ST_Within(c.geom::geometry, p.geom::geometry)
                              AND p.ma_tinh = '82'
                        ", [':id' => $id])->execute();
                    }

                    Yii::$app->session->setFlash('success', "Đã thêm công trình #{$id} thành công!");
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Lỗi: ' . $e->getMessage());
                }
            }

            return $this->render('form', ['data' => $data, 'errors' => $errors, 'isNew' => true]);
        }

        // Tự động sinh mã CT
        $lastId = (int) Yii::$app->db->createCommand("SELECT MAX(id) FROM congtrinh")->queryScalar();
        $maCt = 'CT' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        return $this->render('form', [
            'data' => [
                'ma_ct' => $maCt,
                'nam_dau_tu' => date('Y'),
                'loai_ct' => 'cau',
                'muc_uu_tien' => 'cao',
                'trang_thai' => 'cho_dau_tu',
                'tien_do' => 0,
                'lat' => null,
                'lng' => null,
                'ten_xa' => '',
                'ten_huyen' => '',
                'ma_xa_ref' => '',
                'chieu_dai' => '',
                'chieu_rong' => '',
                'tai_trong' => '',
                'kinh_phi_dc' => '',
                'mo_ta' => '',
                'lien_he_ho_ten' => '',
                'lien_he_chuc_vu' => '',
                'lien_he_sdt' => '',
                'lien_he_email' => '',
            ],
            'errors' => [],
            'isNew' => true,
        ]);
    }

    // ── CẬP NHẬT ──────────────────────────────────────
    public function actionUpdate($id)
    {
        $db = Yii::$app->db;
        $row = $db->createCommand("
            SELECT *, ST_X(geom::geometry) AS lng, ST_Y(geom::geometry) AS lat
            FROM congtrinh WHERE id = :id
        ", [':id' => (int) $id])->queryOne();

        if (!$row)
            throw new NotFoundHttpException("Không tìm thấy công trình #$id");

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $errors = $this->validateData($data);

            if (empty($errors)) {
                try {
                    $lat = trim($data['lat'] ?? '');
                    $lng = trim($data['lng'] ?? '');

                    $db->createCommand()->update('congtrinh', [
                        'ma_ct' => strtoupper(trim($data['ma_ct'] ?? '')),
                        'ten_ct' => trim($data['ten_ct']),
                        'ten_xa' => trim($data['ten_xa'] ?? ''),
                        'ten_huyen' => trim($data['ten_huyen'] ?? ''),
                        'nam_dau_tu' => (int) $data['nam_dau_tu'],
                        'chieu_dai' => $data['chieu_dai'] ?: null,
                        'chieu_rong' => $data['chieu_rong'] ?: null,
                        'tai_trong' => $data['tai_trong'] ?: null,
                        'loai_ct' => $data['loai_ct'] ?? 'cau',
                        'muc_uu_tien' => $data['muc_uu_tien'] ?? 'cao',
                        'trang_thai' => $data['trang_thai'] ?? 'cho_dau_tu',
                        'tien_do' => (int) ($data['tien_do'] ?? 0),
                        'mo_ta' => trim($data['mo_ta'] ?? ''),
                        'lien_he_ho_ten' => trim($data['lien_he_ho_ten'] ?? ''),
                        'lien_he_chuc_vu' => trim($data['lien_he_chuc_vu'] ?? ''),
                        'lien_he_sdt' => trim($data['lien_he_sdt'] ?? ''),
                        'lien_he_email' => trim($data['lien_he_email'] ?? ''),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], 'id = :id', [':id' => (int) $id])->execute();

                    // Cập nhật tọa độ
                    if ($lat && $lng && is_numeric($lat) && is_numeric($lng)) {
                        $db->createCommand("
                            UPDATE congtrinh
                            SET geom = ST_SetSRID(ST_MakePoint(:lng, :lat), 4326)
                            WHERE id = :id
                        ", [':lng' => (float) $lng, ':lat' => (float) $lat, ':id' => (int) $id])->execute();

                        $db->createCommand("
                            UPDATE congtrinh c
                            SET ma_xa_ref = p.ma_xa
                            FROM phuongxa p
                            WHERE c.id = :id
                              AND ST_Within(c.geom::geometry, p.geom::geometry)
                              AND p.ma_tinh = '82'
                        ", [':id' => (int) $id])->execute();
                    } elseif (isset($data['clear_geom']) && $data['clear_geom']) {
                        $db->createCommand("UPDATE congtrinh SET geom=NULL, ma_xa_ref=NULL WHERE id=:id", [':id' => (int) $id])->execute();
                    }

                    Yii::$app->session->setFlash('success', 'Đã cập nhật thành công!');
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Lỗi: ' . $e->getMessage());
                }
            }

            return $this->render('form', ['data' => array_merge($row, $data), 'errors' => $errors, 'isNew' => false, 'id' => $id]);
        }

        return $this->render('form', ['data' => $row, 'errors' => [], 'isNew' => false, 'id' => $id]);
    }

    // ── XÓA ──────────────────────────────────────────
    public function actionDelete($id)
    {
        Yii::$app->db->createCommand()->delete('congtrinh', 'id = :id', [':id' => (int) $id])->execute();
        Yii::$app->session->setFlash('success', "Đã xóa công trình #$id");
        return $this->redirect(['index']);
    }

    // ── VALIDATE ──────────────────────────────────────
    private function validateData($data)
    {
        $errors = [];
        if (empty(trim($data['ten_ct'] ?? '')))
            $errors['ten_ct'] = 'Tên công trình không được để trống';
        if (empty($data['nam_dau_tu']) || !is_numeric($data['nam_dau_tu']))
            $errors['nam_dau_tu'] = 'Năm đầu tư không hợp lệ';
        if (!empty($data['lat']) && !is_numeric($data['lat']))
            $errors['lat'] = 'Vĩ độ không hợp lệ';
        if (!empty($data['lng']) && !is_numeric($data['lng']))
            $errors['lng'] = 'Kinh độ không hợp lệ';
        return $errors;
    }
}