<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hour extends Model
{
    // 
    protected $fillable = [
        'user_id', 'initialDate', 'finalDate', 'description', 'status', 'total'
    ];
    
    public function user(){
        return $this->belongsTo('App\User');
    }

}
