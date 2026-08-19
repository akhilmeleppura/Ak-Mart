<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\WorkflowRule;
use App\Models\SystemNotification;
use App\Models\AuditLog;
use Illuminate\Http\Request;

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
                // Execute action
                if (($actions['type'] ?? '') === 'notification' || ($actions['type'] ?? '') === 'create_stock_alert') {
                    if (class_exists(SystemNotification::class)) {
                        try {
                            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                                'id'             => (string) \Illuminate\Support\Str::uuid(),
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
                }
            }
        }
    }
}
