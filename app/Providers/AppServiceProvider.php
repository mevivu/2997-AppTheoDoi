<?php

namespace App\Providers;

use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use App\Models\Transaction;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);
        // URL::forceScheme('https');

        View::composer('admin.layouts.sidebar-left', function ($view) {
            $pendingWithdrawCount = 0;
            if (Schema::hasTable('transactions')) {
                $pendingWithdrawCount = Transaction::where('type', TransactionType::Withdraw)
                    ->where('status', TransactionStatus::Pending)
                    ->count();
            }
            $view->with('pendingWithdrawCount', $pendingWithdrawCount);
        });
    }
}
