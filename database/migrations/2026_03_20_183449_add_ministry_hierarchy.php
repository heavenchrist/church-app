<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ministries', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('ministries')->onDelete('cascade');
            $table->integer('age_min')->nullable()->after('parent_id');
            $table->integer('age_max')->nullable()->after('age_min');
            $table->enum('gender', ['male', 'female', 'both'])->default('both')->after('age_max');
            $table->enum('type', ['traditional', 'group'])->default('group')->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('ministries', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'age_min', 'age_max', 'gender', 'type']);
        });
    }
};
