<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerBranch extends Model
{

    protected $guarded = [];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
