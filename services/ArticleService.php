<?php

require_once __DIR__ . '/../models/Article.php';
require_once __DIR__ . '/../models/Response.php';
require_once __DIR__ . '/CategoryService.php';
class ArticleService
{
    private CategoryService $categoryService;


    public function getArticles()
    {
        return Article::getAll();
    }

    public function getArticleCode($code)
    {
        $article = Article::findByCode($code);
        $response = new Response();
        if ($article === false) {
            $response->setSuccessResponse("OK");
        } else {
            $response->conflict("article code");
        }
        return $response;

    }

    public function deleteArticle($id)
    {
        $article = Article::findById($id);
        $response = new Response();
        if (!$article) {
            return $response->notFound("Article");
        }
        return Article::delete($id) ? $response->deleted("Article") : $response->setErrorResponse(
            "Deletion failed",
            "The article could not be deleted due to an internal error",
            500
        );
    }
    public function saveArticle($method, $arrayArticle)
    {
        $response = new Response();
        if ($method === "POST") {
            $this->categoryService = new CategoryService();
            if (!$this->categoryService->getCategory($arrayArticle["id_category"])) {
                return $response->notFound("Category");
            }
            if (Article::findByName($arrayArticle["name"]) || Article::findByCode($arrayArticle["code"])) {
                return $response->conflict("Article");
            }
            $article = new Article($arrayArticle);
            $article->setIdCategory((int) $article->getIdCategory());
            $img = "";
            if (isset($_FILES["image"]) && $_FILES["image"]['error'] === UPLOAD_ERR_OK) {
                $img = $this->saveImage($_FILES["image"]);
                $article->setImage($img);
            }

            if (Article::insert($article) === false) {
                $response->setErrorResponse(
                    "Insertion failed",
                    "The article could not be created due to an internal error",
                    500
                );
            } else {
                $response->created("Article");
                $response->setData($article->toArray());
            }
            return $response;

        } else {
            $articleDb = Article::findById($arrayArticle["id"]);
            if (!$articleDb) {
                return $response->notFound("Article");
            }
            $this->categoryService = new CategoryService();
            if (!$this->categoryService->getCategory($arrayArticle["id_category"])) {
                return $response->notFound("Category");
            }

            $existingArticleName = Article::findByName($arrayArticle["name"]);
            $articleNameTaken = $existingArticleName && $existingArticleName->getId() !== $articleDb->getId();

            $existingArticleCode = Article::findByCode($arrayArticle["code"]);
            $articleCodeTaken = $existingArticleCode && $existingArticleCode->getId() !== $articleDb->getId();

            if ($articleNameTaken || $articleCodeTaken) {
                return $response->conflict("Article");
            }


            $img = "";
            if (isset($_FILES["image"]) && $_FILES["image"]['error'] === UPLOAD_ERR_OK) {
                $img = $this->saveImage($_FILES["image"]);
                $articleDb->setImage($img);
            }
            $articleDb->setName($arrayArticle["name"]);
            $articleDb->setIdCategory($arrayArticle["id_category"]);
            $articleDb->setCode($arrayArticle["code"]);
            $articleDb->setDescription(empty($arrayArticle["description"]) ? $articleDb->getDescription() : $arrayArticle["description"]);
            $articleDb->setActive($arrayArticle["active"] ?? $articleDb->getActive());
            $articleDb->setPrice($arrayArticle["price"] ?? $articleDb->getPrice());
            $articleDb->setStock($arrayArticle["stock"] ?? $articleDb->getStock());
            if (Article::edit($articleDb) === false) {
                $response->setErrorResponse(
                    "Update failed",
                    "The article could not be updated due to an internal error",
                    500
                );
            } else {
                $response->updated("Article");
                $response->setData($articleDb->toArray());
            }
            return $response;
        }
    }
    public function saveImage($img)
    {
        $tmpName = $img['tmp_name'];
        $fileName = basename($img['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExt, $allowed)) {
            $newName = uniqid() . '.' . $fileExt;
            $uploadPath = __DIR__ . '/../files/articles_img/' . $newName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                return $newName;
            } else {
                return "File couldnt move";
            }
        } else {
            return "Extension not allowed";
        }
    }

}