<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('plate_number')->unique();
            $table->enum('vehicle_type', ['honda_crf_150', 'honda_supra_x_125'])->default('honda_crf_150');
            $table->string('model_name')->default('Honda CRF 150 L');
            $table->string('frame_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->year('year')->default(2025);
            $table->string('color')->default('Hitam / Merah');
            $table->string('user_holder')->nullable(); // e.g. Asisten Afdeling 1, Asisten Teknik
            $table->decimal('rental_price_per_month', 15, 2)->default(1570000);
            $table->enum('status', ['active', 'maintenance', 'replaced', 'lost', 'claimed'])->default('active');
            $table->string('replacement_plate_number')->nullable(); // plat pengganti jika motor sedang diservis/hilang
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
