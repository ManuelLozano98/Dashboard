<?php
require_once __DIR__ . '/../configurations/config.php';
require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../services/UserRolesService.php';
define('VIEWS_PATH', dirname(__DIR__) . '/views');
$method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = array_values(array_filter(explode('/', $uri)));
$resource = $uriParts[1] ?? null;
$action = $uriParts[2] ?? null;
$body = json_decode(file_get_contents("php://input"), true) ?? [];
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$controller = new HomeController();
$isLogged = $controller->isLogged();
$userRoleService = new UserRolesService();

$adminRoutes = [
    'articles',
    'categories',
    'users',
    'roles'


];
$user = $_SESSION["user"] ?? NULL;
    if (!$userRoleService->hasAdminRole($user) && in_array($resource, $adminRoutes)) {
        $uri = ROOT . '/notFound'; // Send to 404.php because that URL does not exist
}



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
        if ($isLogged) {
            require __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->index();
        } else {
            $controller->index();
        }
        break;
    case ROOT . "/register":
        $controller->register();
        break;
    case ROOT . "/login":
        $controller->login();
        break;
    case ROOT . "/logout":
        if ($isLogged) {
            require __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->logout();
        } else {
            header("Location: " . ROOT);
        }
        break;
    case ROOT . "/confirm-email":
        $controller->confirmEmail();
        break;
    case ROOT . "/email/confirm":
        $controller->emailConfirmed();
        break;
    case ROOT:
    case ROOT . "/":
        if ($isLogged) {
            require_once __DIR__ . '/../controllers/UserController.php';
            $userController = new UserController();
            $userController->index();
        } else {
            $controller->index();
        }
        break;

    default:
        require VIEWS_PATH . '/404.php';
        break;
}