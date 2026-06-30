<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Database\Eloquent\Model;
use App\Models\OperationalRecordDetail;
use App\Observers\OperationalRecordDetailObserver;

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
        Model::unguard();
        OperationalRecordDetail::observe(OperationalRecordDetailObserver::class);
    }
}
