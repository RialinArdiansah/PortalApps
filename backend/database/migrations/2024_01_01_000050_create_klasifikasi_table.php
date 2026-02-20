<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('klasifikasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sbu_type_id')->constrained('sbu_types')->cascadeOnDelete();
            $table->foreignUuid('asosiasi_id')->nullable()->constrained('asosiasi')->cascadeOnDelete();
            $table->string('name');
            $table->json('sub_klasifikasi')->default('[]');
            $table->json('kualifikasi')->nullable();
            $table->json('sub_bidang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klasifikasi');
    }
};
