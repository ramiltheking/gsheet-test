<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Показ стартового view 
     * 
     * @void
     * 
     * @return view('welcome')
     */
    public function index()
    {
        $products = Product::paginate(100);
        return view("welcome")->with("products", $products);
    }

    
    /**
     * Заполняет таблицу
     * 
     * @void
     * 
     * @return response()
     */
    public function fillTable()
    {
        Product::factory()->count(1000)->create();
        return response()->json([
            "success" => true,
        ]);
    }



    /**
     * Очищает таблицу
     * 
     * @void
     * 
     * @return response()
     */
    public function truncateDB()
    {
        Product::truncate();
        return response()->json([
            "success" => true,
        ]);
    }

}
