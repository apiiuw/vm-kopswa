<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('status', 50)->default('active')->change();
            $table->string('asset_type', 30)->default('motor')->after('vendor_id');
            $table->string('asset_code', 80)->nullable()->after('asset_type');
            $table->string('serial_number', 120)->nullable()->after('asset_code');
            $table->string('billing_cycle', 30)->default('bulanan')->after('rental_price_per_month');

            // Valuation, Depreciation & Dealer Discount
            $table->decimal('invoice_value', 15, 2)->default(0)->after('billing_cycle');
            $table->decimal('acquisition_value', 15, 2)->default(0)->after('invoice_value');
            $table->string('dealer_name')->nullable()->after('acquisition_value');
            $table->decimal('dealer_discount', 15, 2)->default(0)->after('dealer_name');
            $table->date('acquisition_date')->nullable()->after('dealer_discount');
            $table->unsignedInteger('useful_life_months')->default(48)->after('acquisition_date');
            $table->string('depreciation_method', 30)->default('straight_line')->after('useful_life_months');
            $table->decimal('residual_value', 15, 2)->default(0)->after('depreciation_method');

            // Sale tracking
            $table->date('sold_date')->nullable()->after('residual_value');
            $table->decimal('sold_price', 15, 2)->nullable()->after('sold_date');
            $table->string('sold_to')->nullable()->after('sold_price');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'asset_type',
                'asset_code',
                'serial_number',
                'billing_cycle',
                'invoice_value',
                'acquisition_value',
                'dealer_name',
                'dealer_discount',
                'acquisition_date',
                'useful_life_months',
                'depreciation_method',
                'residual_value',
                'sold_date',
                'sold_price',
                'sold_to',
            ]);
        });
    }
};
