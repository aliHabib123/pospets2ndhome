<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    public function getTypeNameAttribute()
    {
        if ($this->id == 0) {
            return 'Service';
        }

        if ($this->id == 1) {
            return 'Product';
        }

        return 'Product';
    }
}
