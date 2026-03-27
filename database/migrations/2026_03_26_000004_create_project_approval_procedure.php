<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_approve_project');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_approve_project(IN input_project_id BIGINT)
            BEGIN
                DECLARE project_exists INT DEFAULT 0;

                SELECT COUNT(*) INTO project_exists
                FROM projects
                WHERE id = input_project_id AND status = 'pending';

                IF project_exists = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Project not found or already processed';
                ELSE
                    UPDATE projects
                    SET status = 'approved',
                        rejection_reason = NULL,
                        updated_at = NOW()
                    WHERE id = input_project_id;

                    INSERT INTO audit_logs (project_id, user_id, action, details, created_at)
                    VALUES (
                        input_project_id,
                        @approval_user_id,
                        'approved',
                        @approval_action_details,
                        NOW()
                    );

                    SELECT 1 AS success, 'approved' AS status;
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_approve_project');
    }
};
