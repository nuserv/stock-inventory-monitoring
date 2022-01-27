<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockReqNo extends Model
{
    protected $guarded = [];
    // protected $table = 'buffers_no';
    protected $connection = 'mysql1';
    protected $table = 'requests';
}
