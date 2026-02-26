<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sbu_types', function (Blueprint $table) {
            $table->json('menu_config')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('sbu_types', function (Blueprint $table) {
            $table->dropColumn('menu_config');
        });
    }
};
