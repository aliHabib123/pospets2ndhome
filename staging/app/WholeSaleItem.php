<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class WholeSaleItem extends Model {

    #protected  $foreignKey = 'sale_id';
    protected $table = 'whole_sale_items';
    protected  $foreignKey = 'sale_id';
    #protected  $primaryKey = 'id';
    
    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function wholeSale()
    {
        return $this->belongsTo('App\WholeSale');
    }


}
