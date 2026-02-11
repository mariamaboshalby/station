<?php

use App\Models\Shift;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$shiftId = 3;
$shift = Shift::with('nozzleReadings')->find($shiftId);

if (!$shift) {
    echo "Shift #$shiftId not found.\n";
    exit;
}

echo "Shift #$shiftId found.\n";
echo "Nozzle Readings Count: " . $shift->nozzleReadings->count() . "\n";

foreach ($shift->nozzleReadings as $reading) {
    echo " - Reading ID: {$reading->id}, Nozzle ID: {$reading->nozzle_id}, Start Reading: {$reading->start_reading}\n";
}
