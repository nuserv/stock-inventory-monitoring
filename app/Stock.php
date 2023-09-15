<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [];
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function categ()
    {
        return $this->belongsTo(Category::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
