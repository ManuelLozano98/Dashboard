<?php
require_once "../controllers/article.php";
$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents('php://input'), true);
$queryParams = $_POST;
switch ($method) {
    case 'GET':
        $queryParams = $_GET;
        if (!empty($queryParams["code"])) {
            checkIfExitsCode($queryParams["code"]);
        } else {
            getArticles();
        }
        break;
    case 'POST':
    case 'PUT':
        save($method, $queryParams);
        break;
    case 'DELETE':
        deleteArticle($_REQUEST);
        break;
}

?>