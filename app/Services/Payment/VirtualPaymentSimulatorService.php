<?php

namespace App\Services\Payment;

use Illuminate\Support\Str;

class VirtualPaymentSimulatorService
{
    /**
     * Check if the virtual payment sandbox is enabled.
     */
    public static function isEnabled(): bool
    {
        return config('services.virtual_payment.enabled', true);
    }

    /**
     * Generate a realistic virtual credit/debit card.
     */
    public static function generateVirtualCard(string $brand = 'visa'): array
    {
        $brand = strtolower($brand);
        $binPrefixes = [
            'visa'       => '4242',
            'mastercard' => '5200',
            'rupay'      => '6075',
            'amex'       => '3782',
        ];

        $prefix = $binPrefixes[$brand] ?? '4242';
        $middle = sprintf('%04d%04d', mt_rand(1000, 9999), mt_rand(1000, 9999));
        $partial = $prefix . $middle . sprintf('%03d', mt_rand(100, 999));
        
        // Calculate Luhn Check Digit
        $checkDigit = self::calculateLuhnCheckDigit($partial);
        $fullCardNumber = $partial . $checkDigit;
        $formattedCardNumber = implode(' ', str_split($fullCardNumber, 4));

        $expMonth = sprintf('%02d', mt_rand(1, 12));
        $expYear = date('y') + mt_rand(2, 5);
        $cvv = sprintf('%03d', mt_rand(100, 999));

        $holderNames = [
            'Alex Morgan', 'David Miller', 'Sarah Jenkins', 'Akhil Meleppura', 
            'Priya Sharma', 'Michael Chang', 'Elena Rostova', 'Johnathan Smith'
        ];
        $holderName = $holderNames[array_rand($holderNames)];

        return [
            'brand'          => strtoupper($brand),
            'card_number'    => $formattedCardNumber,
            'raw_number'     => $fullCardNumber,
            'holder_name'    => $holderName,
            'expiry'         => "{$expMonth}/{$expYear}",
            'expiry_month'   => $expMonth,
            'expiry_year'    => $expYear,
            'cvv'            => $cvv,
            'virtual_limit'  => 5000.00,
            'is_virtual'     => true,
            'generated_at'   => now()->toDateTimeString(),
        ];
    }

    /**
     * Generate a realistic Virtual UPI ID & Virtual Mobile Number.
     */
    public static function generateVirtualUpi(): array
    {
        $virtualMobile = '+1 (555) ' . sprintf('%03d-%04d', mt_rand(100, 999), mt_rand(1000, 9999));
        $upiHandles = ['akmart', 'okaxis', 'okhdfcbank', 'paytm', 'ybl'];
        $userPrefix = 'tester_' . mt_rand(1000, 9999);
        $vpa = $userPrefix . '@' . $upiHandles[array_rand($upiHandles)];

        return [
            'upi_id'          => $vpa,
            'virtual_phone'   => $virtualMobile,
            'bank_name'       => 'AK-Mart Sandbox Virtual Bank',
            'is_verified_vpa' => true,
        ];
    }

    /**
     * Generate dynamic UPI Deep-Link URI for QR code generators.
     */
    public static function generateUpiUri(float $amount, string $orderNumber = 'TEST_ORDER'): string
    {
        $vpa = 'akmart@icici';
        $payeeName = urlencode('AK-Mart Supermarket');
        $note = urlencode("Order_{$orderNumber}");
        
        return "upi://pay?pa={$vpa}&pn={$payeeName}&am={$amount}&cu=USD&tn={$note}";
    }

    /**
     * Validate simulated 3D-Secure Bank OTP.
     */
    public static function verifyOtp(string $otp): bool
    {
        $cleaned = trim($otp);
        // Default universal test OTP is 123456 or matching session
        return in_array($cleaned, ['123456', '000000', session('sandbox_test_otp')]);
    }

    /**
     * Compute Luhn check digit for card verification realism.
     */
    protected static function calculateLuhnCheckDigit(string $number): int
    {
        $sum = 0;
        $alt = true;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $n = intval($number[$i]);
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alt = !$alt;
        }
        return (10 - ($sum % 10)) % 10;
    }
}
