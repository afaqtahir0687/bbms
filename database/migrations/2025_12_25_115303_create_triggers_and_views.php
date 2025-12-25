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
        // Triggers
        $triggers = [
            "
            CREATE TRIGGER trg_campaign_dates_bi BEFORE INSERT ON campaign FOR EACH ROW
            BEGIN
                IF NEW.end_date < NEW.start_date THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Campaign end_date cannot be earlier than start_date';
                END IF;
            END
            ",
            "
            CREATE TRIGGER trg_campaign_dates_bu BEFORE UPDATE ON campaign FOR EACH ROW
            BEGIN
                IF NEW.end_date < NEW.start_date THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Campaign end_date cannot be earlier than start_date';
                END IF;
            END
            ",
             "
            CREATE TRIGGER trg_allocation_dates_bi BEFORE INSERT ON allocation FOR EACH ROW
            BEGIN
                IF NEW.allocated_to < NEW.allocated_from THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Allocation allocated_to cannot be earlier than allocated_from';
                END IF;
            END
            ",
            "
            CREATE TRIGGER trg_allocation_dates_bu BEFORE UPDATE ON allocation FOR EACH ROW
            BEGIN
                IF NEW.allocated_to < NEW.allocated_from THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Allocation allocated_to cannot be earlier than allocated_from';
                END IF;
            END
            ",
            "
            CREATE TRIGGER trg_soft_booking_dates_bi BEFORE INSERT ON soft_booking FOR EACH ROW
            BEGIN
                IF NEW.hold_to < NEW.hold_from THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Soft booking hold_to cannot be earlier than hold_from';
                END IF;
            END
            ",
            "
            CREATE TRIGGER trg_soft_booking_dates_bu BEFORE UPDATE ON soft_booking FOR EACH ROW
            BEGIN
                IF NEW.hold_to < NEW.hold_from THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Soft booking hold_to cannot be earlier than hold_from';
                END IF;
            END
            ",
            "
            CREATE TRIGGER trg_allocation_no_overlap_bi BEFORE INSERT ON allocation FOR EACH ROW
            BEGIN
                IF NEW.allocation_status IN ('Planned','Installed','Live') THEN
                    IF EXISTS (SELECT 1 FROM allocation a WHERE a.billboard_id = NEW.billboard_id AND a.allocation_status IN ('Planned','Installed','Live') AND NOT (NEW.allocated_to < a.allocated_from OR NEW.allocated_from > a.allocated_to) LIMIT 1) THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping allocation detected for the same billboard';
                    END IF;
                END IF;
            END
            ",
            "
            CREATE TRIGGER trg_allocation_no_overlap_bu BEFORE UPDATE ON allocation FOR EACH ROW
            BEGIN
                IF NEW.allocation_status IN ('Planned','Installed','Live') THEN
                    IF EXISTS (SELECT 1 FROM allocation a WHERE a.billboard_id = NEW.billboard_id AND a.allocation_id <> NEW.allocation_id AND a.allocation_status IN ('Planned','Installed','Live') AND NOT (NEW.allocated_to < a.allocated_from OR NEW.allocated_from > a.allocated_to) LIMIT 1) THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping allocation detected for the same billboard';
                    END IF;
                END IF;
            END
            ",
            "
            CREATE TRIGGER trg_soft_booking_no_overlap_bi BEFORE INSERT ON soft_booking FOR EACH ROW
            BEGIN
                IF NEW.hold_status IN ('HOLD','RESERVED') THEN
                    IF EXISTS (SELECT 1 FROM soft_booking sb WHERE sb.billboard_id = NEW.billboard_id AND sb.hold_status IN ('HOLD','RESERVED') AND (sb.expires_at IS NULL OR sb.expires_at > NOW()) AND NOT (NEW.hold_to < sb.hold_from OR NEW.hold_from > sb.hold_to) LIMIT 1) THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping soft booking hold detected for the same billboard';
                    END IF;

                    IF EXISTS (SELECT 1 FROM allocation a WHERE a.billboard_id = NEW.billboard_id AND a.allocation_status IN ('Planned','Installed','Live') AND NOT (NEW.hold_to < CONCAT(a.allocated_from, ' 00:00:00') OR NEW.hold_from > CONCAT(a.allocated_to, ' 23:59:59')) LIMIT 1) THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Soft booking overlaps an active allocation for the same billboard';
                    END IF;
                END IF;
            END
            ",
             "
            CREATE TRIGGER trg_soft_booking_no_overlap_bu BEFORE UPDATE ON soft_booking FOR EACH ROW
            BEGIN
                IF NEW.hold_status IN ('HOLD','RESERVED') THEN
                    IF EXISTS (SELECT 1 FROM soft_booking sb WHERE sb.billboard_id = NEW.billboard_id AND sb.soft_booking_id <> NEW.soft_booking_id AND sb.hold_status IN ('HOLD','RESERVED') AND (sb.expires_at IS NULL OR sb.expires_at > NOW()) AND NOT (NEW.hold_to < sb.hold_from OR NEW.hold_from > sb.hold_to) LIMIT 1) THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping soft booking hold detected for the same billboard';
                    END IF;

                    IF EXISTS (SELECT 1 FROM allocation a WHERE a.billboard_id = NEW.billboard_id AND a.allocation_status IN ('Planned','Installed','Live') AND NOT (NEW.hold_to < CONCAT(a.allocated_from, ' 00:00:00') OR NEW.hold_from > CONCAT(a.allocated_to, ' 23:59:59')) LIMIT 1) THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Soft booking overlaps an active allocation for the same billboard';
                    END IF;
                END IF;
            END
            ",
            "
            CREATE TRIGGER trg_picture_uploader_must_be_assigned_bi BEFORE INSERT ON picture FOR EACH ROW
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM allocation_uploader_assignment aua WHERE aua.allocation_id = NEW.allocation_id AND aua.uploader_user_id = NEW.uploaded_by AND aua.active_flag = 1 LIMIT 1) THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Uploader is not assigned to this allocation';
                END IF;
            END
            "
        ];

        foreach ($triggers as $trigger) {
            DB::unprepared($trigger);
        }

        // Views
        DB::statement("
            CREATE OR REPLACE VIEW vw_client_verification_report AS
            SELECT
              cu.customer_code,
              cu.customer_name,
              ca.campaign_code,
              ca.start_date   AS campaign_start_date,
              ca.end_date     AS campaign_end_date,
              ca.status       AS campaign_status,

              bb.billboard_code,
              bb.display_name,
              bt.type_name    AS billboard_type,
              bb.status       AS billboard_status,

              al.allocation_id,
              al.allocated_from,
              al.allocated_to,
              al.allocation_status,

              p.picture_id,
              p.uploaded_at,
              p.file_path,
              p.picture_status,

              v.verification_id,
              v.verified_at,
              v.result        AS verification_result,

              u_up.full_name  AS uploaded_by_name,
              u_ver.full_name AS verified_by_name

            FROM allocation al
            JOIN campaign ca ON ca.campaign_id = al.campaign_id
            JOIN customer cu ON cu.customer_id = ca.customer_id

            JOIN billboard bb ON bb.billboard_id = al.billboard_id
            JOIN billboard_type bt ON bt.billboard_type_id = bb.billboard_type_id

            LEFT JOIN picture p
              ON p.picture_id = (
                SELECT p2.picture_id
                FROM picture p2
                WHERE p2.allocation_id = al.allocation_id
                ORDER BY p2.uploaded_at DESC, p2.picture_id DESC
                LIMIT 1
              )

            LEFT JOIN verification v ON v.picture_id = p.picture_id
            LEFT JOIN app_user u_up ON u_up.user_id = p.uploaded_by
            LEFT JOIN app_user u_ver ON u_ver.user_id = v.verified_by;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_client_verification_report");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_picture_uploader_must_be_assigned_bi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_soft_booking_no_overlap_bu");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_soft_booking_no_overlap_bi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_allocation_no_overlap_bu");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_allocation_no_overlap_bi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_soft_booking_dates_bu");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_soft_booking_dates_bi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_allocation_dates_bu");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_allocation_dates_bi");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_campaign_dates_bu");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_campaign_dates_bi");
    }
};
