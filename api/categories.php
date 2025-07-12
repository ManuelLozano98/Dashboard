<?php
require_once "../services/CategoryService.php";
$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents('php://input'), true);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$uri = array_filter($uri, fn($u) => !empty($u));
switch ($method) {
    case 'GET':
        $categoryService = new CategoryService();
        if (isset($uri["4"])) { //Admindashboard/api/categories/names
            $categories = $categoryService->getCategoriesName();
            $data = array_map(function (Category $c) {
                return [
                    'id' => (int) $c->getId(),
                    'name' => $c->getName(),
                ];
            }, $categories);
            echo json_encode(['data' => $data]);
        } else {
            $categories = $categoryService->getCategories();
            $data = array_map(function (Category $category) {
                return [
                    'id_category' => (int) $category->getId(),
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),
                    'active' => (int) $category->getActive()
                ];
            }, $categories);
            echo json_encode(['data' => $data]);
        }
        break;
    case 'POST':
        $categoryService = new CategoryService();
        $response = new Response();
        if (empty($body["name"])) {
            $response->missingFields();
            $response->setDetails(["name" => "The name is required"]);
            echo $response->toJson();
            return;
        }
        $body = array_map('sanitizeText', $body);
        $isDescValid = empty($body["description"]) || isDescriptionValid($body["description"]);

        if (isNameValid($body["name"]) && $isDescValid) {
            $response = $categoryService->saveCategory($method, $body);
        } else {
            $response->setErrorResponse("Bad Request", "There are one or more invalid fields");
        }

        echo $response->toJson();

        break;
    case 'PUT':
        $categoryService = new CategoryService();
        $response = new Response();
        if (empty($body["id"]) || empty($body["name"])) {
            $response = $response->missingFields();
            $response->setDetails(["id" => "The id is required"]);
            $response->setDetails(["name" => "The name is required"]);
            echo $response->toJson();
            return;
        }
        $body["name"] = sanitizeText($body["name"] ?? '');
        $body["description"] = sanitizeText($body["description"] ?? '');
        $id = !empty($body["id"]);
        $isDescValid = empty($body["description"]) || isDescriptionValid($body["description"]);
        $isActValid = empty($body["active"]) || isActiveValid($body["active"]);

        if (isNameValid($body["name"]) && $id && $isDescValid && $isActValid) {
            $response = $categoryService->saveCategory($method, $body);
        } else {
            $response->setErrorResponse("Bad Request", "There are one or more invalid fields");
        }

        echo $response->toJson();
        break;
    case 'DELETE':
        $categoryService = new CategoryService();
        $response = new Response();
        if (!empty($_GET["id"])) {
            if (intval($_GET["id"])) {
                $response = $categoryService->deleteCategory($_GET["id"]);
            } else {
                $response = $response->setErrorResponse("Bad request", "The id is not integer");
            }
        } else {
            $response = $response->missingFields();
            $response->setDetails(["id" => "The id is required"]);
        }

        echo $response->toJson();
        break;


}

function sanitizeText($text)
{
    $text = trim($text);
    $text = strip_tags($text);
    return $text;
}



function isNameValid($name)
{
    return (bool) preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/u", $name);
}

function isDescriptionValid($description)
{
    return (bool) preg_match("/^.{0,255}$/s", $description);
}
function isActiveValid($active)
{
    return $active === 0 || $active === 1 ? true : false;
}
?>