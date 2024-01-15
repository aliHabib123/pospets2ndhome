<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model {

	public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function saleItems() {
        return $this->hasMany('App\SaleItem');
    }

    public function location()
    {
        return $this->hasOne('App\Location','id','location_id');
    }


}
