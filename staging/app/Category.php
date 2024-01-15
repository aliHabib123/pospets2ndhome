<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nestable\NestableTrait;

class Category extends Model
{
    use NestableTrait;


    protected $parent = 'parent_id';

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function type()
    {
        return $this->belongsTo('App\CategoryType');
    }

}
