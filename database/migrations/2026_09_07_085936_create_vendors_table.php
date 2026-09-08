<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Vendor, e.g. PT Swadharma Utama Prima
            $table->string('code')->unique(); // Kode unik vendor, e.g. SUP, VDR-01
            $table->string('contract_number')->nullable(); // Nomor kontrak sewa
            $table->string('client_name')->nullable(); // Nama klien penyewa / mitra kerja
            $table->string('project_name')->nullable(); // Nama proyek tender
            $table->string('pic_name')->nullable(); // Penanggung jawab operasional
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
