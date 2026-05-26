<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = DB::select('SELECT id, product_id, qty, free_qty, netprice, retail_price FROM batch__stocks');

foreach ($rows as $r) {
    $val = ($r->qty + $r->free_qty) * $r->netprice;
    echo "ID:{$r->id} pid:{$r->product_id} qty:{$r->qty} free:{$r->free_qty} net:{$r->netprice} retail:{$r->retail_price} val:{$val}\n";
}

echo "\nTotal (net): " . array_sum(array_map(fn($r) => ($r->qty + $r->free_qty) * $r->netprice, $rows)) . "\n";
echo "Total (retail): " . array_sum(array_map(fn($r) => ($r->qty + $r->free_qty) * $r->retail_price, $rows)) . "\n";
