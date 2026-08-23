<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE alat MODIFY stok INT NOT NULL DEFAULT 0');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_peminjaman_after_update;');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_peminjaman_after_update
AFTER UPDATE ON peminjaman
FOR EACH ROW
BEGIN
  DECLARE current_stok INT;

  IF NEW.status = 'disetujui' AND OLD.status <> 'disetujui' THEN
    SELECT CAST(stok AS SIGNED) INTO current_stok
    FROM alat
    WHERE id = NEW.alat_id
    FOR UPDATE;

    IF current_stok <= 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak cukup untuk menyetujui peminjaman';
    ELSE
      UPDATE alat
      SET stok = CASE WHEN CAST(stok AS SIGNED) > 0 THEN CAST(stok AS SIGNED) - 1 ELSE 0 END,
          status = CASE WHEN CASE WHEN CAST(stok AS SIGNED) > 0 THEN CAST(stok AS SIGNED) - 1 ELSE 0 END <= 0 THEN 'tidak_tersedia' ELSE status END,
          updated_at = CURRENT_TIMESTAMP
      WHERE id = NEW.alat_id;
    END IF;
  END IF;
END
SQL
);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_after_insert;');
        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_pengembalian_after_insert
AFTER INSERT ON pengembalian
FOR EACH ROW
BEGIN
  DECLARE v_alat_id INT;

  SELECT alat_id INTO v_alat_id
  FROM peminjaman
  WHERE id = NEW.peminjaman_id
  FOR UPDATE;

  UPDATE alat
  SET stok = CAST(stok AS SIGNED) + 1,
      status = CASE WHEN CAST(stok AS SIGNED) + 1 > 0 AND status = 'tidak_tersedia' THEN 'ada' ELSE status END,
      updated_at = CURRENT_TIMESTAMP
  WHERE id = v_alat_id;

  UPDATE peminjaman
  SET status = 'selesai',
      updated_at = CURRENT_TIMESTAMP
  WHERE id = NEW.peminjaman_id;
END
SQL
);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_peminjaman_after_update;');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_after_insert;');
        DB::statement('ALTER TABLE alat MODIFY stok INT UNSIGNED NOT NULL DEFAULT 0');
    }
};
