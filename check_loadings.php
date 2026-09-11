<?php

use App\Models\Loading;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Loading Manifests...\n";

try {
    $count = Loading::count();
    echo "Total Loadings in DB: " . $count . "\n";

    if ($count > 0) {
        $loadings = Loading::with(['truck', 'route'])->get();
        foreach ($loadings as $loading) {
            echo "ID: " . $loading->id . " | Number: " . $loading->load_number . " | Status: " . $loading->status . "\n";
            echo "  Truck: " . ($loading->truck ? $loading->truck->licence_plate_no : 'NULL') . "\n";
            echo "  Route: " . ($loading->route ? $loading->route->route_code : 'NULL') . "\n";
        }
    } else {
        // Check raw SQL to be absolutely sure
        $rawCount = DB::table('loadings')->count();
        echo "Raw DB Count: " . $rawCount . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
