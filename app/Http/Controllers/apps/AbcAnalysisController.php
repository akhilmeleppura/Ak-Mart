<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\InventoryService;

class AbcAnalysisController extends Controller
{
    public function index(InventoryService $inventoryService)
    {
        $abcItems = $inventoryService->calculateAbcAnalysis();
        $deadStock = $inventoryService->getDeadStock(60);

        $totalTiedUpCapital = array_sum(array_column($deadStock, 'tied_up_capital'));
        $countClassA = count(array_filter($abcItems, fn($i) => $i['abc_category'] === 'A'));
        $countClassB = count(array_filter($abcItems, fn($i) => $i['abc_category'] === 'B'));
        $countClassC = count(array_filter($abcItems, fn($i) => $i['abc_category'] === 'C'));

        return view('content.apps.inventory.abc-analysis', compact(
            'abcItems',
            'deadStock',
            'totalTiedUpCapital',
            'countClassA',
            'countClassB',
            'countClassC'
        ));
    }
}
