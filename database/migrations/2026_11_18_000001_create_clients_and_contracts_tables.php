<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('npwp')->nullable();
            $table->string('nitku')->nullable();
            $table->text('address')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->nullOnDelete();
            $table->string('billing_cycle', 30)->default('bulanan')->after('status');
            $table->unsignedTinyInteger('billing_day')->default(1)->after('billing_cycle');
            $table->decimal('contract_value', 18, 2)->default(0)->after('billing_day');
            $table->string('npwp')->nullable()->after('email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('vendor_id')->constrained('clients')->nullOnDelete();
        });

        // Backfill existing vendors into clients so existing data works per-contract seamlessly
        $existingVendors = DB::table('vendors')->get();
        foreach ($existingVendors as $v) {
            $baseCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($v->code ?: $v->name, 0, 12))) ?: 'CLIENT';
            $client = DB::table('clients')->where('name', $v->name)->first();
            if (! $client) {
                $code = $baseCode;
                $suffix = 1;
                while (DB::table('clients')->where('code', $code)->exists()) {
                    $code = $baseCode.$suffix++;
                }
                $clientId = DB::table('clients')->insertGetId([
                    'name' => $v->name,
                    'code' => $code,
                    'pic_name' => $v->pic_name,
                    'phone' => $v->phone,
                    'email' => $v->email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $clientId = $client->id;
            }

            DB::table('vendors')->where('id', $v->id)->update(['client_id' => $clientId]);
            DB::table('users')->where('vendor_id', $v->id)->update(['client_id' => $clientId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
            $table->dropColumn(['billing_cycle', 'billing_day', 'contract_value', 'npwp']);
        });
        Schema::dropIfExists('clients');
    }
};
