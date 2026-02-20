<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('marketing_name');
            $table->date('input_date');
            $table->foreignUuid('submitted_by_id')->constrained('users')->cascadeOnDelete();
            $table->string('certificate_type');
            $table->string('sbu_type', 50);
            $table->json('selected_sub')->nullable();
            $table->json('selected_klasifikasi')->nullable();
            $table->string('selected_sub_klasifikasi')->nullable();
            $table->json('selected_kualifikasi')->nullable();
            $table->json('selected_biaya_lainnya')->nullable();
            $table->bigInteger('biaya_setor_kantor')->default(0);
            $table->bigInteger('keuntungan')->default(0);
            $table->timestamps();

            $table->index('submitted_by_id');
            $table->index('input_date');
            $table->index('marketing_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
