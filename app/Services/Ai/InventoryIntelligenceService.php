<?php

namespace App\Services\Ai;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryIntelligenceService
{
    /**
     * 1. Multi-Horizon Demand Forecasting
     */
    public function calculateDemandForecast(Product $product, int $days = 30): array
    {
        // 60-day historical window for velocity baseline
        $since = Carbon::now()->subDays(60);
        $totalSold60d = (int)OrderItem::where('product_id', $product->id)
            ->whereHas('order', fn($q) => $q->where('payment_status', 'paid')->where('created_at', '>=', $since))
            ->sum('qty');

        $orderCount60d = OrderItem::where('product_id', $product->id)
            ->whereHas('order', fn($q) => $q->where('payment_status', 'paid')->where('created_at', '>=', $since))
            ->count();

        $dailyVelocity = round($totalSold60d / 60, 2);

        $forecast7d = (int)ceil($dailyVelocity * 7);
        $forecast14d = (int)ceil($dailyVelocity * 14);
        $forecast30d = (int)ceil($dailyVelocity * 30);
        $forecast60d = (int)ceil($dailyVelocity * 60);
        $forecast90d = (int)ceil($dailyVelocity * 90);

        $confidence = match (true) {
            $orderCount60d >= 15 => 'High',
            $orderCount60d >= 5  => 'Medium',
            default              => 'Low / Insufficient Data',
        };

        return [
            'product_id'        => $product->id,
            'sku'               => $product->sku,
            'daily_velocity'    => $dailyVelocity,
            'forecast_7d'       => $forecast7d,
            'forecast_14d'      => $forecast14d,
            'forecast_30d'      => $forecast30d,
            'forecast_60d'      => $forecast60d,
            'forecast_90d'      => $forecast90d,
            'target_forecast'   => (int)ceil($dailyVelocity * $days),
            'confidence'        => $confidence,
            'historical_sold'   => $totalSold60d,
        ];
    }

    /**
     * 2. Stockout Prediction & Dynamic Reorder Point
     */
    public function predictStockoutRunway(Product $product, int $supplierLeadDays = 7): array
    {
        $forecast = $this->calculateDemandForecast($product, 30);
        $dailyVelocity = max(0.05, $forecast['daily_velocity']);
        $currentStock = $product->qty;

        $runwayDays = (int)floor($currentStock / $dailyVelocity);

        // Safety Stock = 3 days of average demand
        $safetyStock = (int)ceil($dailyVelocity * 3);

        // Reorder Point = Demand during supplier lead time + Safety Stock
        $reorderPoint = (int)ceil(($dailyVelocity * $supplierLeadDays) + $safetyStock);

        $riskLevel = match (true) {
            $runwayDays <= 3 => 'Critical',
            $runwayDays <= 7 => 'High',
            $runwayDays <= 14 => 'Medium',
            default           => 'Low',
        };

        $needsReorder = $currentStock <= $reorderPoint;
        $recommendedQty = $needsReorder ? max(20, (int)ceil($dailyVelocity * 30)) : 0;

        return [
            'product_id'       => $product->id,
            'sku'              => $product->sku,
            'current_stock'    => $currentStock,
            'runway_days'      => $runwayDays,
            'risk_level'       => $riskLevel,
            'reorder_point'    => $reorderPoint,
            'safety_stock'     => $safetyStock,
            'needs_reorder'    => $needsReorder,
            'recommended_qty'  => $recommendedQty,
            'supplier_lead_days' => $supplierLeadDays,
        ];
    }

    /**
     * 3. Purchase Order Draft Generator
     */
    public function generatePurchaseOrderDraft(Product $product, int $recommendedQty = 50): array
    {
        $supplier = Supplier::first();
        $supplierName = $supplier?->name ?? 'Default Tier-1 Supplier';

        return [
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'sku'             => $product->sku,
            'supplier_name'   => $supplierName,
            'order_quantity'  => $recommendedQty,
            'estimated_cost'  => '$' . number_format($product->cost_price ? $product->cost_price * $recommendedQty : $product->price * 0.6 * $recommendedQty, 2),
            'status'          => 'draft_pending_manager_approval',
            'created_at'      => Carbon::now()->toDateTimeString(),
        ];
    }

    /**
     * 4. Dead Stock and Overstock Detection
     */
    public function detectDeadAndOverstock(Product $product): array
    {
        $since90d = Carbon::now()->subDays(90);
        $sold90d = OrderItem::where('product_id', $product->id)
            ->whereHas('order', fn($q) => $q->where('payment_status', 'paid')->where('created_at', '>=', $since90d))
            ->sum('qty');

        $forecast = $this->calculateDemandForecast($product, 30);
        $velocity = $forecast['daily_velocity'];

        if ($sold90d == 0 && $product->qty > 0) {
            return [
                'classification' => 'Dead Stock',
                'explanation'    => 'Zero sales recorded in the last 90 days with positive inventory.',
                'action'         => 'Consider bundle discount, liquidation, or promotional clearance.',
            ];
        }

        if ($velocity > 0 && ($product->qty / $velocity) > 180) {
            return [
                'classification' => 'Overstock',
                'explanation'    => 'Current inventory exceeds 180 days of projected demand.',
                'action'         => 'Pause incoming purchase orders and launch promotional campaigns.',
            ];
        }

        return [
            'classification' => 'Healthy Velocity',
            'explanation'    => 'Inventory levels align with expected demand velocity.',
            'action'         => 'Maintain standard reorder policy.',
        ];
    }

    /**
     * 5. Multi-Branch Stock Rebalancing Transfer Recommendation
     */
    public function recommendBranchStockTransfer(Product $product, int $surplusBranchId, int $deficitBranchId, int $qty): array
    {
        return [
            'product_id'         => $product->id,
            'sku'                => $product->sku,
            'source_branch_id'   => $surplusBranchId,
            'target_branch_id'   => $deficitBranchId,
            'recommended_qty'    => $qty,
            'reason'             => "Rebalancing inventory: Source branch has surplus stock while target branch has high stockout risk.",
            'status'             => 'draft_pending_manager_approval',
        ];
    }

    /**
     * 6. Stock Movement Anomaly Detection
     */
    public function detectStockMovementAnomalies(int $thresholdUnits = 50): array
    {
        $anomalies = [];

        $largeMovements = StockMovement::where('type', 'adjustment')
            ->whereRaw('ABS(quantity) >= ?', [$thresholdUnits])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->take(10)
            ->get();

        foreach ($largeMovements as $mov) {
            $anomalies[] = [
                'movement_id'   => $mov->id,
                'product_id'    => $mov->product_id,
                'quantity'      => $mov->quantity,
                'type'          => $mov->type,
                'reason'        => $mov->notes ?: 'Manual Stock Adjustment',
                'detected_at'   => $mov->created_at->toDateTimeString(),
                'severity'      => abs($mov->quantity) >= 100 ? 'High' : 'Medium',
            ];
        }

        return [
            'anomalies_count' => count($anomalies),
            'anomalies'       => $anomalies,
        ];
    }

    /**
     * 7. Cycle Count Prioritization Candidates
     */
    public function prioritizeCycleCountCandidates(int $limit = 10): Collection
    {
        return Product::where('is_active', true)
            ->where('qty', '>', 0)
            ->select('*', DB::raw('(qty * price) as asset_value'))
            ->orderByDesc('asset_value')
            ->take($limit)
            ->get();
    }
}
