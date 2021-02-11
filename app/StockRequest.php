<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    protected $guarded = [];
    protected $table = 'requests';
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
