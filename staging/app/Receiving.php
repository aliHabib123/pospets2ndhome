<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Receiving extends Model {

	public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }

    public function receivingItems() {
        return $this->hasMany('App\ReceivingItem');
    }

    public function location()
    {
        return $this->hasOne('App\Location','id','location_id');
    }


}
