<?php

require_once __DIR__ . "/../services/UserRolesService.php";

$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents('php://input'), true);
$uriParts = array_values(array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))));
$idResource = $uriParts[3] ?? null;

$roleService = new UserRolesService();
$response = new Response();


switch ($method) {
    case 'GET':
        handleGetRequest($roleService, $idResource, $response);
        break;
    case 'POST':
        handlePostRequest($roleService, $body, $response);
        break;

    case 'PUT':
        handlePutRequest($roleService, $idResource, $body, $response);
        break;

    case 'DELETE':
        handleDeleteRequest($roleService, $idResource, $response);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function handleGetRequest(UserRolesService $service, $action, $response): void
{

    if (is_numeric($action)) {         // GET /api/roles/{id}
        $role = $service->getRoleService()->getRole($action);
        if ($role !== FALSE) {
            echo json_encode($role->toArray());
        } else {
            echo $response->setSuccessResponse("No data")->toJson();
        }

    } elseif ($action === null) {         // GET /api/roles
        $roles = $service->getRoles();
        $data = array_map(fn(Role $r) => $r->toArray(), $roles);
        echo json_encode(['data' => $data]);

    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Invalid URL or resource not found']);
    }
}
function handlePostRequest(UserRolesService $service, $body, $response)
{
    if (empty($body['name'])) {
        $response->missingFields();
        $response->setDetails(['name' => 'The name is required']);
        echo $response->toJson();
        return;
    }
    if(empty($body["users"])) $body["users"] = [];
    $body["users"] = array_map('intval', $body["users"]);
    $body["name"] = sanitizeText($body["name"]);

    if (isNameValid($body['name']) && isUserValid($body["users"])) {
        $response = $service->getRoleService()->saveRole('POST', $body);
    } else {
        $response->setErrorResponse("Bad Request", "There are one or more invalid fields");
    }
    if ($response->getStatus() === 201) {
        $roleId = $response->getData()["id"];
        foreach ($body["users"] as $user) {
            $arrayUserRoles = [
                "id_user" => $user,
                "id_role" => $roleId
            ];
            $response->setData($service->saveUserRoles('POST', $arrayUserRoles)->getData());
        }
    }



    echo $response->toJson();
}


function handlePutRequest(UserRolesService $service, $id, $body, $response)
{
    $missingFields = [];
    if (empty($id))
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

    if (isNameValid($body['name']) && isUserValid([$id])) {
        $response = $service->getRoleService()->saveRole('PUT', [
            "id" => $id,
            "name" => $body["name"]
        ]);
    } else {
        $response->setErrorResponse("Bad Request", "There are one or more invalid fields");
    }

    echo $response->toJson();
}

function handleDeleteRequest(UserRolesService $service, $resource, $response)
{
    $resource = (int) $resource ?? NULL;
    if (!is_int($resource)) {
        $response->setErrorResponse("Bad Request", "The id must be numeric");
    } else {
        $response = $service->deleteUserRoles($resource);
        if ($response->getStatus() <= 299) {
            $response = $service->getRoleService()->deleteRole($resource);
        }
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
function isUserValid($users)
{
    $valid = false;
    foreach ($users as $user) {
        if (!is_int($user) && $user <= 0) {
            return $valid;
        }
    }
    $valid = true;
    return $valid;
}