<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class WholeSale extends Model {
    #protected $table = 'whole_sales';
    #protected  $foreignKey = 'sale_id';
    #protected  $primaryKey = 'id';

	public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

//      public function saleItems() {
//         return $this->hasMany('App\SaleItem');
//     } 
    
    public function wholeSaleItems(){
        return $this->hasMany('App\WholeSaleItem', 'sale_id');
    }

    public function location()
    {
        return $this->hasOne('App\Location','id','location_id');
    }


}
