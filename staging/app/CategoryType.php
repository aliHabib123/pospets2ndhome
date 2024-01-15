<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nestable\NestableTrait;

class CategoryType extends Model
{
    use NestableTrait;


    public function category()
    {
        return $this->belongsTo('App\Category');
    }

}
