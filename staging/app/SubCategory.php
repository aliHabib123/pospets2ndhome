<?php

namespace App;

use App\Category;

use Nestable\NestableTrait;

class SubCategory extends \Eloquent {

    use NestableTrait;

    protected $parent = 'parent_id';
}
