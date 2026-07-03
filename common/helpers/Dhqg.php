<?php
namespace common\helpers;

use Yii;

/**
 * Cấu hình dùng chung cho Hệ thống Bản đồ số Khu đô thị ĐHQG-HCM.
 * Là "nguồn sự thật" cho định nghĩa 3 module + enum, dùng bởi cả backend lẫn frontend.
 */
class Dhqg
{
    /** Tâm & zoom bản đồ Khu đô thị ĐHQG-HCM (lat, lng). */
    const MAP_CENTER = [10.868, 106.803];
    const MAP_ZOOM = 15;

    /** Màu thương hiệu ĐHQG-HCM (xanh dương) — dùng cho chrome (header/nav/nút/biểu đồ chính). */
    const BRAND = '#123c8a';
    const BRAND_2 = '#1b52c0';
    const BRAND_DARK = '#0c2a63';
    const BRAND_LIGHT = '#eaf1fb';

    /** 3 module chính (Giai đoạn 1). Thứ tự = thứ tự hiển thị. */
    const MODULES = [
        'cong_trinh' => [
            'label' => 'Công trình thanh niên & Điểm check-in',
            'label_ngan' => 'Công trình thanh niên',
            'mau' => '#16a37f',
            'icon' => 'fa-flag',
            'loai' => [
                'check_in' => 'Điểm check-in',
                'cong_trinh_tn' => 'Công trình thanh niên',
                'tieu_canh' => 'Tiểu cảnh',
                'vuon_hoa' => 'Vườn hoa',
                'khong_gian_sh' => 'Không gian sinh hoạt',
            ],
            'co_trang_thai' => true,
        ],
        'an_toan' => [
            'label' => 'An toàn khu đô thị',
            'label_ngan' => 'An toàn đô thị',
            'mau' => '#ef4444',
            'icon' => 'fa-triangle-exclamation',
            'loai' => [
                'ho_da' => 'Hồ đá',
                'diem_nguy_hiem' => 'Điểm nguy hiểm',
                'bien_canh_bao' => 'Biển cảnh báo',
                'tram_bao_ve' => 'Trạm bảo vệ',
                'diem_pccc' => 'Điểm PCCC',
                'diem_so_cuu' => 'Điểm sơ cứu',
                'camera' => 'Camera an ninh',
            ],
            'co_trang_thai' => false,
        ],
        'truyen_thong' => [
            'label' => 'Truyền thông trực quan',
            'label_ngan' => 'Truyền thông',
            'mau' => '#f59e0b',
            'icon' => 'fa-bullhorn',
            'loai' => [
                'pano' => 'Pano',
                'bien_tuyen_truyen' => 'Biển tuyên truyền',
                'bien_chi_dan' => 'Biển chỉ dẫn',
                'qr_truyen_thong' => 'QR truyền thông',
            ],
            'co_trang_thai' => false,
        ],
    ];

    /** Trạng thái (chủ yếu cho module cong_trinh). */
    const TRANG_THAI = [
        'de_xuat' => 'Đề xuất',
        'dang_trien_khai' => 'Đang triển khai',
        'hoan_thanh' => 'Hoàn thành',
        'bao_tri' => 'Bảo trì',
    ];

    /** Màu badge theo trạng thái. */
    const MAU_TRANG_THAI = [
        'de_xuat' => '#6b7280',
        'dang_trien_khai' => '#f59e0b',
        'hoan_thanh' => '#16a37f',
        'bao_tri' => '#3b82f6',
    ];

    /** Loại ảnh (nhiều ảnh / đối tượng). */
    const LOAI_ANH = [
        'hien_trang' => 'Hiện trạng',
        'trien_khai' => 'Triển khai',
        'hoan_thanh' => 'Hoàn thành',
        'khac' => 'Khác',
    ];

    public static function moduleTonTai($m): bool
    {
        return isset(self::MODULES[$m]);
    }

    public static function moduleLabel($m): string
    {
        return self::MODULES[$m]['label'] ?? $m;
    }

    public static function loaiLabel($m, $loai): string
    {
        return self::MODULES[$m]['loai'][$loai] ?? ($loai ?? '');
    }

    public static function trangThaiLabel($tt): string
    {
        return self::TRANG_THAI[$tt] ?? ($tt ?? '');
    }

    /** URL công khai của một ảnh (path lưu DB dạng 'uploads/...'). */
    public static function anhUrl($path): string
    {
        if (!$path) {
            return '';
        }
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }
        $base = rtrim((string) (Yii::$app->params['frontendUrl'] ?? ''), '/');
        return $base . '/' . ltrim($path, '/');
    }
}
