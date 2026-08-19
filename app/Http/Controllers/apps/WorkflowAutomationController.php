<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\WorkflowRule;
use App\Models\SystemNotification;
use App\Models\LoyaltyTransaction;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\CommunicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkflowAutomationController extends Controller
{
    public function index()
    {
        $rules = WorkflowRule::latest()->get();
        return view('content.apps.automation.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'trigger_event' => 'required|string',
            'action_type'   => 'required|string',
        ]);

        $conditions = [
            'field'    => $request->input('condition_field', 'amount'),
            'operator' => $request->input('condition_operator', '>='),
            'value'    => $request->input('condition_value', '100'),
        ];

        $actions = [
            'type'        => $request->input('action_type'),
            'message'     => $request->input('action_message', 'Automated workflow rule triggered.'),
            'target_role' => $request->input('target_role', 'Super Admin'),
            'points'      => (int) $request->input('action_points', 50),
        ];

        WorkflowRule::create([
            'name'          => $request->name,
            'trigger_event' => $request->trigger_event,
            'conditions'    => $conditions,
            'actions'       => $actions,
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Workflow automation rule created successfully.');
    }

    public function toggle($id)
    {
        $rule = WorkflowRule::findOrFail($id);
        $rule->is_active = !$rule->is_active;
        $rule->save();

        return redirect()->back()->with('success', "Rule '{$rule->name}' status updated.");
    }

    public function destroy($id)
    {
        $rule = WorkflowRule::findOrFail($id);
        $rule->delete();

        return redirect()->back()->with('success', 'Rule deleted.');
    }

    /**
     * Dispatch and evaluate automated rules.
     */
    public static function trigger(string $eventName, array $context = [])
    {
        $rules = WorkflowRule::where('trigger_event', $eventName)->where('is_active', true)->get();

        foreach ($rules as $rule) {
            $conditions = $rule->conditions ?? [];
            $actions = $rule->actions ?? [];

            $match = true;

            if (!empty($conditions['field']) && isset($context[$conditions['field']])) {
                $val = $context[$conditions['field']];
                $targetVal = $conditions['value'];
                $op = $conditions['operator'] ?? '==';

                if ($op === '>=' && !($val >= $targetVal)) $match = false;
                elseif ($op === '<=' && !($val <= $targetVal)) $match = false;
                elseif ($op === '>' && !($val > $targetVal)) $match = false;
                elseif ($op === '<' && !($val < $targetVal)) $match = false;
                elseif ($op === '==' && !($val == $targetVal)) $match = false;
            }

            if ($match) {
                $actionType = $actions['type'] ?? 'notification';

                // Action 1: In-App System Notification
                if ($actionType === 'notification' || $actionType === 'create_stock_alert') {
                    try {
                        DB::table('notifications')->insert([
                            'id'             => (string) Str::uuid(),
                            'type'           => 'App\Notifications\WorkflowAlert',
                            'notifiable_type'=> 'App\Models\User',
                            'notifiable_id'  => auth()->id() ?? 1,
                            'data'           => json_encode([
                                'title'   => "Workflow: {$rule->name}",
                                'message' => $actions['message'] ?? 'Automated condition met.',
                                'time'    => now()->diffForHumans(),
                            ]),
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    } catch (\Exception $e) {}
                }

                // Action 2: Award Loyalty Points
                if ($actionType === 'award_loyalty' && isset($context['customer_id'])) {
                    $pts = (int) ($actions['points'] ?? 50);
                    LoyaltyTransaction::recordPoints(
                        $context['customer_id'],
                        $pts,
                        'earned',
                        $context['order_id'] ?? null,
                        "Automation Reward: {$rule->name}"
                    );
                }

                // Action 3: Log to Immutable Audit Trail
                AuditLog::create([
                    'user_id'        => auth()->id() ?? 1,
                    'event'          => 'WORKFLOW_ACTION_EXECUTED',
                    'auditable_type' => WorkflowRule::class,
                    'auditable_id'   => $rule->id,
                    'new_values'     => [
                        'rule'    => $rule->name,
                        'event'   => $eventName,
                        'action'  => $actionType,
                        'context' => $context,
                    ],
                    'ip_address'     => request()?->ip() ?: '127.0.0.1',
                    'user_agent'     => 'AK-Mart Workflow Engine/1.0',
                ]);
            }
        }
    }
}
