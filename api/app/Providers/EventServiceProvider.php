<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\CheckUserTokenEvent::class => [
            \App\Listeners\UpdateExpiredUserToken::class
        ],
        \App\Events\PaidOrderEvent::class => [
            \App\Listeners\PaidOrderListener::class
        ],
        \App\Events\StockEnoughtOrderEvent::class => [
            \App\Listeners\StockEnoughtOrderListener::class
        ],
        \App\Events\ActiveTicketEvent::class => [
            \App\Listeners\ActiveTicketListener::class
        ],
        \App\Events\UsedTicketEvent::class => [
            \App\Listeners\UsedTicketListener::class
        ],
        \App\Events\UserLoginFailEvent::class => [
            \App\Listeners\UserLoginFailListener::class
        ],
        \App\Events\UserLoginSuccessEvent::class => [
            \App\Listeners\UserLoginSuccessListener::class
        ],
    ];
}
