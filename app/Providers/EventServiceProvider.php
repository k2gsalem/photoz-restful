<?php

namespace App\Providers;

use App\Album;
use App\Observers\AlbumObserver;
use App\Observers\Photoobserver;
use App\Observers\TestimonialsObserver;
use App\Photo;
use App\Testimonials;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
        Album::observe(AlbumObserver::class);
        Photo::observe(Photoobserver::class);
        Testimonials::observe(TestimonialsObserver::class);
    }
}
