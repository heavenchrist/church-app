<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('members')
            ->whereIn('classification', ['elder', 'deacon', 'deaconess'])
            ->update(['classification' => 'officer']);
    }

    public function down(): void
    {
        // Cannot reverse - we lose the specific officer type info
    }
};
