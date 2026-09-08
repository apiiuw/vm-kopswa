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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('stnk_number')->nullable()->after('notes');
            $table->string('stnk_file')->nullable()->after('stnk_number');
            $table->string('bpkb_number')->nullable()->after('stnk_file');
            $table->string('bpkb_file')->nullable()->after('bpkb_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['stnk_number', 'stnk_file', 'bpkb_number', 'bpkb_file']);
        });
    }
};
