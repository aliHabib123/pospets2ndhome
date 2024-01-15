<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model {

    public function fromLocation()
    {
        return $this->hasOne('App\Location','id','from_location');
    }

    public function toLocation()
    {
        return $this->hasOne('App\Location','id','to_location');
    }

    public function fromUser()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function toUser()
    {
        return $this->belongsTo('App\User');
    }


    public function transferItems() {
        return $this->hasMany('App\TransferTemp');
    }

}
