<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function products(){
        /*Quan hệ 1-nhiều */
        return $this->hasMany(Product::class,'brand_id','id');


    }
}
