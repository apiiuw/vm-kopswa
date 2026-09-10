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
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('odometer_km');
            $table->string('driver_phone')->nullable()->after('driver_name');
            $table->string('pic_name')->nullable()->after('driver_phone');
            $table->string('pic_phone')->nullable()->after('pic_name');
            $table->string('workshop_phone')->nullable()->after('workshop_name');
            $table->string('mechanic_name')->nullable()->after('workshop_phone');
            $table->string('mechanic_phone')->nullable()->after('mechanic_name');
            $table->string('vehicle_operational_status')->nullable()->after('mechanic_phone');
            $table->string('vehicle_current_location')->nullable()->after('vehicle_operational_status');
            $table->text('vehicle_condition_notes')->nullable()->after('vehicle_current_location');
            $table->json('proof_files')->nullable()->after('receipt_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropColumn([
                'driver_name',
                'driver_phone',
                'pic_name',
                'pic_phone',
                'workshop_phone',
                'mechanic_name',
                'mechanic_phone',
                'vehicle_operational_status',
                'vehicle_current_location',
                'vehicle_condition_notes',
                'proof_files',
            ]);
        });
    }
};
