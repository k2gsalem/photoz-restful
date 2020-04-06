<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Testimonials extends Model
{

    protected $fillable = [
        'testimonial_name','testimonial_title','testimonial_desc',
        'testimonial_image','testimonial_date'
                ,'created_by','updated_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
    // public function photos()
    // {
    //     return $this->hasMany('App\Photo','album_id');
    // }
    //
}
