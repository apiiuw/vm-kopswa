<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Handled by 2026_11_19_000002_fix_vehicle_type_column_and_resync_persewaan
     * after altering vehicles.vehicle_type from ENUM to VARCHAR(80).
     */
    public function up(): void
    {
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
