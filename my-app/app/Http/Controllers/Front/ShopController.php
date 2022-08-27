<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductComment;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function show($id){

        //Get category
        $categories = ProductCategory::all();
        //Get Brand
        $brands = Brand::all();

        $product = Product::findOrFail($id);
        $avgRating = 0;
        $sumRating = array_sum(array_column($product->productComments->toArray(),'rating'));
        $countRating = count($product->productComments);
        if($countRating!=0){
            $avgRating = $sumRating/$countRating;
        }

        $relatedProducts = Product::where('product_category_id',$product->product_category_id)
            ->where('tag',$product->tag)
            ->limit(4)
            ->get();

        return view('front.shop.show',compact('product','avgRating','relatedProducts','categories','brands'));
    }

    public function postComment(Request $request){
        ProductComment::create($request->all());
        return redirect()->back();
    }

    public function index(Request $request){
        //Get category
        $categories = ProductCategory::all();
        //Get Brand
        $brands = Brand::all();

        //Get Product
        $perPage = $request->show ?? 3;
        $sortBy = $request->sort_by ?? 'latest';
        $search = $request->search ?? '';
        $products = Product::where('name','like','%'.$search.'%');

        $products = $this->filter($products,$request);
        $products = $this->sortAndPagination($sortBy,$products,$perPage);

        return view('front.shop.index',compact('products','categories','brands'));
    }

    public function category($categoryName,Request $request){
        //Get category
        $categories = ProductCategory::all();
        //Get Brand
        $brands = Brand::all();

        $perPage = $request->show ?? 3;
        $sortBy = $request->sort_by ?? 'latest';

        $products = ProductCategory::where('name',$categoryName)->first()->products->toQuery();
        $products = $this->filter($products,$request);

        $products = $this->sortAndPagination($sortBy,$products,$perPage,);

        return view('front.shop.index',compact('products','categories','brands'));
    }

    public function sortAndPagination($sortBy,$products,$perPage){
        switch ($sortBy){
            case 'latest' :
                $products = $products->orderBy('id');
                break;
            case 'oldest':
                $products = $products->orderByDesc('id');
                break;
            case 'name-asc':
                $products = $products->orderBy('name');
                break;
            case 'name-dsc':
                $products = $products->orderByDesc('name');
                break;
            case 'price-asc':
                $products = $products->orderBy('price');
                break;
            case 'price-dsc':
                $products = $products->orderByDesc('price');
                break;
            default :
        }
        $products = $products->paginate($perPage);
        $products->appends(['sort_by'=>$sortBy,'show'=>$perPage]);
        return $products;
    }

    public function filter($products,Request $request){
        //Brand
        $brands = $request->brand ?? [];
        $brand_ids = array_keys($brands);
        //Price
        $priceMin = $request->price_min;
        $priceMax = $request->price_max;
        $priceMin = str_replace('$','',$priceMin);
        $priceMax = str_replace('$','',$priceMax);
        $products = ($priceMax!=null && $priceMin!=null) ? $products->whereBetween('price',[$priceMin,$priceMax]) : $products;
        //Color
        $color = $request->color;
        $products = ($color!=null) ?
            $products->whereHas('productDetails',function ($query) use ($color){
                return $query->where('color',$color);
            })
            :
            $products;
        //Size
        $size = $request->size;
        $products = ($size!=null) ?
            $products->whereHas('productDetails',function ($query) use ($size){
                return $query->where('size',$size);
            })
            :
            $products;

        $products = $brand_ids !=null ? $products->whereIn('brand_id',$brand_ids) : $products;

        return $products;
    }
}
