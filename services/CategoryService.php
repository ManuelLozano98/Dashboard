<?php

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Response.php';

class CategoryService
{

    public function getCategories()
    {
        return Category::getAll();
    }
    public function getCategory($id)
    {
        return Category::findById($id);
    }
    public function getCategoriesName()
    {
        return Category::findByIdAndName();
    }
    public function deleteCategory($id)
    {
        $category = Category::findById($id);
        $response = new Response();
        if (!$category) {
            return $response->notFound("Category");
        }
        return Category::delete($id) ? $response->deleted("Category") : $response->setErrorResponse(
            "Deletion failed",
            "The category could not be deleted due to an internal error",
            500
        );
    }
    public function saveCategory($method, $arrayCategory)
    {
        $response = new Response();
        if ($method === "POST") {
            if (Category::findByName($arrayCategory["name"])) {
                return $response->conflict("Category");
            }
            $category = new Category($arrayCategory);
            if (Category::insert($category) === false) {
                $response->setErrorResponse(
                    "Insertion failed",
                    "The category could not be created due to an internal error",
                    500
                );
            } else {
                $response->created("Category");
                $response->setData($category->toArray());
            }
            return $response;

        } else {
            $categoryDb = Category::findById($arrayCategory["id"]);
            if (!$categoryDb) {
                return $response->notFound("Category");
            }
            $insertedName = Category::findByName($arrayCategory["name"]);
            if ($insertedName) {
                if ($insertedName->getName() === $categoryDb->getName() && $categoryDb->getId() !== $insertedName->getId()) { // Checks if the name of the category is already taken
                    return $response->conflict("Category");
                }
            }
            $categoryDb->setName($arrayCategory["name"]);
            $categoryDb->setDescription($arrayCategory["description"] ?? $categoryDb->getDescription());
            $categoryDb->setActive($arrayCategory["active"] ?? $categoryDb->getActive());
            if (Category::edit($categoryDb) === false) {
                $response->setErrorResponse(
                    "Update failed",
                    "The category could not be updated due to an internal error",
                    500
                );
            } else {
                $response->updated("Category");
                $response->setData($categoryDb->toArray());
            }
            return $response;
        }
    }

}