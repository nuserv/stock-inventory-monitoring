<?php

namespace App\Providers;

use App\Mail\Transport\RotatingSmtpTransport;
use App\Mail\MailPoolAccountRepository;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
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
        $this->app->extend('swift.transport', function ($manager, $app) {
            $manager->extend('smtp_pool', function ($app) {
                return new RotatingSmtpTransport(
                    $app->make(MailPoolAccountRepository::class),
                    $app['config']->get('mail'),
                    null,
                    $app->make(LoggerInterface::class)
                );
            });

            return $manager;
        });
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
