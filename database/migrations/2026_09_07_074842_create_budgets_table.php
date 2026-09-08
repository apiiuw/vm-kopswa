<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_group'); // honda_crf_150, honda_supra_x_125, operasional_umum
            $table->string('category'); // pajak_stnk, service_berkala, ban, accu, sparepart, asuransi, operasional_tak_terduga
            $table->string('label'); // Nama Tampilan
            $table->integer('qty_units')->default(1);
            $table->decimal('unit_rate_monthly', 15, 2)->default(0);
            $table->decimal('unit_rate_yearly', 15, 2)->default(0);
            $table->decimal('total_budget_36m', 15, 2)->default(0); // Plafon 36 bulan
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
