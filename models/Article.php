<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Article
 *
 * @author Usuario
 */
require_once '../configurations/database.php';
require_once 'Response.php';

class Article
{
    private $id;
    private $name;
    private $description;
    private $active;

    private Response $response;

    function __construct()
    {
        $this->response = new Response();
    }


    private function insert($article)
    {
        if ($this->findByName($article["name"]) || $this->findByCode($article["code"])) {
            $this->response->getAddConflictMessage("Article");
            return $this->response->buildResponse();

        }
        $img = "";
        if (isset($_FILES["image"]) && $_FILES["image"]['error'] === UPLOAD_ERR_OK) {
            $img = $this->saveImage($_FILES["image"]);
        }
        $sql = "INSERT INTO ARTICLES (ID_CATEGORY, CODE, NAME, DESCRIPTION, IMAGE, STOCK, ACTIVE) VALUES (?,?,?,?,?,?,?)";
        preparedQuerySQL($sql, "issssii", $article["id_category"], $article["code"], $article["name"], $article["description"], $img, $article["stock"], 1);
        $this->response->getCreatedSuccesfullyMessage("Article");
        $this->response->setData($this->findByName($article["name"]));
        return $this->response->buildResponse();
    }

    private function saveImage($img)
    {
        $tmpName = $img['tmp_name'];
        $fileName = basename($img['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExt, $allowed)) {
            $newName = uniqid() . '.' . $fileExt;
            $uploadPath = '../articles_img/' . $newName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                return $newName;
            } else {
                return "File couldnt move";
            }
        } else {
            return "Ext not allowed";
        }
    }
    private function edit($data)
    {
        // $dbArticle = $this->findById($id);
        // if (!$dbArticle) {
        //     $this->response->setError("Article not found");
        //     $this->response->setStatus("404");
        //     $this->response->setMessage("This article cannot be updated because it does not exist");
        //     return $this->response->buildResponse();
        // }
        // $nameTaken = $this->findByName($name);
        // if ($nameTaken) {
        //     if ($nameTaken[0]["name"] === $name && $dbArticle["id_article"] !== $nameTaken[0]["id_article"]) { // Checks if the name of the article is already taken
        //         $this->response->setError("Conflict");
        //         $this->response->setMessage("This article cannot be updated because it already exists");
        //         $this->response->setStatus("409");
        //         return $this->response->buildResponse();
        //     }
        // }

        // $sql = "UPDATE ARTICLES SET NAME=?, description=?, active=? WHERE ID_CATEGORY=?";
        // preparedQuerySQL($sql, "ssii", $name, $description, $active, $id);
        // $this->response->setError(null);
        // $this->response->setStatus("201");
        // $this->response->setMessage("Article updated successfully");
        // $this->response->setData($this->findById($id));
        // return $this->response->buildResponse();
    }
    public function existsCode($code)
    {
        $data = $this->findByCode($code);
        if (count($data) <= 0) {
            $this->response->setError("OK");
            return $this->response->buildResponse();
        }
        $this->response->setError("Code exists");
        return $this->response->buildResponse();

    }
    public function delete($id)
    {
        $dbArticle = $this->findById($id);
        if (!$dbArticle) {
            $this->response->getDeletedNotFoundMessage("Article");
            return $this->response->buildResponse();
        }
        $sql = "DELETE FROM ARTICLES WHERE ID_CATEGORY = ?";
        preparedQuerySQL($sql, "i", $id);
        $this->response->getDeletedSuccesfullyMessage("Article");
        $this->response->setData(null);
        return $this->response->buildResponse();
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM ARTICLES WHERE ID_CATEGORY=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return $data ? $data[0] : null;
    }


    public function getArticles()
    {
        $sql = "SELECT * FROM ARTICLES";
        $records = $this->getCountArticles();
        $query = querySQL($sql);
        $data = array(
            "records" => (int) $records[0]["RECORDS"],
            "data" => $query
        );
        return $data;
    }

    public function getCountArticles()
    {
        $sql = "SELECT COUNT(*) AS RECORDS FROM ARTICLES";
        return querySQL($sql);
    }
    public function findByName($name)
    {
        $sql = "SELECT * FROM ARTICLES WHERE NAME = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $name);
        return $data;
    }

    public function findByCode($code)
    {
        $sql = "SELECT * FROM ARTICLES WHERE CODE = ?";
        $data = getDataPreparedQuerySQL($sql, "i", $code);
        return $data;
    }
    public function insertOrEdit($data, $method)
    {
        $flag = $this->validateFields($data, $method);
        if ($flag === TRUE) {
            if ($method === "POST") {
                return $this->insert($data);
            } else {
                return $this->edit($data);
            }
        }
        return $flag;
    }
    private function validateFields($fields, $method)
    {
        $this->response->getRequiredFieldsMissingMessage();
        if ($method === "POST") {
            $requiredFields = ["id_category", "code", "name"];
            $validatedFields = 0;
            foreach ($requiredFields as $field) {
                if (array_key_exists($field, $fields) && $fields[$field] !== "") {
                    $validatedFields++;
                } else {
                    $this->response->setDetails([$field => "This field is required"]);
                }

            }
            if ($validatedFields === count($requiredFields)) {
                return true;
            }
            return $this->response->buildResponse();
        } else {
            $requiredFields = ["id_article", "id_category", "code", "name"];
            $validatedFields = 0;
            foreach ($requiredFields as $field) {
                if (array_key_exists($field, $fields) && $fields[$field] !== "") {
                    $validatedFields++;
                } else {
                    $this->response->setDetails([$field => "This field is required"]);
                }

            }
            if ($validatedFields === count($requiredFields)) {
                return true;
            }

            return $this->response->getDetails() ? $this->response->buildResponse() : true;
        }
    }

    /**
     * Get the value of active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @return  self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
