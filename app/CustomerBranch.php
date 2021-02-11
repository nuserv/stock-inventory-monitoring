<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerBranch extends Model
{

    protected $guarded = [];
    public function customers()
    {
        return $this->belongsTo(Customer::class);
    }
}
