<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../models/category.php';

function getCategories()
{
    $category = new Category();
    $categories = $category->getCategories();
    echo json_encode($categories);
}

function getCategoriesName(){
    $category = new Category();
    $categories = $category->getCategoriesName();
    echo json_encode($categories);
}

function save($method, $request)
{
    header('Content-Type: application/json');
    $validated = validateFields($request);
    $response = null;
    if ($validated) {
        $category = new Category();
        $response = $category->insertOrEdit($request, $method);
    } else {
        $response = [
            'error' => "Bad Request",
            'status' => "400",
            'message' => "Required fields are missing"
        ];
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
function deleteCategory($request)
{
    header('Content-Type: application/json');
    $validated = validateFields($request);
    if ($validated) {
        $category = new Category();
        $response = $category->delete($request["id"]);
        echo json_encode($response);
    }
}

function disable()
{
    $category = new Category();
    $response = $category->disable($_REQUEST["idcategory"]);
    echo $response ? "Category successfully disabled" : "The category could not be disabled";
}

function enable()
{
    $category = new Category();
    $response = $category->enable($_REQUEST["idcategory"]);
    echo $response ? "Category successfully enabled" : "The category could not be enabled";
}
