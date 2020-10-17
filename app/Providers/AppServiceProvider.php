<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //商用環境意外だった場合はSQLログを出力する
        if(config('app.env') !== 'production'){
          \DB::listen(function ($query){
            \Log::info("Query Time:{$query->time}s] $query->sql");
          });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
