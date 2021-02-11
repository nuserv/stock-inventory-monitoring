<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];
    public function categories()
    {
        return $this->belongsTo(Category::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
    public function RequestedItems()
    {
        return $this->hasMany(RequestedItem::class);
    }
    public function PreparedItems()
    {
        return $this->hasMany(PreparedItem::class);
    }
    public function Warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
