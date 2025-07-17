<?php
require_once __DIR__ . '/../configurations/config.php';

define('VIEWS_PATH', dirname(__DIR__) . '/views');
$method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = array_values(array_filter(explode('/', $uri)));
$resource = $uriParts[2] ?? null;
$action = $uriParts[3] ?? null;
$body = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($uri) {
    case ROOT . "/articles":
        require __DIR__ . '/../controllers/ArticleController.php';
        $controller = new ArticleController();
        $controller->index();
        break;
    case ROOT . "/categories":
        require __DIR__ . '/../controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->index();
        break;
    case ROOT . "/users":
        require __DIR__ . '/../controllers/UserController.php';
        $controller = new UserController();
        $controller->listUsers();
        break;
    case ROOT . "/roles":
        require __DIR__ . '/../controllers/RoleController.php';
        $controller = new RoleController();
        $controller->index();
        break;
    case ROOT . "/home":
        require __DIR__ . '/../controllers/UserController.php';
        $controller = new UserController();
        $controller->index();
        break;
    case ROOT . "/register":
        require __DIR__ . '/../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->register();
        break;
    case ROOT . "/login":
        require __DIR__ . '/../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->login();
        break;
    case ROOT . "/logout":
        require __DIR__ . '/../controllers/UserController.php';
        $controller = new UserController();
        $controller->logout();
        break;
    case ROOT . "/confirm-email":
        require __DIR__ . '/../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->confirmEmail();
        break;
    case ROOT . "/email-confirmed":
        require __DIR__ . '/../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->emailConfirmed();
        break;
    case ROOT:
    case ROOT . "/":
        require __DIR__ . '/../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->login();
        break;

    default:
        require VIEWS_PATH . '/404.php';
        break;
}