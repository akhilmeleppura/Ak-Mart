<?php

namespace App\Services;

use App\Models\Product;

class ProductFeedService
{
    /**
     * Generate Google Shopping XML RSS 2.0 Feed
     */
    public function generateGoogleShoppingXml(): string
    {
        $products = Product::where('is_active', true)->with('category')->get();
        $storeUrl = config('app.url', 'http://localhost');

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"><channel></channel></rss>');
        $channel = $xml->channel;
        $channel->addChild('title', 'AK-Mart Product Catalog Feed');
        $channel->addChild('link', $storeUrl);
        $channel->addChild('description', 'Official Google Shopping Feed for AK-Mart');

        foreach ($products as $p) {
            $item = $channel->addChild('item');
            $item->addChild('g:id', (string)$p->id, 'http://base.google.com/ns/1.0');
            $item->addChild('g:title', htmlspecialchars($p->name), 'http://base.google.com/ns/1.0');
            $item->addChild('g:description', htmlspecialchars($p->description ?? $p->name), 'http://base.google.com/ns/1.0');
            $item->addChild('g:link', $storeUrl . '/products/' . $p->id, 'http://base.google.com/ns/1.0');
            $item->addChild('g:image_link', $storeUrl . '/' . ($p->image ?? 'assets/img/ecommerce-images/product-1.png'), 'http://base.google.com/ns/1.0');
            $item->addChild('g:availability', $p->qty > 0 ? 'in_stock' : 'out_of_stock', 'http://base.google.com/ns/1.0');
            $item->addChild('g:price', number_format($p->price, 2) . ' USD', 'http://base.google.com/ns/1.0');
            $item->addChild('g:brand', htmlspecialchars($p->brand ?? 'AK-Mart'), 'http://base.google.com/ns/1.0');
            $item->addChild('g:mpn', (string)($p->sku ?? 'AKM-' . $p->id), 'http://base.google.com/ns/1.0');
            if ($p->barcode) {
                $item->addChild('g:gtin', (string)$p->barcode, 'http://base.google.com/ns/1.0');
            }
        }

        return $xml->asXML();
    }

    /**
     * Generate Meta / Facebook Catalog CSV Feed
     */
    public function generateMetaCatalogCsv(): string
    {
        $products = Product::where('is_active', true)->with('category')->get();
        $storeUrl = config('app.url', 'http://localhost');

        $headers = ['id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand', 'google_product_category'];
        $rows = [];
        $rows[] = implode(',', $headers);

        foreach ($products as $p) {
            $row = [
                $p->id,
                '"' . str_replace('"', '""', $p->name) . '"',
                '"' . str_replace('"', '""', $p->description ?? $p->name) . '"',
                $p->qty > 0 ? 'in stock' : 'out of stock',
                'new',
                number_format($p->price, 2) . ' USD',
                $storeUrl . '/products/' . $p->id,
                $storeUrl . '/' . ($p->image ?? 'assets/img/ecommerce-images/product-1.png'),
                '"' . str_replace('"', '""', $p->brand ?? 'AK-Mart') . '"',
                '"' . str_replace('"', '""', $p->category?->name ?? 'General') . '"',
            ];
            $rows[] = implode(',', $row);
        }

        return implode("\n", $rows);
    }

    /**
     * Generate TikTok Product Catalog JSON Feed
     */
    public function generateTikTokCatalogJson(): string
    {
        $products = Product::where('is_active', true)->with('category')->get();
        $storeUrl = config('app.url', 'http://localhost');

        $items = [];
        foreach ($products as $p) {
            $items[] = [
                'sku_id'       => (string)$p->id,
                'title'        => $p->name,
                'description'  => $p->description ?? $p->name,
                'price'        => (float)$p->price,
                'currency'     => 'USD',
                'availability' => $p->qty > 0 ? 'IN_STOCK' : 'OUT_OF_STOCK',
                'product_url'  => $storeUrl . '/products/' . $p->id,
                'image_url'    => $storeUrl . '/' . ($p->image ?? 'assets/img/ecommerce-images/product-1.png'),
                'brand'        => $p->brand ?? 'AK-Mart',
                'category'     => $p->category?->name ?? 'General',
            ];
        }

        return json_encode(['products' => $items, 'generated_at' => now()->toIso8601String()], JSON_PRETTY_PRINT);
    }
}
