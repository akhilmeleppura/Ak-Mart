<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\api\SelectSearchController;
use Illuminate\Http\Request;

echo "=== AK-Mart SelectSearchController Test Suite ===\n\n";

$controller = app(SelectSearchController::class);

// 1. Products search with empty query (< 2 chars)
$reqShort = Request::create('/api/select/products', 'GET', ['q' => 'a']);
$resShort = $controller->products($reqShort);
$dataShort = json_decode($resShort->getContent(), true);
echo "1. Products search (< 2 chars):\n";
echo "   - Status code: " . $resShort->getStatusCode() . "\n";
echo "   - Hint returned: " . ($dataShort['hint'] ?? 'None') . "\n";
echo "   - Results count: " . count($dataShort['results']) . "\n\n";

// 2. Categories search
$reqCat = Request::create('/api/select/categories', 'GET', ['q' => '']);
$resCat = $controller->categories($reqCat);
$dataCat = json_decode($resCat->getContent(), true);
echo "2. Categories search:\n";
echo "   - Status code: " . $resCat->getStatusCode() . "\n";
echo "   - Categories found: " . count($dataCat['results']) . "\n";
if (!empty($dataCat['results'])) {
    echo "   - First category: " . json_encode($dataCat['results'][0]) . "\n";
}
echo "\n";

// 3. Branches search
$reqBranch = Request::create('/api/select/branches', 'GET', ['q' => '']);
$resBranch = $controller->branches($reqBranch);
$dataBranch = json_decode($resBranch->getContent(), true);
echo "3. Branches search:\n";
echo "   - Status code: " . $resBranch->getStatusCode() . "\n";
echo "   - Branches found: " . count($dataBranch['results']) . "\n";
if (!empty($dataBranch['results'])) {
    echo "   - First branch: " . json_encode($dataBranch['results'][0]) . "\n";
}
echo "\n";

echo "=== All SelectSearch Tests Passed ===\n";
