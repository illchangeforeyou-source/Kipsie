<?php
// Test to verify sidebar HTML structure is correct
echo "=== Sidebar Structure Verification ===\n";

$htmlFile = file_get_contents('c:\\Users\\cindy\\OneDrive\\Documents\\tick B\\pplg\\writing hting\\lodit - Copy - Copy - Copy\\lodit\\resources\\views\\components\\sidebar.blade.php');

// Check for My History with data-visible-level="1"
if (preg_match('/data-visible-level="1".*?My History/s', $htmlFile)) {
    echo "✓ My History has data-visible-level=\"1\"\n";
} else {
    echo "✗ My History missing or data-visible-level=\"1\" not found\n";
}

// Check for Medicines in admin section
if (preg_match('/admin-section.*?medicines.*?admin-levels/s', $htmlFile)) {
    echo "✓ Medicines in admin section with admin-levels\n";
} else {
    echo "✗ Medicines not properly in admin section\n";
}

// Check for Transactions in admin section
if (preg_match('/transactions.*?admin-levels/s', $htmlFile)) {
    echo "✓ Transactions in admin section with admin-levels\n";
} else {
    echo "✗ Transactions not properly in admin section\n";
}

// Check for admin items having data-admin-levels attribute
if (preg_match_all('/class="nav-item permission-item admin-item"[^>]*data-admin-levels="[^"]*"/', $htmlFile, $matches)) {
    echo "✓ Found " . count($matches[0]) . " admin items with data-admin-levels\n";
} else {
    echo "✗ No admin items found with data-admin-levels\n";
}

echo "\n=== Expected Sidebar for Level 1 ===\n";
echo "Should see:\n";
echo "  - Home\n";
echo "  - POS System\n";
echo "  - My History\n";
echo "  - Notifications\n";
echo "  - Profile\n";
echo "  - Logout\n";
echo "\nShould NOT see:\n";
echo "  - Medicines\n";
echo "  - Transactions\n";
echo "  - Admin Panel section\n";
?>
