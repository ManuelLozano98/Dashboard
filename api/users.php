<?php
require_once "../controllers/user.php";
$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents('php://input'), true);
$queryParams = $_POST;
switch ($method) {
    case 'GET':
        break;
    case 'POST':
        if (empty($_GET)) {
            save($method, $queryParams);
        } else {
            if (($_GET["q"] === "login")) {
                login($queryParams);
            }
        }
        break;
    case "PUT":

        break;
    case 'DELETE':
        break;
}
