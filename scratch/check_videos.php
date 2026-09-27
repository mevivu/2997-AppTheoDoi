<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \Illuminate\Support\Facades\DB::table('videos')->get();
echo "Count videos: " . $rows->count() . "\n";
foreach ($rows as $r) {
    echo "ID: {$r->id} | title: {$r->title} | access_type: {$r->access_type} | is_preview: {$r->is_preview}\n";
}





