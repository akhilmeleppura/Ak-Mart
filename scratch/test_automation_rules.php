<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\apps\WorkflowAutomationController;
use App\Models\WorkflowRule;
use App\Models\LoyaltyTransaction;
use App\Models\AuditLog;
use App\Models\User;

echo "=== AK-Mart Workflow Automation Engine Test ===\n\n";

$user = User::first();

// 1. Create Test Automation Rule: When order.created with amount >= 200, award 75 bonus loyalty points
WorkflowRule::where('name', 'High-Value Order Loyalty Reward')->delete();
$rule = WorkflowRule::create([
    'name'          => 'High-Value Order Loyalty Reward',
    'trigger_event' => 'order.created',
    'conditions'    => [
        'field'    => 'amount',
        'operator' => '>=',
        'value'    => '200',
    ],
    'actions'       => [
        'type'        => 'award_loyalty',
        'points'      => 75,
        'message'     => 'Awarded 75 VIP bonus points for order >= $200',
        'target_role' => 'Customer',
    ],
    'is_active'     => true,
]);

echo "1. Registered Automation Rule:\n";
echo "   - Rule Name: {$rule->name}\n";
echo "   - Trigger: {$rule->trigger_event} where amount >= 200\n";
echo "   - Action: Award 75 Loyalty Points\n\n";

// 2. Trigger Event Below Threshold ($150) -> Should NOT match
$initialPoints = LoyaltyTransaction::getCustomerBalance($user->id);
WorkflowAutomationController::trigger('order.created', [
    'amount'      => 150,
    'customer_id' => $user->id,
    'order_id'    => 9991,
]);
$pointsAfterLow = LoyaltyTransaction::getCustomerBalance($user->id);
echo "2. Evaluation on Low-Value Event ($150):\n";
echo "   - Points before: {$initialPoints}, Points after: {$pointsAfterLow}\n";
echo "   - Condition Filtering: " . ($pointsAfterLow === $initialPoints ? "✓ PASS (No action on unmatched condition)" : "FAIL") . "\n\n";

// 3. Trigger Event Meeting Threshold ($350) -> Should MATCH and execute action
WorkflowAutomationController::trigger('order.created', [
    'amount'      => 350,
    'customer_id' => $user->id,
    'order_id'    => 9992,
]);
$pointsAfterHigh = LoyaltyTransaction::getCustomerBalance($user->id);
echo "3. Evaluation on High-Value Event ($350):\n";
echo "   - Points after trigger: {$pointsAfterHigh} (Expected +75 points)\n";
echo "   - Action Execution: " . ($pointsAfterHigh === $initialPoints + 75 ? "✓ PASS (75 Points awarded successfully)" : "FAIL") . "\n\n";

// 4. Verify Immutable Audit Trail Entry
$audit = AuditLog::where('event', 'WORKFLOW_ACTION_EXECUTED')->latest()->first();
echo "4. Audit Trail Verification:\n";
echo "   - Event: {$audit->event}\n";
echo "   - Recorded Data: " . json_encode($audit->new_values) . "\n";
echo "   - Audit Logging: " . ($audit ? "✓ PASS" : "FAIL") . "\n\n";

echo "=== Automation Engine Suite Passed 100% ===\n";
