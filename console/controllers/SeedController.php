<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Seed dữ liệu nền cho hệ thống Bản đồ số ĐHQG-HCM.
 *
 *   php yii seed/init      → đơn vị + tài khoản (admin, doan, ttql)
 *   php yii seed/sample    → dữ liệu mẫu 3 module (chạy sau khi có ranh_khu)
 *   php yii seed/all       → cả hai
 */
class SeedController extends Controller
{
    /** Đơn vị + tài khoản đăng nhập theo vai trò. */
    public function actionInit()
    {
        $db = Yii::$app->db;

        // ── Đơn vị ──
        $donVi = [
            ['DOAN', 'Ban Cán sự Đoàn ĐHQG-HCM', ['cong_trinh', 'an_toan', 'truyen_thong']],
            ['TTQL', 'Trung tâm Quản lý KTX và Khu đô thị', ['an_toan', 'truyen_thong']],
        ];
        foreach ($donVi as [$ma, $ten, $modules]) {
            $exists = $db->createCommand('SELECT id FROM don_vi WHERE ma=:m', [':m' => $ma])->queryScalar();
            // Truyền thẳng mảng PHP: Yii tự cast sang jsonb cho cột json/jsonb (KHÔNG json_encode thủ công → tránh double-encode).
            if ($exists) {
                $db->createCommand()->update('don_vi', ['ten' => $ten, 'modules' => $modules], 'id=:i', [':i' => $exists])->execute();
            } else {
                $db->createCommand()->insert('don_vi', ['ma' => $ma, 'ten' => $ten, 'modules' => $modules])->execute();
            }
        }
        $doanId = (int) $db->createCommand("SELECT id FROM don_vi WHERE ma='DOAN'")->queryScalar();
        $ttqlId = (int) $db->createCommand("SELECT id FROM don_vi WHERE ma='TTQL'")->queryScalar();
        $this->stdout("Đã seed 2 đơn vị (DOAN=$doanId, TTQL=$ttqlId)\n");

        // ── Tài khoản ──
        $now = time();
        $accounts = [
            ['admin', 'admin@dhqg.local', 'admin123', 'admin', null],
            ['doan',  'doan@dhqg.local',  'doan123',  'don_vi', $doanId],
            ['ttql',  'ttql@dhqg.local',  'ttql123',  'don_vi', $ttqlId],
        ];
        foreach ($accounts as [$u, $e, $pw, $role, $dv]) {
            $hash = Yii::$app->security->generatePasswordHash($pw);
            $authKey = Yii::$app->security->generateRandomString();
            $id = $db->createCommand('SELECT id FROM "user" WHERE username=:u', [':u' => $u])->queryScalar();
            if ($id) {
                $db->createCommand()->update('user', [
                    'password_hash' => $hash, 'email' => $e, 'status' => 10,
                    'vai_tro' => $role, 'don_vi_id' => $dv, 'updated_at' => $now,
                ], 'id=:i', [':i' => $id])->execute();
            } else {
                $db->createCommand()->insert('user', [
                    'username' => $u, 'email' => $e, 'password_hash' => $hash,
                    'auth_key' => $authKey, 'status' => 10,
                    'vai_tro' => $role, 'don_vi_id' => $dv,
                    'created_at' => $now, 'updated_at' => $now,
                ])->execute();
            }
            $this->stdout("Tài khoản: $u / $pw  (vai_tro=$role)\n");
        }
        $this->stdout("Seed init xong.\n");
        return ExitCode::OK;
    }

    /** Dữ liệu mẫu 3 module (điểm nằm trong ranh ĐHQG) + vài ảnh minh hoạ. */
    public function actionSample()
    {
        $db = Yii::$app->db;
        $doanId = (int) $db->createCommand("SELECT id FROM don_vi WHERE ma='DOAN'")->queryScalar();
        $ttqlId = (int) $db->createCommand("SELECT id FROM don_vi WHERE ma='TTQL'")->queryScalar();
        if (!$doanId) {
            $this->stderr("Chưa có đơn vị. Chạy 'yii seed/init' trước.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        // Xoá dữ liệu cũ để re-run sạch (demo)
        $db->createCommand('TRUNCATE hinh_anh, doi_tuong RESTART IDENTITY CASCADE')->execute();

        // Anchor = tâm các phân khu (chắc chắn nằm trong ranh khu)
        $anchors = $db->createCommand("
            SELECT ST_Y(ST_Centroid(geom)) lat, ST_X(ST_Centroid(geom)) lng FROM phan_khu ORDER BY id
        ")->queryAll();
        if (!$anchors) {
            $this->stderr("Chưa có phan_khu. Nạp ranh giới GIS trước.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        // [ten, loai, trang_thai, nam, don_vi, co_anh]
        $data = [
            'cong_trinh' => [$doanId, [
                ['Cổng chào Khu đô thị ĐHQG', 'check_in', 'hoan_thanh', 2024, true],
                ['Đường sách - Không gian đọc ĐHQG', 'cong_trinh_tn', 'hoan_thanh', 2024, true],
                ['Vườn hoa Nhà Điều hành', 'vuon_hoa', 'dang_trien_khai', 2025, true],
                ['Tiểu cảnh Ký túc xá Khu A', 'tieu_canh', 'hoan_thanh', 2023, false],
                ['Không gian sinh hoạt Nhà Văn hóa Sinh viên', 'khong_gian_sh', 'hoan_thanh', 2024, true],
                ['Check-in Hồ Đá ĐHQG', 'check_in', 'hoan_thanh', 2023, true],
                ['Sân chơi thanh niên KTX Khu B', 'cong_trinh_tn', 'dang_trien_khai', 2025, false],
                ['Vườn hoa Trường ĐH Bách Khoa CS2', 'vuon_hoa', 'de_xuat', 2026, false],
                ['Công trình thanh niên "Con đường xanh"', 'cong_trinh_tn', 'hoan_thanh', 2024, true],
                ['Tiểu cảnh khu Trung tâm điều hành', 'tieu_canh', 'bao_tri', 2023, false],
            ]],
            'an_toan' => [$ttqlId, [
                ['Khu vực Hồ Đá - Nguy hiểm', 'ho_da', null, 2024, false],
                ['Điểm nguy hiểm khúc cua Tạ Quang Bửu', 'diem_nguy_hiem', null, 2024, false],
                ['Biển cảnh báo ven Hồ Đá', 'bien_canh_bao', null, 2024, false],
                ['Trạm bảo vệ Cổng chính', 'tram_bao_ve', null, 2023, false],
                ['Điểm PCCC Ký túc xá Khu A', 'diem_pccc', null, 2024, false],
                ['Điểm sơ cứu Trạm Y tế ĐHQG', 'diem_so_cuu', null, 2025, false],
                ['Camera an ninh ngã tư Trung tâm', 'camera', null, 2025, false],
                ['Biển cảnh báo giao thông Nhà Điều hành', 'bien_canh_bao', null, 2024, false],
            ]],
            'truyen_thong' => [$doanId, [
                ['Pano "ĐHQG Xanh - Thông minh - Bản sắc"', 'pano', null, 2025, true],
                ['Biển tuyên truyền bảo vệ môi trường', 'bien_tuyen_truyen', null, 2024, false],
                ['Biển chỉ dẫn Khu Ký túc xá', 'bien_chi_dan', null, 2023, false],
                ['QR truyền thông Nhà Văn hóa Sinh viên', 'qr_truyen_thong', null, 2025, false],
                ['Pano tuyên truyền an toàn giao thông', 'pano', null, 2024, true],
                ['Biển chỉ dẫn các trường thành viên', 'bien_chi_dan', null, 2024, false],
            ]],
        ];

        $prefix = ['cong_trinh' => 'CT', 'an_toan' => 'AT', 'truyen_thong' => 'TT'];
        $tongDiem = 0;
        $tongAnh = 0;
        foreach ($data as $module => [$dvId, $items]) {
            $i = 0;
            foreach ($items as $idx => [$ten, $loai, $tt, $nam, $coAnh]) {
                $a = $anchors[$idx % count($anchors)];
                // Offset nhỏ, xen kẽ để điểm không chồng nhau
                $lat = $a['lat'] + (($idx % 5) - 2) * 0.0006;
                $lng = $a['lng'] + ((($idx * 3) % 5) - 2) * 0.0006;
                $ma = $prefix[$module] . str_pad((string) (++$i), 3, '0', STR_PAD_LEFT);

                $db->createCommand()->insert('doi_tuong', [
                    'ma' => $ma,
                    'ten' => $ten,
                    'module' => $module,
                    'loai' => $loai,
                    'trang_thai' => $tt ?? 'hoan_thanh',
                    'nam' => $nam,
                    'don_vi_thuc_hien_id' => $dvId,
                    'don_vi_quan_ly_id' => $dvId,
                    'mo_ta' => $ten . ' — dữ liệu mẫu phục vụ demo Giai đoạn 1.',
                    'created_by' => 1,
                ])->execute();
                $id = (int) $db->getLastInsertID('doi_tuong_id_seq');
                $db->createCommand(
                    'UPDATE doi_tuong SET geom = ST_SetSRID(ST_MakePoint(:lng,:lat),4326) WHERE id=:i',
                    [':lng' => $lng, ':lat' => $lat, ':i' => $id]
                )->execute();
                $tongDiem++;

                if ($coAnh) {
                    $tongAnh += $this->taoAnhMau($module, $id, $ten);
                }
            }
        }
        $this->stdout("Đã seed $tongDiem điểm + $tongAnh ảnh mẫu.\n");
        return ExitCode::OK;
    }

    /** Sinh ảnh SVG placeholder cho 1 đối tượng, trả số ảnh đã tạo. */
    private function taoAnhMau(string $module, int $id, string $ten): int
    {
        $mau = \common\helpers\Dhqg::MODULES[$module]['mau'] ?? '#16a37f';
        $webDir = Yii::$app->params['uploadsWebDir'] ?? 'uploads';
        $relDir = "$webDir/$module/$id";
        $absDir = Yii::getAlias('@frontend/web/' . $relDir);
        if (!is_dir($absDir)) {
            @mkdir($absDir, 0775, true);
        }
        $loai = ['hien_trang' => 'Hiện trạng', 'hoan_thanh' => 'Hoàn thành'];
        $n = 0;
        foreach ($loai as $lk => $lv) {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500">'
                . '<rect width="800" height="500" fill="' . $mau . '"/>'
                . '<rect width="800" height="500" fill="url(#g)"/>'
                . '<defs><linearGradient id="g" x1="0" y1="0" x2="0" y2="1">'
                . '<stop offset="0" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity="0.35"/></linearGradient></defs>'
                . '<text x="40" y="440" fill="#fff" font-family="Arial" font-size="34" font-weight="bold">' . htmlspecialchars($ten) . '</text>'
                . '<text x="40" y="480" fill="#fff" font-family="Arial" font-size="22" opacity="0.9">' . $lv . ' · Ảnh minh hoạ</text>'
                . '</svg>';
            $file = "anh_{$lk}.svg";
            if (file_put_contents($absDir . '/' . $file, $svg) !== false) {
                Yii::$app->db->createCommand()->insert('hinh_anh', [
                    'doi_tuong_id' => $id,
                    'url' => "$relDir/$file",
                    'loai_anh' => $lk,
                    'thu_tu' => ++$n,
                ])->execute();
            }
        }
        return $n;
    }

    public function actionAll()
    {
        $this->actionInit();
        $this->actionSample();
        return ExitCode::OK;
    }

    public function getDb()
    {
        return Yii::$app->db;
    }
}
