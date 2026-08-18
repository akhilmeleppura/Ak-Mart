<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosRegisterSession;
use App\Services\FinanceService;

class PosRegisterController extends Controller
{
    public function index()
    {
        $sessions = PosRegisterSession::with('user')->latest()->paginate(20);
        $activeSession = PosRegisterSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        return view('content.apps.finance.pos-register', compact('sessions', 'activeSession'));
    }

    public function open(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $active = PosRegisterSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($active) {
            return back()->with('error', 'You already have an open POS register session!');
        }

        $session = PosRegisterSession::create([
            'branch_id'      => session('branch_id', 1),
            'user_id'        => auth()->id(),
            'opening_amount' => $request->opening_amount,
            'status'         => 'open',
            'opened_at'      => now(),
        ]);

        return redirect()->route('app-pos-register')->with('success', 'POS register shift opened successfully!');
    }

    public function close(Request $request, FinanceService $financeService)
    {
        $request->validate([
            'session_id'     => 'required|exists:pos_register_sessions,id',
            'closing_amount' => 'required|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        $session = $financeService->closeRegister(
            $request->session_id,
            (float)$request->closing_amount,
            $request->notes
        );

        return redirect()->route('app-pos-register')->with('success', "POS register shift closed! Difference: \${$session->difference}");
    }
}
