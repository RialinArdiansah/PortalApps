<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('biaya_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sbu_type_id')->constrained('sbu_types')->cascadeOnDelete();
            $table->foreignUuid('asosiasi_id')->nullable()->constrained('asosiasi')->nullOnDelete();
            $table->enum('category', ['kualifikasi', 'biaya_setor', 'biaya_lainnya']);
            $table->string('name');
            $table->bigInteger('biaya')->default(0);
            $table->timestamps();

            $table->index(['sbu_type_id', 'asosiasi_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_items');
    }
};
