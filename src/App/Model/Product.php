<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    protected $primaryKey = 'id';

    protected $fillable = ['name'];

    public function getProperties()
    {
        $properties = $this->categories->first()->parent->properties;
        $properties = $properties->merge($this->categories->first()->properties);
        return $properties->merge($this->properties);
    }

    public function items()
    {
        return $this->hasMany('App\Model\Item');
    }

    public function properties()
    {
        return $this->belongsToMany('App\Model\Property')->withPivot('required');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Model\Category');
    }
}