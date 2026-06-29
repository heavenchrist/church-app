<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('members')
            ->where('classification', 'officer')
            ->update(['classification' => 'regular']);
    }

    public function down(): void
    {
        DB::table('members')
            ->whereIn('classification', ['elder', 'deacon', 'deaconess'])
            ->update(['classification' => 'officer']);
    }
};
