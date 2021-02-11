<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];
    public function cbranches()
    {
        return $this->hasMany(CustomerBranch::class);
    }
}
