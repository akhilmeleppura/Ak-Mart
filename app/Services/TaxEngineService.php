<?php

namespace App\Services;

use App\Models\Product;
use App\Models\TaxRule;
use App\Models\StoreSetting;

class TaxEngineService
{
    /**
     * Calculate comprehensive tax for a cart of items based on destination address & customer
     *
     * @param array $cart [productId => ['id', 'price', 'qty', ...]]
     * @param array|null $address ['country', 'state', 'zip', 'city']
     * @param mixed|null $customer
     * @return array
     */
    public function calculateCartTax(array $cart, ?array $address = null, $customer = null): array
    {
        $taxEnabled = (bool) StoreSetting::get('tax_enabled', true);
        if (!$taxEnabled) {
            return [
                'total_tax'      => 0.00,
                'tax_breakdown'  => [],
                'tax_rate_avg'   => 0.00,
                'is_inclusive'   => false,
                'tax_exempt'     => false,
            ];
        }

        // Check if customer is B2B tax exempt (e.g. verified VAT number)
        $isExempt = false;
        if ($customer && !empty($customer->tax_exempt)) {
            $isExempt = true;
        }

        if ($isExempt) {
            return [
                'total_tax'      => 0.00,
                'tax_breakdown'  => [['name' => 'B2B Tax Exemption', 'rate' => 0, 'amount' => 0.00]],
                'tax_rate_avg'   => 0.00,
                'is_inclusive'   => false,
                'tax_exempt'     => true,
            ];
        }

        $calcMode = StoreSetting::get('tax_calculation_mode', 'exclusive'); // exclusive or inclusive
        $defaultRate = (float) StoreSetting::get('vat_default_rate', 5.0); // 5% default VAT

        $country = strtoupper(trim($address['country'] ?? '*'));
        $state = strtoupper(trim($address['state'] ?? '*'));
        $zip = trim($address['zip'] ?? '');

        // Fetch active tax rules ordered by priority
        $activeRules = TaxRule::where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();

        $totalTax = 0.00;
        $breakdown = [];

        foreach ($cart as $item) {
            $price = (float) ($item['price'] ?? 0);
            $qty = (int) ($item['qty'] ?? 1);
            $lineTotal = $price * $qty;

            // Product specific tax class / rate override
            $product = isset($item['id']) ? Product::find($item['id']) : null;
            $taxClass = $item['tax_class'] ?? ($product->tax_class ?? 'standard');
            $customRate = $product ? $product->tax_rate : null;

            if ($taxClass === 'exempt') {
                $applicableRate = 0.00;
                $ruleName = 'Tax Exempt';
            } elseif ($customRate !== null) {
                // Product has fixed manual rate override
                $applicableRate = (float) $customRate;
                $ruleName = "Product Specific ({$taxClass})";
            } else {
                // Match area-based rules for this tax class
                $matchedRule = $this->matchAreaRule($activeRules, $taxClass, $country, $state, $zip);

                if ($matchedRule) {
                    $applicableRate = (float) $matchedRule->rate;
                    $ruleName = $matchedRule->name;
                } else {
                    // Fallback to global VAT rate
                    $applicableRate = ($taxClass === 'zero_rate') ? 0.00 : $defaultRate;
                    $ruleName = ($taxClass === 'zero_rate') ? 'Zero Rate (0%)' : 'Standard VAT / Sales Tax';
                }
            }

            // Calculate tax amount for this line
            if ($applicableRate > 0) {
                if ($calcMode === 'inclusive') {
                    // Price includes tax: Tax = Price - (Price / (1 + Rate/100))
                    $taxAmount = $lineTotal - ($lineTotal / (1 + ($applicableRate / 100)));
                } else {
                    // Price exclusive of tax: Tax = Price * (Rate / 100)
                    $taxAmount = ($lineTotal * $applicableRate) / 100;
                }
                $taxAmount = round($taxAmount, 2);
                $totalTax += $taxAmount;

                // Group by rule name in breakdown
                if (!isset($breakdown[$ruleName])) {
                    $breakdown[$ruleName] = [
                        'name'   => $ruleName,
                        'rate'   => $applicableRate,
                        'amount' => 0.00,
                    ];
                }
                $breakdown[$ruleName]['amount'] += $taxAmount;
            }
        }

        // Format breakdown list
        $breakdownList = array_values(array_map(function ($b) {
            $b['amount'] = round($b['amount'], 2);
            return $b;
        }, $breakdown));

        return [
            'total_tax'      => round($totalTax, 2),
            'tax_breakdown'  => $breakdownList,
            'is_inclusive'   => $calcMode === 'inclusive',
            'tax_exempt'     => false,
            'default_rate'   => $defaultRate,
        ];
    }

    /**
     * Match most specific area rule
     */
    protected function matchAreaRule($rules, $taxClass, $country, $state, $zip): ?TaxRule
    {
        // 1. Exact match with tax_class
        foreach ($rules as $r) {
            if ($r->tax_class === $taxClass) {
                $cMatch = ($r->country_code === '*' || $r->country_code === $country);
                $sMatch = ($r->state_name === '*' || strtoupper($r->state_name) === $state);
                $zMatch = empty($r->postal_code_pattern) || ($r->postal_code_pattern === '*') || str_starts_with($zip, rtrim($r->postal_code_pattern, '*'));

                if ($cMatch && $sMatch && $zMatch) {
                    return $r;
                }
            }
        }

        // 2. Fallback to standard tax class rules for this destination
        if ($taxClass !== 'zero_rate' && $taxClass !== 'exempt') {
            foreach ($rules as $r) {
                if ($r->tax_class === 'standard') {
                    $cMatch = ($r->country_code === '*' || $r->country_code === $country);
                    $sMatch = ($r->state_name === '*' || strtoupper($r->state_name) === $state);
                    $zMatch = empty($r->postal_code_pattern) || ($r->postal_code_pattern === '*') || str_starts_with($zip, rtrim($r->postal_code_pattern, '*'));

                    if ($cMatch && $sMatch && $zMatch) {
                        return $r;
                    }
                }
            }
        }

        return null;
    }
}
