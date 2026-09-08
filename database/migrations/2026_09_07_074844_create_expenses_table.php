<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('maintenance_record_id')->nullable()->constrained('maintenance_records')->nullOnDelete();
            $table->foreignId('vehicle_document_id')->nullable()->constrained('vehicle_documents')->nullOnDelete();
            $table->string('expense_number')->unique();
            $table->string('journal_entry_number')->nullable(); // e.g. Journal Entry 10109
            $table->string('memo_number')->nullable(); // e.g. SUP/LPG/047/VIII/2025
            $table->date('transaction_date');
            $table->enum('category', [
                'pajak_stnk',
                'service_berkala',
                'ban',
                'accu',
                'sparepart',
                'asuransi',
                'motor_pengganti',
                'legalitas_kepolisian',
                'overhead_admin'
            ]);
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->string('recipient_vendor')->nullable(); // AHASS / Toko Ban / Karyawan / Pihak Rental
            $table->text('description')->nullable();
            $table->string('proof_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
