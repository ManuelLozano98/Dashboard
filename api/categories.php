<?php

require_once __DIR__ . "/../services/CategoryService.php";

$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents('php://input'), true);
$uriParts = array_values(array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))));
$idResource = $uriParts[3] ?? null;

$categoryService = new CategoryService();
$response = new Response();


switch ($method) {
    case 'GET':
        handleGetRequest($categoryService, $idResource, $response);
        break;
    case 'POST':
        handlePostRequest($categoryService, $body, $response);
        break;

    case 'PUT':
        handlePutRequest($categoryService, $body, $response);
        break;

    case 'DELETE':
        handleDeleteRequest($categoryService, $idResource, $response);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function handleGetRequest(CategoryService $categoryService, $action, $response): void
{
    if ($action === 'names') {         // GET /api/categories/names
        $categories = $categoryService->getCategoriesName();
        $data = array_map(fn(Category $c) => [
            'id' => (int) $c->getId(),
            'name' => $c->getName()
        ], $categories);

        echo json_encode(['data' => $data]);

    } elseif (is_numeric($action)) {         // GET /api/categories/{id}
        $category = $categoryService->getCategory($action);
        if ($category !== FALSE) {
            echo json_encode($category->toArray());
        } else {
            echo $response->setSuccessResponse("No data")->toJson();
        }

    } elseif ($action === null) {         // GET /api/categories
        $categories = $categoryService->getCategories();
        $data = array_map(fn(Category $c) => $c->toArray(), $categories);
        echo json_encode(['data' => $data]);

    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Invalid URL or resource not found']);
    }
}
function handlePostRequest($service, $body, $response)
{
    if (empty($body['name'])) {
        $response->missingFields();
        $response->setDetails(['name' => 'The name is required']);
        echo $response->toJson();
        return;
    }

    $body = array_map('sanitizeText', $body);
    $isDescValid = empty($body['description']) || isDescriptionValid($body['description']);

    if (isNameValid($body['name']) && $isDescValid) {
        $response = $service->saveCategory('POST', $body);
    } else {
        $response->setErrorResponse("Bad Request", "There are one or more invalid fields");
    }

    echo $response->toJson();
}


function handlePutRequest($service, $body, $response)
{
    $missingFields = [];
    if (empty($body['id']))
        $missingFields['id'] = 'The id is required';
    if (empty($body['name']))
        $missingFields['name'] = 'The name is required';

    if (!empty($missingFields)) {
        $response->missingFields();
        $response->setDetails($missingFields);
        echo $response->toJson();
        return;
    }

    $body['name'] = sanitizeText($body['name']);
    $body['description'] = sanitizeText($body['description'] ?? '');
    $isDescValid = empty($body['description']) || isDescriptionValid($body['description']);
    $isActValid = !isset($body['active']) || isActiveValid((int) $body['active']);

    if (isNameValid($body['name']) && $isDescValid && $isActValid) {
        $response = $service->saveCategory('PUT', $body);
    } else {
        $response->setErrorResponse("Bad Request", "There are one or more invalid fields");
    }

    echo $response->toJson();
}

function handleDeleteRequest($service, $resource, $response)
{
    $resource = (int) $resource ?? NULL;
    if (!is_int($resource)) {
        $response->setErrorResponse("Bad Request", "The id must be numeric");
    } else {
        $response = $service->deleteCategory($resource);
    }

    echo $response->toJson();
}


function sanitizeText($text)
{
    return strip_tags(trim($text));
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
    return $active === 0 || $active === 1;
}