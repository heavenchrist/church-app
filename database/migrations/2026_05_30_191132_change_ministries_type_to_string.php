<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `ministries` MODIFY COLUMN `type` VARCHAR(50) NOT NULL DEFAULT 'group'");
    }

    public function down(): void
    {
        DB::statement("UPDATE `ministries` SET `type` = 'group' WHERE `type` NOT IN ('traditional', 'group')");
        DB::statement("ALTER TABLE `ministries` MODIFY COLUMN `type` ENUM('traditional', 'group') NOT NULL DEFAULT 'group'");
    }
};
