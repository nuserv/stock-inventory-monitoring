<?php

namespace App\Providers;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use DB;

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
        Event::listen(MessageSending::class, function () {
            if (config('email.disabled')) {
                return false;
            }
        });

        // DB::listen(function ($query) {
        //     \Log::channel('daily')->info($query->sql, ['bindings' => $query->bindings, 'time' => $query->time]);
        // });
        // Magdagdag ng event listener upang mag-log ng mga query sa database
        
    }
}
