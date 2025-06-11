<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../models/article.php';

function getArticles()
{
    $article = new Article();
    $articles = $article->getArticles();
    echo json_encode($articles);
}

function save($method, $request)
{
    header('Content-Type: application/json');
    $validated = validateFields($request);
    $response = null;
    if ($validated) {
        $article = new Article();
        $response = $article->insertOrEdit($request, $method);
    } else {
        $response = new Response();
        $response->getRequiredFieldsMissingMessage();
    }
    echo json_encode($response);
}

function validateFields($params)
{
    if (count($params) === 0) {
        return false;
    }
    foreach ($params as $key => $value) {
        if (empty($key)) {
            return false;
        }
    }
    return true;
}
function deleteArticle($request)
{
    header('Content-Type: application/json');
    $validated = validateFields($request);
    if ($validated) {
        $article = new Article();
        $response = $article->delete($request["id"]);
        echo json_encode($response);
    }
}