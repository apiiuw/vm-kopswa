<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('category')->default('Roda 2 (Motor)')->after('plate_number'); // e.g. Roda 2 (Motor), Roda 4 (Mobil/Truk)
            $table->string('brand')->default('Honda')->after('category'); // e.g. Honda, Yamaha, Kawasaki, Suzuki, Toyota, Mitsubishi
            $table->string('type_name')->nullable()->after('model_name'); // e.g. Trail On/Off Road, Bebek / Underbone, Sport, Matic
            $table->date('tax_annual_date')->nullable()->after('color'); // Tanggal Jatuh Tempo Pajak Tahunan (PKB)
            $table->date('tax_5year_date')->nullable()->after('tax_annual_date'); // Tanggal Jatuh Tempo Pajak Plat 5 Tahunan
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'brand',
                'type_name',
                'tax_annual_date',
                'tax_5year_date'
            ]);
        });
    }
};
