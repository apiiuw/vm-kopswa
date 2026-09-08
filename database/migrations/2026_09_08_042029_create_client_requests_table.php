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
        Schema::create('client_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->enum('request_type', [
                'perbaikan_servis',      // Pengajuan Perbaikan / Servis
                'ganti_ban_accu',        // Penggantian Ban / Accu
                'unit_pengganti',        // Permohonan Unit Pengganti Sementara
                'lapor_kehilangan',      // Laporan Insiden / Kehilangan Unit (TLO)
                'input_hasil_perbaikan', // Input Data & Bukti Selesai Perbaikan
                'lainnya'
            ]);
            $table->string('title');
            $table->text('description');
            $table->integer('odometer_km')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('workshop_name')->nullable(); // Bengkel rujukan
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->json('proof_files')->nullable(); // Foto kerusakan / nota / BAP
            $table->enum('status', [
                'menunggu_review', // Baru diajukan
                'diproses',        // Sedang ditangani tim SUP
                'unit_dikirim',    // Unit pengganti dalam pengiriman
                'selesai',         // Selesai / Disetujui
                'ditolak'          // Ditolak dengan alasan
            ])->default('menunggu_review');
            $table->text('admin_response')->nullable(); // Catatan balasan admin PT SUP
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_requests');
    }
};
