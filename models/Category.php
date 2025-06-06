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

class Category
{
    private $message;
    private $status;
    private $data;
    private $error;
    private $details = [];

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setMessage($message): void
    {
        $this->message = $message;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function setError($error): void
    {
        $this->error = $error;
    }

    public function setDetails($details): void
    {
        array_push($this->details, $details);
    }


    private function insert($name, $description = "")
    {

        if ($this->findByName($name)) {
            $this->setError("Conflict");
            $this->setMessage("This category does already exists");
            $this->setStatus("409");
            return $this->buildResponse();
        }

        $sql = "INSERT INTO CATEGORIES (name,description,active) VALUES (?,?,'1')";
        preparedQuerySQL($sql, "ss", $name, $description);
        $this->setError(null);
        $this->setStatus("201");
        $this->setMessage("Category created successfully");
        $this->setData($this->findByName($name));
        return $this->buildResponse();
    }

    private function edit($id, $name, $description,$active)
    {
        $dbCategory = $this->findById($id);
        if (!$dbCategory) {
            $this->setError("Category not found");
            $this->setStatus("404");
            $this->setMessage("This category cannot be updated because it does not exist");
            return $this->buildResponse();
        }
        $nameTaken = $this->findByName($name);
        if ($nameTaken) {
            if ($nameTaken[0]["name"] === $name && $dbCategory["id_category"] !== $nameTaken[0]["id_category"]) { // Checks if the name of the category is already taken
                $this->setError("Conflict");
                $this->setMessage("This category cannot be updated because it already exists");
                $this->setStatus("409");
                return $this->buildResponse();
            }
        }

        $sql = "UPDATE CATEGORIES SET NAME=?, description=?, active=? WHERE ID_CATEGORY=?";
        preparedQuerySQL($sql, "ssii", $name, $description,$active, $id);
        $this->setError(null);
        $this->setStatus("201");
        $this->setMessage("Category updated successfully");
        $this->setData($this->findById($id));
        return $this->buildResponse();
    }
    public function delete($id)
    {
        $dbCategory = $this->findById($id);
        if (!$dbCategory) {
            $this->setError("Category not found");
            $this->setStatus("404");
            $this->setMessage("This category cannot be deleted because it does not exist");
            return $this->buildResponse();
        }
        $sql = "DELETE FROM CATEGORIES WHERE ID_CATEGORY = ?";
        preparedQuerySQL($sql, "i", $id);
        $this->setError(null);
        $this->setStatus("204");
        $this->setMessage("Category deleted successfully");
        $this->setData(null);
        return $this->buildResponse();
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
                 return $this->edit($data["id"],$data["name"], $data["description"],$data["active"]);
            }
        }
        return $flag;
    }
    private function validateFields($fields, $method)
    {
        $this->setStatus("400");
        $this->setError("Bad Request");
        $this->setMessage("Required fields are missing");
        if ($method === "POST") {
            if (isset($fields["name"])) {
                if (!empty($fields["name"])) {
                    return true;
                }
            }
            $this->setDetails(['name' => "This field is required"]);
            return $this->buildResponse();
        } else {
            if (!isset($fields["id"])) $this->setDetails(['id' => "This field is required"]);
            if (!isset($fields["name"])) $this->setDetails(['name' => "This field is required"]);
            return $this->getDetails() ? $this->buildResponse() : true;
        }
    }

    private function buildResponse()
    {
        return [
            'error' => $this->getError(),
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'details' => !empty($this->getDetails()) ? $this->getDetails() : null,
            'data' => !empty($this->getData()) ? $this->getData() : null,
        ];
    }
}
