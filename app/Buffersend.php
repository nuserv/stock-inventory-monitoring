<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buffersend extends Model
{
    protected $guarded = [];
    protected $connection = 'mysql1';
    protected $table = 'stocks';
}
