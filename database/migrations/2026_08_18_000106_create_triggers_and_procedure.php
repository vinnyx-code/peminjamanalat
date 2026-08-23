<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create triggers and stored procedure for stok adjust and return processing
        $sql = <<<'SQL'
-- Trigger after update peminjaman to reduce stok when disetujui
DROP TRIGGER IF EXISTS trg_peminjaman_after_update;
CREATE TRIGGER trg_peminjaman_after_update
AFTER UPDATE ON peminjaman
FOR EACH ROW
BEGIN
  DECLARE current_stok INT;
  IF NEW.status = 'disetujui' AND OLD.status <> 'disetujui' THEN
    SELECT stok INTO current_stok FROM alat WHERE id = NEW.alat_id FOR UPDATE;
    IF current_stok <= 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak cukup untuk menyetujui peminjaman';
    ELSE
      UPDATE alat
      SET stok = stok - 1,
          status = CASE WHEN stok - 1 = 0 THEN 'tidak_tersedia' ELSE status END,
          updated_at = CURRENT_TIMESTAMP
      WHERE id = NEW.alat_id;
    END IF;
  END IF;
END;

-- Trigger after insert pengembalian to increase stok and set peminjaman selesai
DROP TRIGGER IF EXISTS trg_pengembalian_after_insert;
CREATE TRIGGER trg_pengembalian_after_insert
AFTER INSERT ON pengembalian
FOR EACH ROW
BEGIN
  DECLARE v_alat_id INT;
  SELECT alat_id INTO v_alat_id FROM peminjaman WHERE id = NEW.peminjaman_id FOR UPDATE;
  UPDATE alat
  SET stok = stok + 1,
      status = CASE WHEN stok + 1 > 0 AND status = 'tidak_tersedia' THEN 'ada' ELSE status END,
      updated_at = CURRENT_TIMESTAMP
  WHERE id = v_alat_id;
  UPDATE peminjaman
  SET status = 'selesai',
      updated_at = CURRENT_TIMESTAMP
  WHERE id = NEW.peminjaman_id;
END;

-- Stored procedure process_return
DROP PROCEDURE IF EXISTS process_return;
CREATE PROCEDURE process_return(
  IN in_peminjaman_id INT,
  IN in_tgl_kembali DATETIME,
  OUT out_denda DECIMAL(12,2)
)
BEGIN
  DECLARE v_tgl_harap DATETIME;
  DECLARE v_alat_id INT;
  DECLARE v_days_late INT;
  DECLARE v_unit_denda DECIMAL(12,2) DEFAULT 50000;

  START TRANSACTION;

  SELECT tgl_harap_kembali, alat_id INTO v_tgl_harap, v_alat_id
  FROM peminjaman
  WHERE id = in_peminjaman_id
  FOR UPDATE;

  IF v_tgl_harap IS NULL THEN
    SET out_denda = 0;
    ROLLBACK;
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Peminjaman tidak ditemukan';
  END IF;

  IF (SELECT COUNT(1) FROM pengembalian WHERE peminjaman_id = in_peminjaman_id) > 0 THEN
    SET out_denda = 0;
    ROLLBACK;
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengembalian sudah diproses untuk peminjaman ini';
  END IF;

  SET v_days_late = GREATEST(0, DATEDIFF(DATE(in_tgl_kembali), DATE(v_tgl_harap)));
  SET out_denda = v_days_late * v_unit_denda;

  INSERT INTO pengembalian (peminjaman_id, tgl_kembali, kondisi, denda, created_at, updated_at)
  VALUES (in_peminjaman_id, in_tgl_kembali, NULL, out_denda, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

  COMMIT;
END;
SQL;

        DB::unprepared($sql);
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_peminjaman_after_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_pengembalian_after_insert;");
        DB::unprepared("DROP PROCEDURE IF EXISTS process_return;");
    }
};
