<?php

namespace App\Services\Ai;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderTransaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RiskIntelligenceService
{
    /**
     * 1. Multi-Factor Explainable Order Risk Assessment
     */
    public function assessOrderRisk(Order $order): array
    {
        $score = 0;
        $signals = [];
        $user = $order->customer;

        // Signal 1: High Order Value vs Customer Historical AOV
        if ($user) {
            $pastOrders = Order::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->where('id', '!=', $order->id)
                ->where('payment_status', 'paid')
                ->get();

            $orderCount = $pastOrders->count();
            if ($orderCount >= 2) {
                $avgSpend = $pastOrders->avg('total_amount');
                if ($avgSpend > 0 && $order->total_amount >= ($avgSpend * 3)) {
                    $score += 30;
                    $ratio = round($order->total_amount / $avgSpend, 1);
                    $signals[] = "Order total (\${$order->total_amount}) is {$ratio}× the customer's historical average (\${$avgSpend}).";
                }
            }
        }

        // Signal 2: Payment Failures / Retries
        if ($order->payment_status === 'failed' || $order->payment_status === 'rejected') {
            $score += 25;
            $signals[] = "Payment status is currently recorded as failed/rejected.";
        }

        // Signal 3: COD Refusal / Cancellation History
        if ($order->payment_method === 'cod' || $order->is_pickup) {
            $codRisk = $this->assessCodRisk($user);
            if ($codRisk['risk_level'] === 'High') {
                $score += 30;
                $signals[] = "Customer has elevated COD return/cancellation history ({$codRisk['cancelled_cod']} cancellations).";
            }
        }

        // Signal 4: Elevated Return Frequency
        if ($user) {
            $returnsCount = OrderReturn::withoutGlobalScopes()->where('user_id', $user->id)->count();
            $totalUserOrders = Order::withoutGlobalScopes()->where('user_id', $user->id)->count();
            if ($totalUserOrders > 0 && ($returnsCount / $totalUserOrders) > 0.40) {
                $score += 20;
                $returnPct = round(($returnsCount / $totalUserOrders) * 100);
                $signals[] = "Customer return rate ({$returnPct}%) exceeds standard baseline.";
            }
        }

        $riskScore = min(100, $score);

        $riskLevel = match (true) {
            $riskScore >= 80 => 'Critical',
            $riskScore >= 60 => 'High',
            $riskScore >= 30 => 'Medium',
            default          => 'Low',
        };

        $confidence = $user && Order::withoutGlobalScopes()->where('user_id', $user->id)->count() >= 3 ? 'High' : 'Medium';

        $recommendedAction = match ($riskLevel) {
            'Critical', 'High' => 'Hold for Manual Verification',
            'Medium'           => 'Standard Review',
            default            => 'Auto-Approve',
        };

        if (empty($signals)) {
            $signals[] = 'Order exhibits normal transaction metrics with no risk anomalies.';
        }

        return [
            'order_id'           => $order->id,
            'order_number'       => $order->order_number,
            'risk_score'         => $riskScore,
            'risk_level'         => $riskLevel,
            'confidence'         => $confidence,
            'signals'            => $signals,
            'recommended_action' => $recommendedAction,
            'evaluated_at'       => Carbon::now()->toDateTimeString(),
        ];
    }

    /**
     * 2. Cash-on-Delivery (COD) Risk Assessment
     */
    public function assessCodRisk(?User $user): array
    {
        if (!$user) {
            return [
                'risk_level'         => 'Low',
                'cancelled_cod'      => 0,
                'recommended_policy' => 'Allow COD',
                'explanation'        => 'Guest or first-time buyer eligible for standard COD.',
            ];
        }

        $codOrders = Order::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('payment_method', 'cod')
            ->get();

        $totalCod = $codOrders->count();
        $cancelledCod = $codOrders->whereIn('order_status', ['cancelled', 'failed'])->count();

        if ($totalCod >= 3 && ($cancelledCod / $totalCod) >= 0.50) {
            return [
                'risk_level'         => 'High',
                'cancelled_cod'      => $cancelledCod,
                'recommended_policy' => 'Require Prepaid Payment',
                'explanation'        => "Customer has {$cancelledCod} cancelled/rejected COD orders out of {$totalCod}.",
            ];
        }

        return [
            'risk_level'         => 'Low',
            'cancelled_cod'      => $cancelledCod,
            'recommended_policy' => 'Allow COD',
            'explanation'        => 'Customer maintains an acceptable COD delivery success rate.',
        ];
    }

    /**
     * 3. Payment Gateway Anomaly Detection
     */
    public function detectPaymentAnomalies(): array
    {
        $since24h = Carbon::now()->subHours(24);

        $totalTx = Order::withoutGlobalScopes()->where('created_at', '>=', $since24h)->count();
        $failedTx = Order::withoutGlobalScopes()->where('created_at', '>=', $since24h)
            ->whereIn('payment_status', ['failed', 'rejected'])
            ->count();

        $failureRate = $totalTx > 0 ? round(($failedTx / $totalTx) * 100, 1) : 0;

        $hasAnomaly = $totalTx >= 10 && $failureRate >= 25.0;

        return [
            'total_transactions_24h' => $totalTx,
            'failed_transactions_24h'=> $failedTx,
            'failure_rate_pct'       => $failureRate,
            'anomaly_detected'       => $hasAnomaly,
            'status'                 => $hasAnomaly ? 'Gateway Failure Spike Detected' : 'Payment Gateways Healthy',
        ];
    }

    /**
     * 4. Coupon & Promotion Abuse Detection
     */
    public function detectCouponAbuse(string $couponCode): array
    {
        $since7d = Carbon::now()->subDays(7);
        $recentUses = Order::withoutGlobalScopes()
            ->where('coupon_code', $couponCode)
            ->where('created_at', '>=', $since7d)
            ->count();

        $isAbuseRisk = $recentUses >= 50;

        return [
            'coupon_code'      => $couponCode,
            'uses_last_7_days' => $recentUses,
            'abuse_risk'       => $isAbuseRisk ? 'High Velocity Abuse Detected' : 'Normal Promotion Usage',
            'action'           => $isAbuseRisk ? 'Review coupon redemption clusters' : 'No action required',
        ];
    }

    /**
     * 5. Risk Review Queue
     */
    public function getRiskReviewQueue(int $limit = 20): Collection
    {
        return Order::withoutGlobalScopes()
            ->where('order_status', 'pending')
            ->where('total_amount', '>=', 500)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}
