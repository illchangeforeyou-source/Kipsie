<?php

$filePath = 'resources/views/layouts/app.blade.php';
$content = file_get_contents($filePath);

if (strpos($content, 'permission-enforcer.js') === false) {
    $newContent = str_replace(
        "@yield('scripts')",
        "    <!-- Permission Enforcer System -->
    <script src=\"{{ asset('js/permission-enforcer.js') }}\"></script>

    @yield('scripts')",
        $content
    );
    
    file_put_contents($filePath, $newContent);
    echo "✅ Added permission-enforcer.js to app.blade.php\n";
} else {
    echo "⚠️ permission-enforcer.js already included\n";
}

// Also update the inline script to add console logs
if (strpos($content, 'User level set to:') !== false) {
    $newContent2 = str_replace(
        "console.log('User level set to:', window.userLevel);",
        "console.log('🔑 User level set to:', window.userLevel);\n        console.log('👤 User ID:', window.userId);\n        console.log('📋 Permission system initializing...');",
        file_get_contents($filePath)
    );
    
    file_put_contents($filePath, $newContent2);
    echo "✅ Updated console logs in app.blade.php\n";
}

echo "\nDone!\n";
?>
