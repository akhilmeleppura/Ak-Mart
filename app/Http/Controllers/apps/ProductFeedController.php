<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductFeedService;

class ProductFeedController extends Controller
{
    public function index()
    {
        return view('content.apps.marketing.product-feeds');
    }

    public function googleXml(ProductFeedService $feedService)
    {
        $xml = $feedService->generateGoogleShoppingXml();
        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function metaCsv(ProductFeedService $feedService)
    {
        $csv = $feedService->generateMetaCatalogCsv();
        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="meta_product_catalog.csv"',
        ]);
    }

    public function tikTokJson(ProductFeedService $feedService)
    {
        $json = $feedService->generateTikTokCatalogJson();
        return response($json, 200, ['Content-Type' => 'application/json']);
    }
}
