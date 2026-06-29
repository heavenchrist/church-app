<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bible_study_groups', function (Blueprint $table) {
            $table->foreignId('leader_id')->nullable()->after('description')->constrained('members')->onDelete('set null');
            $table->dropColumn(['leader_name', 'leader_phone', 'meeting_day', 'meeting_time', 'location']);
        });
    }

    public function down(): void
    {
        Schema::table('bible_study_groups', function (Blueprint $table) {
            $table->string('leader_name')->nullable();
            $table->string('leader_phone')->nullable();
            $table->string('meeting_day')->nullable();
            $table->time('meeting_time')->nullable();
            $table->string('location')->nullable();
            $table->dropForeign(['leader_id']);
            $table->dropColumn('leader_id');
        });
    }
};
