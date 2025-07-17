<?php
require_once __DIR__ . '/../configurations/config.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = array_values(array_filter(explode('/', $uri)));
$resource = $uriParts[2] ?? null;

switch ($resource) {
    case "articles":
        require __DIR__ . '/../api/articles.php';
        break;
    case "categories":
        require __DIR__ . '/../api/categories.php';
        break;
    case "users":
        require __DIR__ . '/../api/users.php';
        break;
    case "roles":
        require __DIR__ . '/../api/roles.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
        break;
}