<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `services` MODIFY COLUMN `service_type` VARCHAR(50) NOT NULL DEFAULT 'sunday_service'");
    }

    public function down(): void
    {
        DB::statement("UPDATE `services` SET `service_type` = 'other' WHERE `service_type` NOT IN ('sunday_service','midweek','bible_study','prayer_meeting','special_service','other')");
        DB::statement("ALTER TABLE `services` MODIFY COLUMN `service_type` ENUM('sunday_service','midweek','bible_study','prayer_meeting','special_service','other') NOT NULL DEFAULT 'sunday_service'");
    }
};
