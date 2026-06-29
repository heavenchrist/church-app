<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministry_executives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('position', ['leader', 'assistant', 'executive_member', 'organiser', 'secretary', 'financial_secretary', 'coordinator']);
            $table->date('assigned_date')->useCurrent();
            $table->timestamps();
            $table->unique(['ministry_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministry_executives');
    }
};
