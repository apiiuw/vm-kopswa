<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->enum('type', [
                'service_berkala',
                'ganti_ban',
                'ganti_accu',
                'sparepart',
                'motor_pengganti',
                'lainnya'
            ]);
            $table->date('date');
            $table->integer('odometer_km')->nullable();
            $table->string('workshop_name')->nullable(); // AHASS / Bengkel Rekanan
            $table->text('description')->nullable();
            $table->string('item_name')->nullable(); // e.g. Ban Luar Belakang CRF, Accu GS GTZ5S, Paket Servis Lengkap
            $table->integer('qty')->default(1);
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('memo_number')->nullable(); // e.g. SUP/LPG/047/VIII/2025
            $table->string('receipt_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
