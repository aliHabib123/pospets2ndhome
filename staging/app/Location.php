<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo('App\Transfer');
    }



}
