<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('member_id');
        });

        DB::statement('UPDATE members SET full_name = CONCAT(IFNULL(first_name, ""), " ", IFNULL(middle_name, ""), " ", IFNULL(last_name, ""))');

        Schema::table('members', function (Blueprint $table) {
            $table->string('full_name')->nullable(false)->change();
            $table->dropColumn(['first_name', 'middle_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('member_id');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
        });

        DB::statement('UPDATE members SET first_name = TRIM(SUBSTRING_INDEX(full_name, " ", 1))');
        DB::statement('UPDATE members SET last_name = TRIM(SUBSTRING_INDEX(full_name, " ", -1))');

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }
};
