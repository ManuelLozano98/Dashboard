<?php
require_once "../controllers/article.php";
$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents('php://input'),true);
switch ($method) {
    case 'GET':
        getArticles();
        break;
    case 'POST':
    case 'PUT':
        save($method,$body);
        break;
    case 'DELETE':
        deleteArticle($_REQUEST);
        break;
}

?>