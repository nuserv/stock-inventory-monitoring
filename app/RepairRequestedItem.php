<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RepairRequestedItem extends Model
{
    protected $guarded = [];
    public function items()
    {
        return $this->belongsTo(RepairItem::class);
    }
    public function RepairRequest()
    {
        return $this->belongsTo(RepairRequest::class);
    }
}
