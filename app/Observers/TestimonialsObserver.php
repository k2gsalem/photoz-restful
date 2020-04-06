<?php

namespace App\Observers;

use App\Testimonials;

class TestimonialsObserver
{
    /**
     * Handle the testimonials "created" event.
     *
     * @param  \App\Testimonials  $testimonials
     * @return void
     */
    public function creating(Testimonials $testimonials)
    {

        $user_id = auth()->user()->id;
        $testimonials->created_by = $user_id;
        $testimonials->updated_by = $user_id;
        //
    }

    /**
     * Handle the testimonials "updated" event.
     *
     * @param  \App\Testimonials  $testimonials
     * @return void
     */
    public function updated(Testimonials $testimonials)
    {
        $user_id = auth()->user()->id;
        // $testimonials->created_by = $user_id;
        $testimonials->updated_by = $user_id;
        //
    }

    /**
     * Handle the testimonials "deleted" event.
     *
     * @param  \App\Testimonials  $testimonials
     * @return void
     */
    public function deleted(Testimonials $testimonials)
    {
        //
    }

    /**
     * Handle the testimonials "restored" event.
     *
     * @param  \App\Testimonials  $testimonials
     * @return void
     */
    public function restored(Testimonials $testimonials)
    {
        //
    }

    /**
     * Handle the testimonials "force deleted" event.
     *
     * @param  \App\Testimonials  $testimonials
     * @return void
     */
    public function forceDeleted(Testimonials $testimonials)
    {
        //
    }
}
