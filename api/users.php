<?php
require_once "../controllers/user.php";
$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents('php://input'), true);
$queryParams = $_POST;
switch ($method) {
    case 'GET':
        if (empty($_GET)) {
            getUsers();
        } else {
            if (isset($_GET["token"])) {
                confirmRegistration($_GET["token"]);
            }
            if (isset($_GET["getDocumentTypes"])) {
                getDocumentTypes();
            }
        }
        break;
    case 'POST':
        if (empty($_GET)) {
            save($method, $queryParams);
        } else {
            if (isset($_GET["q"])) {
                if (($_GET["q"] === "login")) {
                    login($queryParams);
                }
            }
            if (isset($_GET["resend"])) {
                sendEmailVerification($_GET["resend"]);
            }
        }
        break;
    case "PUT":

        break;
    case 'DELETE':
        deleteUser($_REQUEST);
        break;
}
