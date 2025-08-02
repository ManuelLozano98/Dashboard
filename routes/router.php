<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($uri, "/AdminDashboard/api/")) {
    require_once __DIR__ . "/apiRouter.php";
} else {
    require_once __DIR__ . "/controllerRouter.php";
}

