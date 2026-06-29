<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = Schema::getColumnListing('services');

        if (in_array('title', $columns)) {
            Schema::table('services', function (Blueprint $table) use ($columns) {
                $toDrop = array_filter(['title', 'start_time', 'end_time', 'venue'], fn ($col) => in_array($col, $columns));
                if (! empty($toDrop)) {
                    $table->dropColumn($toDrop);
                }
            });
        }

        Schema::table('services', function (Blueprint $table) {
            $table->string('topic', 255)->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->unique(['service_date', 'topic', 'ministry_id'], 'service_unique');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique('service_unique');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->text('topic')->change();
            $table->string('title')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
        });
    }
};
