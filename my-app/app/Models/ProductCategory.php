<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $table = 'product_categories';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function products(){
        /*Quan hệ 1-nhiều */
        return $this->hasMany(Product::class,'product_category_id','id');


    }


}
