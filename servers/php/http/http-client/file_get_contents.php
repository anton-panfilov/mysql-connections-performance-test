<?php

require __DIR__ . '/../../vendor/autoload.php';

$name = "link";

if (isset($_GET[$name]) && is_string($_GET[$name]) && filter_var($_GET[$name], FILTER_VALIDATE_URL)){
    $url = $_GET[$name];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-cURL');

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        http_response_code(500);
        echo "Error: " . curl_error($ch);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode >= 200 && $httpCode < 300) {
            header("Content-Type: text/plain");
            echo $response;
        } else {
            http_response_code($httpCode);
            echo "Failed to fetch URL. HTTP status code: $httpCode";
        }
    }

    curl_close($ch);
} else {
    http_response_code(400);
    echo "invalid query param `$name`";
}

