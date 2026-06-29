<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('topic')->nullable();
            $table->text('description')->nullable();
            $table->date('service_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->enum('service_type', ['sunday_service', 'midweek', 'bible_study', 'prayer_meeting', 'youth_service', 'children_service', 'special_service', 'other'])->default('sunday_service');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('service_date');
            $table->index('service_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
