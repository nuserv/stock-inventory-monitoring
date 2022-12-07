<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cdm extends Model
{
    protected $guarded = [];
    protected $connection = 'cdm';
    protected $table = 'cdr';
}
