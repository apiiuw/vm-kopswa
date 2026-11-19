<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('policy_number');
            $table->string('insurance_company');
            $table->string('coverage_type', 40)->default('all_risk'); // all_risk, tlo, kombinasi
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('premium_amount', 15, 2)->default(0);
            $table->decimal('sum_insured', 15, 2)->default(0);
            $table->string('status', 30)->default('active'); // active, expired, cancelled
            $table->string('policy_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
