<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->enum('claim_type', ['tlo_kehilangan', 'partial_loss', 'kerusakan_berat'])->default('tlo_kehilangan');
            $table->date('incident_date');
            $table->string('police_report_number')->nullable(); // Surat Kehilangan Polsek/Polres
            $table->string('polda_blocking_number')->nullable(); // Surat Blokir STNK Polda Lampung
            $table->decimal('claim_amount', 15, 2)->default(0);
            $table->decimal('settlement_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'diajukan', 'diverifikasi', 'disetujui', 'cair', 'ditolak'])->default('draft');
            $table->text('incident_chronology')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_sim_number')->nullable();
            $table->string('claim_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
