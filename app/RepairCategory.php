<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class RepairCategory extends Model
{
    protected $guarded = [];
    public function items()
    {
        return $this->hasMany(RepairItem::class);
    }
    public function stocks()
    {
        return $this->hasMany(RepairStock::class);
    }
}
