<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RepairItem extends Model
{
    protected $guarded = [];
    public function categories()
    {
        return $this->belongsTo(RepairCategory::class);
    }
    public function stocks()
    {
        return $this->hasMany(RepairStock::class);
    }
}
