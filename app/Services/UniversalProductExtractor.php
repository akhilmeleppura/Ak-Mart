<?php

namespace App\Services;

use App\Services\AmazonProductExtractor;
use App\Services\Extractors\FlipkartProductExtractor;
use App\Services\Extractors\MeeshoProductExtractor;
use App\Services\Extractors\ShopifyProductExtractor;
use App\Services\Extractors\GenericEcommerceExtractor;

class UniversalProductExtractor
{
    protected AmazonProductExtractor $amazonExtractor;
    protected FlipkartProductExtractor $flipkartExtractor;
    protected MeeshoProductExtractor $meeshoExtractor;
    protected ShopifyProductExtractor $shopifyExtractor;
    protected GenericEcommerceExtractor $genericExtractor;

    public function __construct(
        AmazonProductExtractor $amazonExtractor,
        FlipkartProductExtractor $flipkartExtractor,
        MeeshoProductExtractor $meeshoExtractor,
        ShopifyProductExtractor $shopifyExtractor,
        GenericEcommerceExtractor $genericExtractor
    ) {
        $this->amazonExtractor = $amazonExtractor;
        $this->flipkartExtractor = $flipkartExtractor;
        $this->meeshoExtractor = $meeshoExtractor;
        $this->shopifyExtractor = $shopifyExtractor;
        $this->genericExtractor = $genericExtractor;
    }

    /**
     * Detect platform from URL.
     */
    public function detectPlatform(string $url, string $html = ''): string
    {
        if ($this->amazonExtractor->isAmazonUrl($url)) {
            return 'amazon';
        }
        if ($this->flipkartExtractor->isFlipkartUrl($url)) {
            return 'flipkart';
        }
        if ($this->meeshoExtractor->isMeeshoUrl($url)) {
            return 'meesho';
        }
        if ($this->shopifyExtractor->isShopifyUrl($url, $html)) {
            return 'shopify';
        }
        return 'generic';
    }

    /**
     * Normalize URL based on detected platform.
     */
    public function normalizeUrl(string $url): array
    {
        $platform = $this->detectPlatform($url);

        if ($platform === 'amazon') {
            $info = $this->amazonExtractor->normalizeUrl($url);
            $info['platform'] = 'amazon';
            return $info;
        }

        if ($platform === 'flipkart') {
            return $this->flipkartExtractor->normalizeUrl($url);
        }

        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? '');
        return [
            'original_url'  => $url,
            'canonical_url' => $url,
            'domain'        => $host,
            'asin'          => null,
            'platform'      => $platform,
        ];
    }

    /**
     * Main Universal Extraction Entry Point.
     */
    public function extract(string $html, string $url): array
    {
        $platform = $this->detectPlatform($url, $html);

        switch ($platform) {
            case 'amazon':
                $data = $this->amazonExtractor->extract($html, $url);
                $data['platform'] = 'amazon';
                return $data;

            case 'flipkart':
                $data = $this->flipkartExtractor->extract($html, $url);
                $data['platform'] = 'flipkart';
                return $data;

            case 'meesho':
                $data = $this->meeshoExtractor->extract($html, $url);
                $data['platform'] = 'meesho';
                return $data;

            case 'shopify':
                $data = $this->shopifyExtractor->extract($html, $url);
                $data['platform'] = 'shopify';
                return $data;

            default:
                $data = $this->genericExtractor->extract($html, $url);
                $data['platform'] = 'generic';
                return $data;
        }
    }
}
