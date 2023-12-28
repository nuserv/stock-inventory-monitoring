<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Defective extends Model
{
    protected $guarded = [];
    public function items(){
        return $this->belongsTo(Item::class,'items_id','id');
    }
    public function categories(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function branches(){
        return $this->belongsTo(Branch::class,'branch_id','id');
    }
}
