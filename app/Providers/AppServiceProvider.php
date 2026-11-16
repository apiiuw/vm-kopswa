<?php

namespace App\Providers;

use App\Models\ClientRequest;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        Carbon::setLocale('id');

        if (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https' || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $hasVendorsTable = Schema::hasTable('vendors');
            $allVendors = $hasVendorsTable ? Vendor::orderBy('name', 'asc')->get() : collect();
            $activeVendorId = session('active_vendor_id');
            $currentVendor = ($hasVendorsTable && $activeVendorId) ? Vendor::find($activeVendorId) : null;

            $pendingRequestsCount = Schema::hasTable('client_requests')
                ? ClientRequest::where('status', 'menunggu_review')->count()
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
