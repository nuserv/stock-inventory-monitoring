<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Area extends Model
{
    protected $guarded = [];
    public function branches()
    {
        return $this->hasmany(Branch::class);
    }
    public function users()
    {
        return $this->hasmany(User::class);
    }
    public function StockRequests()
    {
        return $this->hasmany(StockRequest::class);
    }
}
