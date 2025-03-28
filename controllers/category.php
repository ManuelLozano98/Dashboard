<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../models/category.php';

$category = new Category();
$params = filter_input_array(INPUT_POST);
switch (filter_input_array(INPUT_GET)["do"]) {

    case "category":
        $response = $category->getCategory($params["idcategory"]);
        echo json_encode($reponse);
        break;
    case "categories":
        $categories = $category->getCategories();
        $records = $category->getCountCategories();
        $data = array(
                "records" => (int) $records[0]["RECORDS"],
                "data" => $categories
        );
        echo json_encode($data);
        break;
    case "save":
        if (!isset($params["idcategory"])) {
            $response = $category->insert($params["name"], $params["description"]);
            echo $response ? "Category registered" : "The category could not be registered";
        } else {
            $response = $category->edit($params["idcategory"], $params["name"], $params["description"]);
            echo $response ? "Category successfully updated" : "The category could not be updated";
        }
        break;
    case "disable":
        $response = $category->disable($params["idcategory"]);
        echo $response ? "Category successfully disabled" : "The category could not be disabled";
        break;
    case "enable":
        $response = $category->enable($params["idcategory"]);
        echo $response ? "Category successfully enabled" : "The category could not be enabled";
        break;
}
