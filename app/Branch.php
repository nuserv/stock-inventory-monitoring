<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Branch $branch) {
            $items = Item::all();
            foreach ($items as $item) {
                Initial::firstOrCreate(
                    [
                        'items_id' => $item->id,
                        'branch_id' => $branch->id,
                    ],
                    ['qty' => 5]
                );
            }
        });
    }

    public function users()
    {
        return $this->hasmany(User::class);
    }
    public function StockRequests()
    {
        return $this->hasmany(StockRequest::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }
    
}
 