<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestedItem extends Model
{
    protected $guarded = [];
    public function items()
    {
        return $this->belongsTo(Item::class);
    }
    public function StockRequest()
    {
        return $this->belongsTo(StockRequest::class);
    }
}
