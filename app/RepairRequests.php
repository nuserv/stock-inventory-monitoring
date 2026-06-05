<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RepairRequests extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public function RequestedItems()
    {
        return $this->hasMany(RequestedItem::class);
    }
}
