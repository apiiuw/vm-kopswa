<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handover_documents', function (Blueprint $table) {
            $table->id();
            $table->string('bast_number')->unique();
            $table->string('bast_type', 30); // unit_baru, unit_pengganti, pengembalian
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('replacement_vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->date('handover_date');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('handed_over_by')->nullable();
            $table->string('received_by')->nullable();
            $table->string('recipient_position')->nullable();
            $table->unsignedInteger('odometer_km')->nullable();
            $table->decimal('replacement_cost', 15, 2)->default(0);
            $table->text('condition_notes')->nullable();
            $table->string('signed_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handover_documents');
    }
};
