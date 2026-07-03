<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\helpers\Dhqg;

/**
 * API công khai (đọc-only) cho Hệ thống Bản đồ số Khu đô thị ĐHQG-HCM.
 * Trả GeoJSON toạ độ [lng,lat]; tắt CSRF; CORS *.
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $this->layout = false;
        Yii::$app->response->headers->add('Access-Control-Allow-Origin', '*');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * GET /api/diem — điểm của 1 hoặc tất cả module.
     * Params: module, loai, nam, trang_thai, don_vi, q
     */
    public function actionDiem()
    {
        $req = Yii::$app->request;
        $where = ['d.geom IS NOT NULL'];
        $params = [];

        $module = trim((string) $req->get('module', ''));
        if ($module !== '' && Dhqg::moduleTonTai($module)) {
            $where[] = 'd.module = :module';
            $params[':module'] = $module;
        }
        foreach (['loai' => 'd.loai', 'trang_thai' => 'd.trang_thai'] as $g => $col) {
            $val = trim((string) $req->get($g, ''));
            if ($val !== '') {
                // Cho phép danh sách phân tách bằng dấu phẩy
                $list = array_values(array_filter(array_map('trim', explode(',', $val))));
                if ($list) {
                    $ph = [];
                    foreach ($list as $i => $vv) {
                        $k = ":{$g}{$i}";
                        $ph[] = $k;
                        $params[$k] = $vv;
                    }
                    $where[] = "$col IN (" . implode(',', $ph) . ')';
                }
            }
        }
        if (($nam = trim((string) $req->get('nam', ''))) !== '') {
            $where[] = 'd.nam = :nam';
            $params[':nam'] = (int) $nam;
        }
        if (($dv = trim((string) $req->get('don_vi', ''))) !== '') {
            $where[] = '(d.don_vi_thuc_hien_id = :dv OR d.don_vi_quan_ly_id = :dv)';
            $params[':dv'] = (int) $dv;
        }
        if (($q = trim((string) $req->get('q', ''))) !== '') {
            $where[] = '(d.ten ILIKE :q OR d.ma ILIKE :q OR d.mo_ta ILIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }
        $whereStr = implode(' AND ', $where);

        $rows = Yii::$app->db->createCommand("
            SELECT d.id, d.ma, d.ten, d.module, d.loai, d.trang_thai, d.nam,
                   dv.ten AS don_vi_thuc_hien,
                   (SELECT count(*) FROM hinh_anh h WHERE h.doi_tuong_id = d.id) AS so_anh,
                   (SELECT h.url FROM hinh_anh h WHERE h.doi_tuong_id = d.id ORDER BY h.thu_tu, h.id LIMIT 1) AS anh,
                   ST_X(d.geom::geometry) AS lng, ST_Y(d.geom::geometry) AS lat
            FROM doi_tuong d
            LEFT JOIN don_vi dv ON dv.id = d.don_vi_thuc_hien_id
            WHERE $whereStr
            ORDER BY d.module, d.id
        ", $params)->queryAll();

        $features = array_map(fn($r) => [
            'type' => 'Feature',
            'geometry' => ['type' => 'Point', 'coordinates' => [(float) $r['lng'], (float) $r['lat']]],
            'properties' => [
                'id' => (int) $r['id'],
                'ma' => $r['ma'],
                'ten' => $r['ten'],
                'module' => $r['module'],
                'loai' => $r['loai'],
                'loai_label' => Dhqg::loaiLabel($r['module'], $r['loai']),
                'trang_thai' => $r['trang_thai'],
                'trang_thai_label' => Dhqg::trangThaiLabel($r['trang_thai']),
                'nam' => $r['nam'] ? (int) $r['nam'] : null,
                'don_vi' => $r['don_vi_thuc_hien'],
                'so_anh' => (int) $r['so_anh'],
                'anh' => Dhqg::anhUrl($r['anh']),
            ],
        ], $rows);

        return ['type' => 'FeatureCollection', 'total' => count($features), 'features' => $features];
    }

    /** GET /api/diem-chi-tiet?id= — chi tiết 1 đối tượng + tất cả ảnh (popup / trang QR). */
    public function actionChiTiet($id)
    {
        $r = Yii::$app->db->createCommand("
            SELECT d.*, dv1.ten AS don_vi_thuc_hien, dv2.ten AS don_vi_quan_ly,
                   ST_X(d.geom::geometry) AS lng, ST_Y(d.geom::geometry) AS lat
            FROM doi_tuong d
            LEFT JOIN don_vi dv1 ON dv1.id = d.don_vi_thuc_hien_id
            LEFT JOIN don_vi dv2 ON dv2.id = d.don_vi_quan_ly_id
            WHERE d.id = :id
        ", [':id' => (int) $id])->queryOne();
        if (!$r) {
            return ['ok' => false];
        }
        $anh = Yii::$app->db->createCommand(
            'SELECT url, loai_anh FROM hinh_anh WHERE doi_tuong_id=:i ORDER BY thu_tu, id',
            [':i' => (int) $id]
        )->queryAll();

        return [
            'ok' => true,
            'id' => (int) $r['id'],
            'ma' => $r['ma'],
            'ten' => $r['ten'],
            'module' => $r['module'],
            'module_label' => Dhqg::moduleLabel($r['module']),
            'loai' => $r['loai'],
            'loai_label' => Dhqg::loaiLabel($r['module'], $r['loai']),
            'trang_thai' => $r['trang_thai'],
            'trang_thai_label' => Dhqg::trangThaiLabel($r['trang_thai']),
            'nam' => $r['nam'] ? (int) $r['nam'] : null,
            'don_vi_thuc_hien' => $r['don_vi_thuc_hien'],
            'don_vi_quan_ly' => $r['don_vi_quan_ly'],
            'mo_ta' => $r['mo_ta'],
            'noi_dung' => $r['noi_dung'],
            'lat' => $r['lat'] !== null ? (float) $r['lat'] : null,
            'lng' => $r['lng'] !== null ? (float) $r['lng'] : null,
            'anh' => array_map(fn($a) => ['url' => Dhqg::anhUrl($a['url']), 'loai' => $a['loai_anh']], $anh),
        ];
    }

    /** GET /api/thongke?module= — thống kê 1 module (hoặc tất cả nếu bỏ trống). */
    public function actionThongke()
    {
        $db = Yii::$app->db;
        $module = trim((string) Yii::$app->request->get('module', ''));
        $cond = '1=1';
        $p = [];
        if ($module !== '' && Dhqg::moduleTonTai($module)) {
            $cond = 'module = :m';
            $p[':m'] = $module;
        }

        $tong = $db->createCommand("
            SELECT COUNT(*) tong,
                   SUM(CASE WHEN trang_thai='hoan_thanh' THEN 1 ELSE 0 END) hoan_thanh,
                   SUM(CASE WHEN trang_thai='dang_trien_khai' THEN 1 ELSE 0 END) dang_trien_khai,
                   SUM(CASE WHEN trang_thai='de_xuat' THEN 1 ELSE 0 END) de_xuat,
                   SUM(CASE WHEN nam = EXTRACT(YEAR FROM now()) THEN 1 ELSE 0 END) trong_nam
            FROM doi_tuong WHERE $cond
        ", $p)->queryOne();

        $theo_loai = $db->createCommand("SELECT loai, COUNT(*) so_luong FROM doi_tuong WHERE $cond GROUP BY loai ORDER BY so_luong DESC", $p)->queryAll();
        $theo_don_vi = $db->createCommand("
            SELECT dv.ten, COUNT(*) so_luong FROM doi_tuong d
            LEFT JOIN don_vi dv ON dv.id = d.don_vi_thuc_hien_id
            WHERE $cond GROUP BY dv.ten ORDER BY so_luong DESC
        ", $p)->queryAll();

        return ['tong' => $tong, 'theo_loai' => $theo_loai, 'theo_don_vi' => $theo_don_vi];
    }

    /** GET /api/tong-hop — dữ liệu dashboard lãnh đạo. Params: don_vi, tu_nam, den_nam */
    public function actionTongHop()
    {
        $db = Yii::$app->db;
        $req = Yii::$app->request;
        $where = ['1=1'];
        $p = [];
        if (($dv = trim((string) $req->get('don_vi', ''))) !== '') {
            $where[] = '(don_vi_thuc_hien_id = :dv OR don_vi_quan_ly_id = :dv)';
            $p[':dv'] = (int) $dv;
        }
        if (($tu = trim((string) $req->get('tu_nam', ''))) !== '') {
            $where[] = 'nam >= :tu';
            $p[':tu'] = (int) $tu;
        }
        if (($den = trim((string) $req->get('den_nam', ''))) !== '') {
            $where[] = 'nam <= :den';
            $p[':den'] = (int) $den;
        }
        $w = implode(' AND ', $where);

        $theo_module = $db->createCommand("SELECT module, COUNT(*) so FROM doi_tuong WHERE $w GROUP BY module", $p)->queryAll();
        $theo_trang_thai = $db->createCommand("SELECT trang_thai, COUNT(*) so FROM doi_tuong WHERE $w AND module='cong_trinh' GROUP BY trang_thai", $p)->queryAll();
        $theo_nam = $db->createCommand("SELECT nam, COUNT(*) so FROM doi_tuong WHERE $w AND nam IS NOT NULL GROUP BY nam ORDER BY nam", $p)->queryAll();
        $theo_loai_ct = $db->createCommand("SELECT loai, COUNT(*) so FROM doi_tuong WHERE $w AND module='cong_trinh' GROUP BY loai", $p)->queryAll();

        // Đếm nhanh theo hạng mục cho card lãnh đạo
        $count = fn($sql) => (int) $db->createCommand("SELECT COUNT(*) FROM doi_tuong WHERE $w AND $sql", $p)->queryScalar();
        return [
            'the' => [
                'cong_trinh' => $count("module='cong_trinh'"),
                'check_in' => $count("module='cong_trinh' AND loai='check_in'"),
                'an_toan' => $count("module='an_toan'"),
                'pano' => $count("module='truyen_thong' AND loai='pano'"),
                'truyen_thong' => $count("module='truyen_thong'"),
                'hoan_thanh' => $count("trang_thai='hoan_thanh'"),
            ],
            'theo_module' => $theo_module,
            'theo_trang_thai' => $theo_trang_thai,
            'theo_nam' => $theo_nam,
            'theo_loai_ct' => $theo_loai_ct,
        ];
    }

    /** GET /api/ranh-khu — ranh giới Khu đô thị ĐHQG-HCM. */
    public function actionRanhKhu()
    {
        $rows = Yii::$app->db->createCommand("
            SELECT ten, ST_AsGeoJSON(ST_SimplifyPreserveTopology(geom, 0.0002)) gj FROM ranh_khu
        ")->queryAll();
        return $this->fc($rows, fn($r) => ['ten' => $r['ten']]);
    }

    /** GET /api/phan-khu — các phân khu / trường thành viên. */
    public function actionPhanKhu()
    {
        $rows = Yii::$app->db->createCommand("
            SELECT ma, ten, loai, ST_AsGeoJSON(ST_SimplifyPreserveTopology(geom, 0.00015)) gj FROM phan_khu ORDER BY ten
        ")->queryAll();
        return $this->fc($rows, fn($r) => ['ma' => $r['ma'], 'ten' => $r['ten'], 'loai' => $r['loai']]);
    }

    /** GET /api/don-vi — danh sách đơn vị. */
    public function actionDonVi()
    {
        $rows = Yii::$app->db->createCommand('SELECT id, ma, ten, modules FROM don_vi ORDER BY ten')->queryAll();
        return array_map(fn($r) => [
            'id' => (int) $r['id'],
            'ma' => $r['ma'],
            'ten' => $r['ten'],
            'modules' => $r['modules'] ? json_decode($r['modules'], true) : [],
        ], $rows);
    }

    /** GET /api/cau-hinh — cấu hình module/enum cho FE (data-driven). */
    public function actionCauHinh()
    {
        return [
            'center' => Dhqg::MAP_CENTER,
            'zoom' => Dhqg::MAP_ZOOM,
            'brand' => ['main' => Dhqg::BRAND, 'alt' => Dhqg::BRAND_2, 'dark' => Dhqg::BRAND_DARK, 'light' => Dhqg::BRAND_LIGHT],
            'modules' => Dhqg::MODULES,
            'trang_thai' => Dhqg::TRANG_THAI,
            'mau_trang_thai' => Dhqg::MAU_TRANG_THAI,
        ];
    }

    /** Helper build FeatureCollection từ cột 'gj' (GeoJSON geometry). */
    private function fc(array $rows, callable $props): array
    {
        $features = [];
        foreach ($rows as $r) {
            if (empty($r['gj'])) {
                continue;
            }
            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($r['gj'], true),
                'properties' => $props($r),
            ];
        }
        return ['type' => 'FeatureCollection', 'total' => count($features), 'features' => $features];
    }
}
