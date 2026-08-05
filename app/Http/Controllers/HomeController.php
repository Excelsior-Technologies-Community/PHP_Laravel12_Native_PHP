<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::oldest()->take(6)->get();

        $totalProducts = Product::count();

        $activeProducts = Product::where('status', 'Active')->count();

        $inactiveProducts = Product::where('status', 'Inactive')->count();

        $totalStock = Product::sum('quantity');

        $categories = Product::distinct('category')->count('category');

        return view('home', compact(

            'products',
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'totalStock',
            'categories'

        ));
    }
}
