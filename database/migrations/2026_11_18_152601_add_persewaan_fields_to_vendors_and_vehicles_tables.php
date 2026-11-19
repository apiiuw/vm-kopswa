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
        Schema::table('vendors', function (Blueprint $table) {
            $table->date('contract_date')->nullable()->after('contract_number');
            $table->unsignedInteger('duration_months')->nullable()->after('contract_end_date');
            $table->decimal('main_contract_value', 15, 2)->default(0)->after('contract_value');
            $table->decimal('main_monthly_value', 15, 2)->default(0)->after('main_contract_value');
            $table->decimal('additional_contract_value', 15, 2)->default(0)->after('main_monthly_value');
            $table->decimal('additional_monthly_value', 15, 2)->default(0)->after('additional_contract_value');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('total_contract_value', 15, 2)->default(0)->after('rental_price_per_month');
            $table->decimal('additional_contract_value', 15, 2)->default(0)->after('total_contract_value');
            $table->decimal('additional_monthly_value', 15, 2)->default(0)->after('additional_contract_value');
            $table->decimal('ppn_pm', 15, 2)->default(0)->after('acquisition_value');
            $table->decimal('residual_percentage', 5, 2)->default(0)->after('residual_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'contract_date',
                'duration_months',
                'main_contract_value',
                'main_monthly_value',
                'additional_contract_value',
                'additional_monthly_value',
            ]);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'total_contract_value',
                'additional_contract_value',
                'additional_monthly_value',
                'ppn_pm',
                'residual_percentage',
            ]);
        });
    }
};
