<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('period_month_year'); // e.g. Mei 2025, Juni 2025, Juli 2026
            $table->date('invoice_date');
            $table->string('client_name')->default('PT Perkebunan Nusantara I Regional 7');
            $table->integer('crf_count')->default(70);
            $table->decimal('crf_subtotal', 15, 2)->default(109900000); // 70 * 1.570.000
            $table->integer('supra_count')->default(6);
            $table->decimal('supra_subtotal', 15, 2)->default(5160000); // 6 * 860.000
            $table->decimal('gross_amount', 15, 2)->default(115060000);
            $table->decimal('ppn_rate', 5, 2)->default(11.00);
            $table->decimal('ppn_amount', 15, 2)->default(12656600);
            $table->decimal('total_amount_with_ppn', 15, 2)->default(127716600);
            $table->string('bast_number')->nullable();
            $table->enum('status', ['draft', 'tertagih', 'lunas'])->default('tertagih');
            $table->date('payment_date')->nullable();
            $table->string('invoice_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
