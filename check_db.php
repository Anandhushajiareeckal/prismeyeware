<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Customer;

echo "Checking databases...\n";
$databases = DB::select("SHOW DATABASES");
foreach ($databases as $db) {
    echo "- " . $db->Database . "\n";
}

$targetDbs = ['prism_eyeware', 'prismeyeware'];
foreach ($targetDbs as $dbName) {
    try {
        echo "\nChecking database: $dbName\n";
        Config::set('database.connections.mysql.database', $dbName);
        DB::purge('mysql');
        DB::reconnect('mysql');
        
        $rows = DB::select("SELECT id, customer_number, deleted_at FROM customers WHERE customer_number = '00126'");
        echo "Found " . count($rows) . " records for '00126'\n";
        if (count($rows) > 0) print_r($rows);
        
        $maxNum = DB::table('customers')->pluck('customer_number')->filter(fn($n) => is_numeric($n))->max();
        echo "Max customer number: $maxNum\n";
    } catch (\Exception $e) {
        echo "Error checking $dbName: " . $e->getMessage() . "\n";
    }
}
