<?php

use yii\db\Migration;

/**
 * Schema Hệ thống Bản đồ số Khu đô thị ĐHQG-HCM (Giai đoạn 1).
 *
 * Pivot từ hệ thống Đồng Tháp (congtrinh/phuongxa/ranhtinh) sang mô hình đa module:
 *  - don_vi     : đơn vị cập nhật + module được cấp quyền (RBAC nhẹ)
 *  - doi_tuong  : điểm dùng chung cho 3 module (cong_trinh | an_toan | truyen_thong)
 *  - hinh_anh   : nhiều ảnh / đối tượng
 *  - ranh_khu   : ranh giới Khu đô thị ĐHQG-HCM (MultiPolygon)
 *  - phan_khu   : phân khu / trường thành viên (tuỳ chọn)
 *  - user       : thêm vai_tro + don_vi_id
 *
 * Dùng raw SQL cho các kiểu PostGIS (geometry) theo đúng pattern dự án.
 */
class m260702_000001_dhqg_schema extends Migration
{
    public function safeUp()
    {
        $db = $this->db;

        // ── 0. Bỏ các bảng của hệ thống Đồng Tháp (đã có backup) ──
        $db->createCommand('DROP TABLE IF EXISTS congtrinh CASCADE')->execute();
        $db->createCommand('DROP TABLE IF EXISTS phuongxa CASCADE')->execute();
        $db->createCommand('DROP TABLE IF EXISTS ranhtinh CASCADE')->execute();

        // ── 1. don_vi ──
        $db->createCommand("
            CREATE TABLE don_vi (
                id          serial PRIMARY KEY,
                ma          varchar(30) UNIQUE,
                ten         varchar(200) NOT NULL,
                modules     jsonb NOT NULL DEFAULT '[]'::jsonb,
                created_at  timestamp DEFAULT now()
            )
        ")->execute();

        // ── 2. doi_tuong ──
        $db->createCommand("
            CREATE TABLE doi_tuong (
                id                  serial PRIMARY KEY,
                ma                  varchar(30) UNIQUE,
                ten                 varchar(255) NOT NULL,
                module              varchar(20)  NOT NULL,
                loai                varchar(40),
                geom                geometry(Point,4326),
                trang_thai          varchar(30) DEFAULT 'de_xuat',
                nam                 integer,
                don_vi_thuc_hien_id integer REFERENCES don_vi(id) ON DELETE SET NULL,
                don_vi_quan_ly_id   integer REFERENCES don_vi(id) ON DELETE SET NULL,
                mo_ta               text,
                noi_dung            text,
                thuoc_tinh          jsonb DEFAULT '{}'::jsonb,
                created_by          integer,
                created_at          timestamp DEFAULT now(),
                updated_at          timestamp DEFAULT now()
            )
        ")->execute();
        $db->createCommand('CREATE INDEX idx_doi_tuong_geom   ON doi_tuong USING gist (geom)')->execute();
        $db->createCommand('CREATE INDEX idx_doi_tuong_module ON doi_tuong (module)')->execute();
        $db->createCommand('CREATE INDEX idx_doi_tuong_loai   ON doi_tuong (loai)')->execute();
        $db->createCommand('CREATE INDEX idx_doi_tuong_tt     ON doi_tuong (trang_thai)')->execute();
        $db->createCommand('CREATE INDEX idx_doi_tuong_nam    ON doi_tuong (nam)')->execute();

        // ── 3. hinh_anh ──
        $db->createCommand("
            CREATE TABLE hinh_anh (
                id           serial PRIMARY KEY,
                doi_tuong_id integer NOT NULL REFERENCES doi_tuong(id) ON DELETE CASCADE,
                url          varchar(500) NOT NULL,
                loai_anh     varchar(20) DEFAULT 'khac',
                thu_tu       integer DEFAULT 0,
                created_at   timestamp DEFAULT now()
            )
        ")->execute();
        $db->createCommand('CREATE INDEX idx_hinh_anh_dt ON hinh_anh (doi_tuong_id)')->execute();

        // ── 4. ranh_khu ──
        $db->createCommand("
            CREATE TABLE ranh_khu (
                id    serial PRIMARY KEY,
                ten   varchar(200),
                geom  geometry(MultiPolygon,4326)
            )
        ")->execute();
        $db->createCommand('CREATE INDEX idx_ranh_khu_geom ON ranh_khu USING gist (geom)')->execute();

        // ── 5. phan_khu ──
        $db->createCommand("
            CREATE TABLE phan_khu (
                id    serial PRIMARY KEY,
                ma    varchar(40),
                ten   varchar(200),
                loai  varchar(60),
                geom  geometry(MultiPolygon,4326)
            )
        ")->execute();
        $db->createCommand('CREATE INDEX idx_phan_khu_geom ON phan_khu USING gist (geom)')->execute();

        // ── 6. user: thêm vai trò + đơn vị ──
        $db->createCommand('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS vai_tro varchar(20) DEFAULT \'admin\'')->execute();
        $db->createCommand('ALTER TABLE "user" ADD COLUMN IF NOT EXISTS don_vi_id integer REFERENCES don_vi(id) ON DELETE SET NULL')->execute();
    }

    public function safeDown()
    {
        // Pivot: down chỉ gỡ schema mới. Khôi phục Đồng Tháp dùng backup db.sql.
        $this->db->createCommand('ALTER TABLE "user" DROP COLUMN IF EXISTS don_vi_id')->execute();
        $this->db->createCommand('ALTER TABLE "user" DROP COLUMN IF EXISTS vai_tro')->execute();
        $this->db->createCommand('DROP TABLE IF EXISTS hinh_anh CASCADE')->execute();
        $this->db->createCommand('DROP TABLE IF EXISTS doi_tuong CASCADE')->execute();
        $this->db->createCommand('DROP TABLE IF EXISTS phan_khu CASCADE')->execute();
        $this->db->createCommand('DROP TABLE IF EXISTS ranh_khu CASCADE')->execute();
        $this->db->createCommand('DROP TABLE IF EXISTS don_vi CASCADE')->execute();
    }
}
