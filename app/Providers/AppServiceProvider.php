<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Carbon\Carbon::setLocale('id');

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $hasVendorsTable = \Illuminate\Support\Facades\Schema::hasTable('vendors');
            $allVendors = $hasVendorsTable ? \App\Models\Vendor::orderBy('name', 'asc')->get() : collect();
            $activeVendorId = session('active_vendor_id');
            $currentVendor = ($hasVendorsTable && $activeVendorId) ? \App\Models\Vendor::find($activeVendorId) : null;

            $pendingRequestsCount = \Illuminate\Support\Facades\Schema::hasTable('client_requests') 
                ? \App\Models\ClientRequest::where('status', 'menunggu_review')->count() 
                : 0;

            $view->with([
                'globalVendors' => $allVendors,
                'activeVendorId' => $activeVendorId,
                'currentVendor' => $currentVendor,
                'globalPendingRequestsCount' => $pendingRequestsCount,
            ]);
        });
    }
}
