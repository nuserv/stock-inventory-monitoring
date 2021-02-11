<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
    protected $guarded = [];
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
        return $this->belongsTo(Area::class);
    }
}
 