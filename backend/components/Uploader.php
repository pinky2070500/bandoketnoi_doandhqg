<?php
namespace backend\components;

use Yii;
use yii\web\UploadedFile;

/**
 * Lưu ảnh upload vào frontend/web/uploads/<module>/<id>/ và trả path tương đối
 * (dạng 'uploads/<module>/<id>/<file>') để lưu vào bảng hinh_anh.
 */
class Uploader
{
    const CHO_PHEP = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    const MAX_BYTE = 8388608; // 8MB

    /**
     * @param string $module   khoá module (cong_trinh|an_toan|truyen_thong)
     * @param int    $id        id đối tượng
     * @param string $field     tên input file (vd 'anh_moi')
     * @return array danh sách ['path'=>..., 'ten_goc'=>...] các file đã lưu
     */
    public static function luuNhieu(string $module, int $id, string $field): array
    {
        $files = UploadedFile::getInstancesByName($field);
        if (!$files) {
            return [];
        }

        $webDir = Yii::$app->params['uploadsWebDir'] ?? 'uploads';
        $relDir = $webDir . '/' . $module . '/' . $id;
        $absDir = Yii::getAlias('@frontend/web/' . $relDir);
        if (!is_dir($absDir)) {
            @mkdir($absDir, 0775, true);
        }

        $ketQua = [];
        foreach ($files as $f) {
            if ($f->getHasError() || $f->size <= 0) {
                continue;
            }
            $ext = strtolower($f->extension);
            if (!in_array($ext, self::CHO_PHEP, true) || $f->size > self::MAX_BYTE) {
                continue;
            }
            $ten = uniqid('img_', true) . '.' . $ext;
            if ($f->saveAs($absDir . '/' . $ten)) {
                $ketQua[] = ['path' => $relDir . '/' . $ten, 'ten_goc' => $f->baseName];
            }
        }
        return $ketQua;
    }

    /** Xoá file vật lý theo path tương đối (an toàn: chỉ trong thư mục uploads). */
    public static function xoaFile(string $path): void
    {
        $webDir = Yii::$app->params['uploadsWebDir'] ?? 'uploads';
        if (strpos($path, $webDir . '/') !== 0) {
            return;
        }
        $abs = Yii::getAlias('@frontend/web/' . $path);
        if (is_file($abs)) {
            @unlink($abs);
        }
    }
}
