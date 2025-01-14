<?php

require __DIR__ . '/../../vendor/autoload.php';

$name = "link";

if (isset($_GET[$name]) && is_string($_GET[$name]) && filter_var($_GET[$name], FILTER_VALIDATE_URL)){
    header("Content-Type: text/plain");
    echo file_get_contents($_GET[$name]);
} else {
    http_response_code(400);
    echo "invalid query param `$name`";
}

