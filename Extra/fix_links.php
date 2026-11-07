<?php
$dir = __DIR__ . '/HTML';
$files = glob($dir . '/*.html');
foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;
    
    // Replace href="index.html" with the PHP homepage relative to the HTML folder.
    $content = str_replace('href="index.html"', 'href="../index.php"', $content);
    // Also handle single quotes just in case
    $content = str_replace("href='index.html'", "href='../index.php'", $content);
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Updated links in: " . basename($file) . "\n";
    }
}
?>
