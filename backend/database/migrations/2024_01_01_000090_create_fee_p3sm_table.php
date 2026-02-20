<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fee_p3sm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('cost')->default(0);
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->timestamps();

            $table->index(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_p3sm');
    }
};
