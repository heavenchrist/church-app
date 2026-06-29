<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `members` DROP FOREIGN KEY `members_bible_study_group_id_foreign`');
        DB::statement('ALTER TABLE `members` MODIFY COLUMN `bible_study_group_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `members` ADD CONSTRAINT `members_bible_study_group_id_foreign` FOREIGN KEY (`bible_study_group_id`) REFERENCES `bible_study_groups`(`id`) ON DELETE RESTRICT');
    }

    public function down(): void
    {
        DB::statement('UPDATE `members` SET `bible_study_group_id` = (SELECT `id` FROM `bible_study_groups` WHERE `is_active` = true LIMIT 1) WHERE `bible_study_group_id` IS NULL');
        DB::statement('ALTER TABLE `members` DROP FOREIGN KEY `members_bible_study_group_id_foreign`');
        DB::statement('ALTER TABLE `members` MODIFY COLUMN `bible_study_group_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `members` ADD CONSTRAINT `members_bible_study_group_id_foreign` FOREIGN KEY (`bible_study_group_id`) REFERENCES `bible_study_groups`(`id`) ON DELETE RESTRICT');
    }
};
