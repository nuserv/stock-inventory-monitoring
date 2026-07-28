<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Item $item) {
            Branch::query()->pluck('id')->each(function ($branchId) use ($item) {
                Initial::updateOrCreate(
                    [
                        'items_id' => $item->id,
                        'branch_id' => $branchId,
                    ],
                    ['qty' => 5]
                );
            });
        });
    }

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
