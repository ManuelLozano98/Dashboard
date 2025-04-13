<?php
require_once "../controllers/category.php";
$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents('php://input'),true);
switch ($method) {
    case 'GET':
        getCategories();
        break;
    case 'POST':
    case 'PUT':
        save($method,$body);
        break;
    case 'DELETE':
        deleteCategory($_REQUEST);
        break;
}

?>