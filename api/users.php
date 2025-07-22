<?php
require_once __DIR__ . "/../services/UserService.php";
require_once __DIR__ . "/../models/Response.php";

$method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = array_values(array_filter(explode('/', $uri)));
$action = $uriParts[3] ?? null;
$id = $uriParts[4] ?? null;

$queryParams = $method === 'GET' ? $_GET : $_POST;
$userService = new UserService();
$response = new Response();

switch ($method) {
    case 'GET':
        handleGetRequest($action, $queryParams, $userService, $response);
        break;

    case 'POST':
        $methodOverride = $queryParams['_method'] ?? null;
        if ($methodOverride === 'PUT') {
            $method = 'PUT';
        }
        if (isset($_GET["resend"])) {
            $email = $_GET["resend"];
            $userDb = $userService->getUserByEmail($email);
            if (!$userDb) {
                echo $response->notFound("Email")->toJson();
                return;
            }
            echo $userService->sendEmailVerification($email)->toJson();
            return;
        }
        if (isset($_GET["q"])) {
            if (($_GET["q"] === "login")) {
                $login = sanitizeText($queryParams["login"]) ?? "";
                $password = sanitizeText($queryParams["password"]) ?? "";
                $data = [
                    "login" => $login,
                    "password" => $password
                ];
                echo $userService->checkLogin($data)->toJson();
                return;
            }
        }
        handlePostOrPutRequest($method, $queryParams, $userService, $response);
        break;

    case 'DELETE':
        handleDeleteRequest($action, $userService, $response);
        break;
}

function handleGetRequest($action, $params, $service, $response)
{
    if ($action === 'documentTypes') {
        $documentTypes = $service->getDocumentTypes();
        $data = array_map(fn(Document_Type $d) => $d->toArray(), $documentTypes);
        echo json_encode(['data' => $data]);

    } elseif (is_numeric($action)) {
        $user = $service->getUser($action);
        if ($user !== FALSE) {
            echo json_encode($user->toArray());
        } else {
            echo $response->setSuccessResponse("No data")->toJson();
        }
    } elseif ($action === null) {
        $users = $service->getUsers();
        foreach ($users as $user) {
            $arrayUser = $user->toArray();
            $arrayUser['document_name'] = $service->getDocumentNameByUser($user);
            $data[] = $arrayUser;
        }
        echo json_encode(['data' => $data]);

    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Invalid URL or resource not found']);
    }
}

function handlePostOrPutRequest($method, $params, $service, $response)
{
    $data = $params;
    $data["image"] = !empty($_FILES["image"]["name"]) ? $_FILES["image"] : '';
    $data = sanitizeUserData($data);
    $requiredFields = [
        'name' => $data['name'],
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => $data['password']
    ];

    if ($method === "PUT") {
        $requiredFields = [];
        $requiredFields['id'] = $data['id'] ?? null;
        $requiredFields['name'] = $data['name'] ?? null;
    }

    $hasAllFields = !in_array('', $requiredFields, true)
        && !in_array(null, $requiredFields, true);

    if (!$hasAllFields) {
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($field)) {
                $missingFields[$field] = 'The ' . $field . ' is required';
            }
        }
        $response->setDetails($missingFields);
        echo $response->toJson();
        return;
    }

    if (!isAllValid($data, $method)) {
        echo $response->setErrorResponse("Bad request", "There one or more wrong fields")->toJson();
        return;
    }

    $result = $service->saveUser($method, $data);
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
        $response = $service->deleteUser($id);
    }

    echo $response->toJson();
}

function sanitizeText($text)
{
    return strip_tags(trim($text));
}

function sanitizeUserData($data)
{
    if (!empty($data["image"])) {
        $data["image"]["name"] = sanitizeText($data["image"]["name"]);
    } else {
        $data["image"] = '';
    }
    return [
        'name' => sanitizeText($data['name'] ?? ''),
        'username' => sanitizeText($data['username'] ?? ''),
        'password' => sanitizeText($data['password'] ?? ''),
        'email' => sanitizeText($data['email'] ?? ''),
        'phone' => sanitizeText($data['phone'] ?? ''),
        'address' => sanitizeText($data["address"] ?? ''),
        'image' => $data["image"],
        'document' => sanitizeText($data['document'] ?? ''),
        'active' => isset($data['active']) ? (int) $data['active'] : null,
        'document_type' => isset($data['document_type']) ? (int) $data['document_type'] : null,
        'id' => isset($data['id']) ? (int) $data['id'] : null,
    ];
}

function isAllValid($data, $method)
{
    $commonValid = isNameValid($data["name"])
        && isUsernameValid($data["username"])
        && isPasswordValid($data["password"])
        && isEmailValid($data["email"])
        && isPhoneValid($data["phone"])
        && isImageValid($data["image"]);

    return $method === "PUT"
        ? (
            ($commonValid = isNameValid($data["name"]) && isIdValid($data["id"])) &&
            (!empty($data["password"]) ? isPasswordValid($data["password"]) : true) &&
            (!empty($data["email"]) ? isEmailValid($data["email"]) : true) &&
            (!empty($data["phone"]) ? isPhoneValid($data["phone"]) : true) &&
            isImageValid($data["image"])
        )
        : $commonValid;
}

function isNameValid($name)
{
    return (bool) preg_match("/^[a-zA-Z]{3,}$/", $name);
}

function isUsernameValid($username)
{
    return (bool) preg_match("/^[a-zA-Z0-9](?!.*[_.]{2})[a-zA-Z0-9._]{2,18}[a-zA-Z0-9]$/", $username);
}

function isPasswordValid($password)
{
    return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
}

function isEmailValid($email)
{
    return (bool) preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
}

function isPhoneValid($phone)
{
    return (bool) preg_match('/^[6-9]\d{8}$/', $phone) || $phone === "";
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

