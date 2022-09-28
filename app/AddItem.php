<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddItem extends Model
{
    protected $guarded = [];
    // protected $table = 'buffers_no';
    protected $connection = 'mysql1';
    // protected $table = 'items_clone';
    protected $table = 'items';
}