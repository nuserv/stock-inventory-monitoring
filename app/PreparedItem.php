<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreparedItem extends Model
{
    protected $guarded = [];
    public function items()
    {
        return $this->belongsTo(Item::class);
    }
    public function Warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
