<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Category
 *
 * @author Usuario
 */
require_once '../configurations/database.php';
require_once 'Response.php';

class Category
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


    private function insert($name, $description = "")
    {
        if ($this->findByName($name)) {
            $this->response->getAddConflictMessage("Category");
            return $this->response->buildResponse();
        }

        $sql = "INSERT INTO CATEGORIES (name,description,active) VALUES (?,?,'1')";
        preparedQuerySQL($sql, "ss", $name, $description);
        $this->response->getCreatedSuccesfullyMessage("Category");
        $this->response->setData($this->findByName($name));
        return $this->response->buildResponse();
    }

    private function edit($id, $name, $description, $active)
    {
        $dbCategory = $this->findById($id);
        if (!$dbCategory) {
            $this->response->getUpdatedNotFoundMessage("Category");
            return $this->response->buildResponse();
        }
        $nameTaken = $this->findByName($name);
        if ($nameTaken) {
            if ($nameTaken[0]["name"] === $name && $dbCategory["id_category"] !== $nameTaken[0]["id_category"]) { // Checks if the name of the category is already taken
                $this->response->getUpdatedExistsMessage(("Category"));
                return $this->response->buildResponse();
            }
        }

        $sql = "UPDATE CATEGORIES SET NAME=?, description=?, active=? WHERE ID_CATEGORY=?";
        preparedQuerySQL($sql, "ssii", $name, $description, $active, $id);
        $this->response->getUpdatedSuccesfullyMessage("Category");
        $this->response->setData($this->findById($id));
        return $this->response->buildResponse();
    }
    public function delete($id)
    {
        $dbCategory = $this->findById($id);
        if (!$dbCategory) {
            $this->response->getDeletedNotFoundMessage("Category");
            return $this->response->buildResponse();
        }
        $sql = "DELETE FROM CATEGORIES WHERE ID_CATEGORY = ?";
        preparedQuerySQL($sql, "i", $id);
        $this->response->getDeletedSuccesfullyMessage("Category");
        return $this->response->buildResponse();
    }


    public function disable($id)
    {
        $sql = "UPDATE CATEGORIES SET ACTIVE='0' WHERE ID_CATEGORY=?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public function enable($id)
    {
        $sql = "UPDATE CATEGORIES SET ACTIVE='1' WHERE ID_CATEGORY=?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM CATEGORIES WHERE ID_CATEGORY=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return $data ? $data[0] : null;
    }


    public function getCategories()
    {
        $sql = "SELECT * FROM CATEGORIES";
        $records = $this->getCountCategories();
        $query = querySQL($sql);
        $data = array(
            "records" => (int) $records[0]["RECORDS"],
            "data" => $query
        );
        return $data;
    }
    public function getCategoriesName(){
        $sql = "SELECT NAME FROM CATEGORIES";
        $records = $this->getCountCategories();
        $query = querySQL($sql);
        $data = array(
            "records" => (int) $records[0]["RECORDS"],
            "data" => $query
        );
        return $data;
    }

    public function getCountCategories()
    {
        $sql = "SELECT COUNT(*) AS RECORDS FROM CATEGORIES";
        return querySQL($sql);
    }
    public function findByName($name)
    {
        $sql = "SELECT * FROM CATEGORIES WHERE NAME = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $name);
        return $data;
    }
    public function insertOrEdit($data, $method)
    {
        $flag = $this->validateFields($data, $method);
        isset($data["description"]) ? $data["description"] : $data["description"] = ""; //Optional param
        if ($flag === TRUE) {
            if ($method === "POST") {
                return $this->insert($data["name"], $data["description"]);
            } else {
                return $this->edit($data["id"], $data["name"], $data["description"], $data["active"]);
            }
        }
        return $flag;
    }
    private function validateFields($fields, $method)
    {
        $this->response->getRequiredFieldsMissingMessage();
        if ($method === "POST") {
            if (isset($fields["name"])) {
                if (!empty($fields["name"])) {
                    return true;
                }
            }
            $this->response->setDetails(['name' => "This field is required"]);
            return $this->response->buildResponse();
        } else {
            if (!isset($fields["id"]))
                $this->response->setDetails(['id' => "This field is required"]);
            if (!isset($fields["name"]))
                $this->response->setDetails(['name' => "This field is required"]);
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
