<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $this->layout = false;
        Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');
        return parent::beforeAction($action);
    }

    /**
     * GET /api/congtrinh
     * Params: huyen, nam, priority, q
     */
    public function actionCongtrinh()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $req = Yii::$app->request;
        $huyen = trim($req->get('huyen', ''));
        $nam = trim($req->get('nam', ''));
        $priority = trim($req->get('priority', ''));
        $q = trim($req->get('q', ''));

        $where = ['1=1'];
        $params = [];

        if ($huyen) {
            $where[] = 'ten_huyen = :huyen';
            $params[':huyen'] = $huyen;
        }
        if ($nam) {
            $where[] = 'nam_dau_tu = :nam';
            $params[':nam'] = (int) $nam;
        }
        if ($priority) {
            $list = array_filter(array_map('trim', explode(',', $priority)));
            $holders = [];
            foreach ($list as $i => $v) {
                $key = ":p$i";
                $holders[] = $key;
                $params[$key] = $v;
            }
            if ($holders) {
                $where[] = 'muc_uu_tien IN (' . implode(',', $holders) . ')';
            }
        }
        // Thêm sau đoạn xử lý $priority
        $loai_ct = trim($req->get('loai_ct', ''));
        if ($loai_ct) {
            $list = array_filter(array_map('trim', explode(',', $loai_ct)));
            $holders = [];
            foreach ($list as $i => $v) {
                $key = ":lct$i";
                $holders[] = $key;
                $params[$key] = $v;
            }
            if ($holders) {
                $where[] = 'loai_ct IN (' . implode(',', $holders) . ')';
            }
        }
        if ($q) {
            $where[] = '(ten_ct ILIKE :q OR ten_xa ILIKE :q OR ten_huyen ILIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }

        $whereStr = implode(' AND ', $where);

        $rows = Yii::$app->db->createCommand("
            SELECT
                id, ma_ct, ten_ct, ten_xa, ten_huyen,
                nam_dau_tu, chieu_dai, chieu_rong, tai_trong,
                loai_ct, muc_uu_tien, trang_thai, tien_do,
                mo_ta, lien_he_ho_ten, lien_he_chuc_vu, lien_he_sdt,
                ST_X(geom::geometry) AS lng,
                ST_Y(geom::geometry) AS lat
            FROM congtrinh
            WHERE geom IS NOT NULL AND $whereStr
            ORDER BY nam_dau_tu, id
        ", $params)->queryAll();

        $features = array_map(function ($r) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $r['lng'], (float) $r['lat']],
                ],
                'properties' => [
                    'id' => (int) $r['id'],
                    'ma_ct' => $r['ma_ct'],
                    'ten_ct' => $r['ten_ct'],
                    'ten_xa' => $r['ten_xa'],
                    'ten_huyen' => $r['ten_huyen'],
                    'nam_dau_tu' => (int) $r['nam_dau_tu'],
                    'chieu_dai' => $r['chieu_dai'],
                    'chieu_rong' => $r['chieu_rong'],
                    'tai_trong' => $r['tai_trong'],
                    'loai_ct' => $r['loai_ct'],
                    'muc_uu_tien' => $r['muc_uu_tien'],
                    'trang_thai' => $r['trang_thai'],
                    'tien_do' => (int) $r['tien_do'],
                    'lien_he' => $r['lien_he_ho_ten'],
                    'chuc_vu' => $r['lien_he_chuc_vu'],
                    'sdt' => $r['lien_he_sdt'],
                ],
            ];
        }, $rows);

        return [
            'type' => 'FeatureCollection',
            'total' => count($features),
            'features' => $features,
        ];
    }

    /**
     * GET /api/thongke
     */
    public function actionThongke()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $db = Yii::$app->db;

        $tong = $db->createCommand("
            SELECT
                COUNT(*)                                                        AS tong,
                SUM(CASE WHEN trang_thai='hoan_thanh'    THEN 1 ELSE 0 END)    AS hoan_thanh,
                SUM(CASE WHEN trang_thai='dang_thi_cong' THEN 1 ELSE 0 END)    AS dang_thi_cong,
                SUM(CASE WHEN trang_thai='cho_dau_tu'    THEN 1 ELSE 0 END)    AS cho_dau_tu,
                SUM(CASE WHEN muc_uu_tien='khan_cap'     THEN 1 ELSE 0 END)    AS khan_cap
            FROM congtrinh
        ")->queryOne();

        $theo_huyen = $db->createCommand("
            SELECT ten_huyen, COUNT(*) AS so_luong
            FROM congtrinh
            GROUP BY ten_huyen
            ORDER BY so_luong DESC
        ")->queryAll();

        $theo_nam = $db->createCommand("
            SELECT nam_dau_tu, COUNT(*) AS so_luong
            FROM congtrinh
            GROUP BY nam_dau_tu
            ORDER BY nam_dau_tu
        ")->queryAll();

        return [
            'tong' => $tong,
            'theo_huyen' => $theo_huyen,
            'theo_nam' => $theo_nam,
        ];
    }

    /**
     * GET /api/ranh-tinh
     */
    public function actionRanhTinh()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $row = Yii::$app->db->createCommand("
            SELECT ST_AsGeoJSON(
                ST_SimplifyPreserveTopology(geom, 0.001)
            ) AS geojson
            FROM ranhtinh
            WHERE ma_tinh = '82'
            LIMIT 1
        ")->queryOne();

        if (!$row) {
            return ['type' => 'FeatureCollection', 'features' => []];
        }

        return [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'geometry' => json_decode($row['geojson'], true),
                    'properties' => ['ten_tinh' => 'Đồng Tháp'],
                ]
            ],
        ];
    }

    /**
     * GET /api/phuong-xa
     */
    public function actionPhuongXa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $rows = Yii::$app->db->createCommand("
            SELECT
                ma_xa, ten_xa,
                ST_AsGeoJSON(
                    ST_SimplifyPreserveTopology(geom, 0.0005)
                ) AS geojson
            FROM phuongxa
            WHERE ma_tinh = '82'
            ORDER BY ten_xa
        ")->queryAll();

        $features = array_map(fn($r) => [
            'type' => 'Feature',
            'geometry' => json_decode($r['geojson'], true),
            'properties' => [
                'ma_xa' => $r['ma_xa'],
                'ten_xa' => $r['ten_xa'],
            ],
        ], $rows);

        return [
            'type' => 'FeatureCollection',
            'total' => count($features),
            'features' => $features,
        ];
    }

    /**
     * GET /api/danh-sach-huyen
     */
    public function actionDanhSachHuyen()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $rows = Yii::$app->db->createCommand("
            SELECT DISTINCT ten_huyen
            FROM congtrinh
            ORDER BY ten_huyen
        ")->queryAll();

        return array_column($rows, 'ten_huyen');
    }
}