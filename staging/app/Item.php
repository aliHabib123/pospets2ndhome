<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Item extends Model
{
    use Searchable;

    public function inventory ()
    {
        return $this->hasMany('App\Inventory')->orderBy('id', 'DESC');
    }

    public function quantity ()
    {
        return $this->hasOne('App\ItemQuantity')->orderBy('id', 'DESC');
    }

    public function category ()
    {
        return $this->hasOne('App\Category');
    }

    public function receivingtemp ()
    {
        return $this->hasMany('App\ReceivingTemp')->orderBy('id', 'DESC');
    }


    public function searchableAs()
    {
        return 'items_data';
    }

    public function location()
    {
        return $this->belongsTo('App\TransferTemp');
    }


}
