<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    /**
     * Generate dynamic sitemap.xml
     */
    public function sitemap()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        $content = view('content.apps.saas.sitemap', compact('products', 'categories'));

        return Response::make($content, 200, ['Content-Type' => 'text/xml']);
    }

    /**
     * SEO Dashboard for Super Admin.
     */
    public function index()
    {
        $productCount = Product::count();
        $missingMeta = Product::whereNull('meta_description')->count();
        
        return view('content.apps.saas.seo-dashboard', compact('productCount', 'missingMeta'));
    }
}
