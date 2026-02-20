<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('transaction_date');
            $table->string('transaction_name');
            $table->bigInteger('cost')->default(0);
            $table->enum('transaction_type', ['Keluar', 'Tabungan', 'Kas']);
            $table->foreignUuid('submitted_by_id')->constrained('users')->cascadeOnDelete();
            $table->string('proof')->nullable();
            $table->timestamps();

            $table->index('submitted_by_id');
            $table->index('transaction_date');
            $table->index('transaction_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
