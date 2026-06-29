<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->date('visit_date');
            $table->foreignId('invited_by_member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->string('invited_by_name')->nullable();
            $table->text('how_heard_about_church')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_followed_up')->default(false);
            $table->timestamp('followed_up_at')->nullable();
            $table->enum('status', ['first_visit', 'second_visit', 'interested', 'not_interested', 'became_convert'])->default('first_visit');
            $table->timestamps();

            $table->index('visit_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
