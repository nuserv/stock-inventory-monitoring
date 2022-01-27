<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockReq extends Model
{
    protected $guarded = [];
    // protected $table = 'buffers_no';
    protected $connection = 'mysql1';
    protected $table = 'stock_request';
}
