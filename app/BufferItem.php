<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BufferItem extends Model
{
    protected $guarded = [];
    protected $connection = 'mysql1';
    protected $table = 'stock_request';
}
