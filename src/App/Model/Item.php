<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';

    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'purchased_at',
        'repaired_at'
    ];

    public function product()
    {
        return $this->belongsTo('App\Model\Product');
    }

    public function properties()
    {
        return $this->belongsToMany('App\Model\Property')->withPivot('value');
    }
}