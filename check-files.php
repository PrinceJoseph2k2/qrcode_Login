<?php
// File: check-files.php
echo "Checking PHP files for BOM or whitespace issues...\n\n";

$files = [
    'backend/api.php',
    'backend/config/database.php',
    'backend/config/app.php',
    'backend/controllers/AuthController.php',
    'backend/controllers/SimpleRegisterController.php',
    'backend/models/User.php',
    'backend/models/AuthSession.php',
    'backend/helpers/SecurityHelper.php'
];

foreach ($files as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        
        // Check for BOM
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            echo "✗ $file - Has BOM (Byte Order Mark). Remove it!\n";
        } 
        // Check for whitespace before <?php
        else if (preg_match('/^\s+<\?php/', $content)) {
            echo "✗ $file - Has whitespace before <?php\n";
        }
        else {
            echo "✓ $file - OK\n";
        }
    } else {
        echo "✗ $file - File not found\n";
    }
}
?>