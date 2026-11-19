<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('vehicle_type', 80)->default('honda_crf_150')->change();
        });

        if (app()->runningUnitTests()) {
            return;
        }

        if (file_exists(base_path('qryPersewaan.xlsx'))) {
            Artisan::call('app:import-persewaan', [
                '--fresh' => true,
                '--with-clients' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
