<?php
require __DIR__ . '/../vendor/autoload.php';

echo "PHP Performance test<br><br>";

foreach (scandir(__DIR__) as $file) {
    if (str_ends_with($file, ".php") && $file != "index.php") {
        $link = "/" . substr($file, 0, -4);
        echo "<div>- <a href='$link'>$link</a></div>";
    }
}