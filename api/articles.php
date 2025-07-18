<?php
require_once __DIR__ . "/../services/ArticleService.php";

$method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = array_values(array_filter(explode('/', $uri)));
$action = $uriParts[3] ?? null;
$id = $uriParts[4] ?? null;

$queryParams = $method === 'GET' ? $_GET : $_POST;
$articleService = new ArticleService();
$response = new Response();

switch ($method) {
    case 'GET':
        handleGetRequest($action, $queryParams, $articleService, $response);
        break;

    case 'POST':
        $methodOverride = $queryParams['_method'] ?? null;
        if ($methodOverride === 'PUT') {
            $method = 'PUT';
        }
        handlePostOrPutRequest($method, $queryParams, $articleService, $response);
        break;

    case 'DELETE':
        handleDeleteRequest($action, $articleService, $response);
        break;
}

function handleGetRequest($action, $params, $service, $response)
{
    if ($action === "check-code") {
        if (!empty($params["code"]) && ctype_digit($params["code"])) {
            echo $service->getArticleCode($params["code"])->toJson();
        } else {
            echo $response->missingFields()->toJson();
        }
        return;
    }

    $rawArticles = $service->getArticles();
    $data = array_map(function ($item) {
        $articleArray = $item['article']->toArray();
        $articleArray['category_name'] = $item['category_name'];
        $articleArray["price"] .= "€";
        return $articleArray;
    }, $rawArticles);

    echo json_encode(['data' => $data]);
}

function handlePostOrPutRequest($method, $params, $service, $response)
{
    $data = $params;
    $data["image"] = !empty($_FILES["image"]["name"]) ? $_FILES["image"] : '';
    $data = sanitizeArticleData($data);
    $requiredFields = [
        'name' => $data['name'],
        'code' => $data['code'],
        'id_category' => $data['id_category'],
    ];

    if ($method === "PUT") {
        $requiredFields['id'] = $data['id'] ?? null;
    }

    $hasAllFields = !in_array('', $requiredFields, true)
        && !in_array(null, $requiredFields, true);

    if (!$hasAllFields) {
        $missingFields = [];
        if (empty($data['name']))
            $missingFields['name'] = 'The name is required';
        if (empty($data['code']))
            $missingFields['code'] = 'The code is required';
        if (empty($data['id_category']))
            $missingFields['id_category'] = 'The id_category is required';
        $response->setDetails($missingFields);
        echo $response->missingFields()->toJson();
        return;
    }

    if (!isAllValid($data, $method)) {
        echo $response->setErrorResponse("Bad request", "There one or more wrong fields")->toJson();
        return;
    }

    $result = $service->saveArticle($method, $data);
    echo $result->toJson();
}

function handleDeleteRequest($id, $service, $response)
{
    $id = (int) $id ?? NULL;
    if (empty($id)) {
        $response->missingFields()->setDetails(["id" => "The id is required"]);
    } elseif (!is_int($id)) {
        $response->setErrorResponse("Bad request", "The id is not a valid integer");
    } else {
        $response = $service->deleteArticle($id);
    }

    echo $response->toJson();
}

function sanitizeText($text)
{
    return strip_tags(trim($text));
}

function sanitizeArticleData($data)
{
    if (!empty($data["image"])) {
        $data["image"]["name"] = sanitizeText($data["image"]["name"]);
    } else {
        $data["image"] = '';
    }
    return [
        'name' => sanitizeText($data['name'] ?? ''),
        'description' => sanitizeText($data['description'] ?? ''),
        'code' => sanitizeText($data['code'] ?? ''),
        'image' => $data["image"],
        'active' => isset($data['active']) ? (int) $data['active'] : null,
        'stock' => isset($data['stock']) ? (int) $data['stock'] : null,
        'price' => isset($data['price']) ? str_replace(',', '.', $data['price']) : null,
        'id_category' => isset($data['id_category']) ? (int) $data['id_category'] : null,
        'id' => isset($data['id']) ? (int) $data['id'] : null,
    ];
}

function isAllValid($data, $method)
{
    $commonValid = isNameValid($data["name"])
        && isDescriptionValid($data["description"])
        && isActiveValid($data["active"])
        && isStockValid($data["stock"])
        && isPriceValid($data["price"])
        && isProductCodeValid($data["code"])
        && isIdCategoryValid($data["id_category"])
        && isImageValid($data["image"]);

    return $method === "PUT"
        ? $commonValid && isIdValid($data["id"])
        : $commonValid;
}

function isNameValid($name)
{
    return (bool) preg_match("/^[a-zA-ZçÇáéíóúÁÉÍÓÚñÑ0-9\s\+-,.():]{3,}$/u", $name);
}

function isDescriptionValid($description)
{
    return (bool) preg_match("/^.{0,255}$/s", $description);
}

function isActiveValid($active)
{
    return in_array($active, [0, 1, null], true);
}

function isStockValid($stock)
{
    return (is_int($stock) && $stock >= 0) || $stock === null;
}

function isPriceValid($price)
{
    return (is_numeric($price) && preg_match('/^\d+(\.\d{1,2})?$/', $price) && $price >= 0) || $price === null;
}

function isProductCodeValid($code)
{
    return ctype_digit($code) && (int) $code >= 0;
}

function isIdCategoryValid($id)
{
    return is_int($id) && $id >= 0;
}

function isImageValid($img)
{
    if (empty($img))
        return true;
    $fileName = basename($img['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    return in_array($fileExt, $allowed, true);
}

function isIdValid($id)
{
    return is_int($id) && $id >= 0;
}
