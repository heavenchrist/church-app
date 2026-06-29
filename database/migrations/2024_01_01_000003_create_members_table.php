<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_study_group_id')->constrained()->onDelete('restrict');
            $table->string('member_id')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('residential_address')->nullable();
            $table->string('gps_address')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed', 'separated'])->default('single');
            $table->date('water_baptism_date')->nullable();
            $table->date('holy_spirit_baptism_date')->nullable();
            $table->date('date_joined')->nullable();
            $table->enum('classification', ['regular', 'officer', 'elder', 'deacon', 'deaconess'])->default('regular');
            $table->string('status', 20)->default('member');
            $table->string('profile_photo')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['first_name', 'last_name']);
            $table->index('status');
            $table->index('classification');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
