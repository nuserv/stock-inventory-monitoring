<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = [];

    public function PreparedItems()
    {
        return $this->hasmany(PreparedItem::class);
    }
    public function Items()
    {
        return $this->hasmany(Item::class);
    }
}
