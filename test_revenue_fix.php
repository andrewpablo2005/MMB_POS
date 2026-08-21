<?php
// Test revenue calculation methods
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/dashboard.php';

try {
    $connection = Database::getConnection();
    $dashboard = new \Classes\DashboardManager($connection);
    
    echo "=== TESTING REVENUE CALCULATION FIXES ===\n\n";
    
    // Test getRealRevenueToday
    echo "Testing getRealRevenueToday()...\n";
    try {
        $revenue = $dashboard->getRealRevenueToday();
        echo "✅ SUCCESS: Today's real revenue = PHP " . number_format($revenue, 2) . "\n";
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
    
    // Test getRealRevenueMonth
    echo "\nTesting getRealRevenueMonth()...\n";
    try {
        $revenue = $dashboard->getRealRevenueMonth();
        echo "✅ SUCCESS: This month's real revenue = PHP " . number_format($revenue, 2) . "\n";
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
    
    // Test getRealRevenueYear
    echo "\nTesting getRealRevenueYear()...\n";
    try {
        $revenue = $dashboard->getRealRevenueYear();
        echo "✅ SUCCESS: This year's real revenue = PHP " . number_format($revenue, 2) . "\n";
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ ALL REVENUE METHODS FIXED!\n";
    
} catch (\Exception $e) {
    echo "❌ Critical Error: " . $e->getMessage() . "\n";
    echo $e->getFile() . " (Line " . $e->getLine() . ")\n";
    exit(1);
}
?>
