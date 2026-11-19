<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('income_type', 30)->default('sewa')->after('vendor_id'); // sewa, lain_lain
            $table->string('contract_number')->nullable()->after('income_type');
            $table->string('billing_cycle', 30)->default('bulanan')->after('contract_number');
            $table->date('due_date')->nullable()->after('invoice_date');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('total_amount_with_ppn');
            $table->foreignId('vehicle_id')->nullable()->after('paid_amount')->constrained('vehicles')->nullOnDelete();
            $table->string('source_name')->nullable()->after('vehicle_id');
        });

        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_id')->nullable()->constrained('incomes')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('faktur_number')->unique();
            $table->string('transaction_code', 10)->default('04');
            $table->string('tax_period', 20);
            $table->date('faktur_date');
            $table->string('buyer_name');
            $table->string('buyer_npwp')->nullable();
            $table->string('buyer_nitku')->nullable();
            $table->text('buyer_address')->nullable();
            $table->decimal('dpp_amount', 15, 2)->default(0);
            $table->decimal('ppn_rate', 5, 2)->default(11.00);
            $table->decimal('ppn_amount', 15, 2)->default(0);
            $table->decimal('pph_amount', 15, 2)->default(0);
            $table->string('coretax_status', 30)->default('draft'); // draft, exported, uploaded, approved
            $table->string('coretax_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token_hash')->unique();
            $table->string('plain_prefix', 20)->nullable();
            $table->json('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
        Schema::dropIfExists('tax_invoices');
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vehicle_id');
            $table->dropColumn([
                'income_type',
                'contract_number',
                'billing_cycle',
                'due_date',
                'paid_amount',
                'source_name',
            ]);
        });
    }
};
