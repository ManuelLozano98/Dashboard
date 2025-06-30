<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author Usuario
 */
require_once '../configurations/database.php';
require_once 'Response.php';

class User
{
    private $id;
    private $name;
    private $username;
    private $email;
    private $password;
    private $phone;
    private $image;
    private $address;
    private $document;
    private $document_type;

    private $active;

    private Response $response;

    public function __construct($data = []) // If there is data, it is filled with the parameters provided; if not, it is set to null
    {
        $this->id = $data['id'] ?? "";
        $this->name = $data['name'] ?? "";
        $this->username = $data['username'] ?? "";
        $this->email = $data['email'] ?? "";
        $this->password = $data['password'] ?? "";
        $this->phone = $data['phone'] ?? "";
        $this->address = $data['address'] ?? "";
        $this->image = $data['image'] ?? "";
        $this->document = $data['document'] ?? "";
        $this->document_type = $data['document_type'] ?? "";
        $this->active = $data['active'] ?? 1;
    }

    public function checkLogin($params)
    {
        $response = new Response();
        $login = $params["login"];
        $password = $params["password"];

        $data = $this->isEmail($login)
            ? $this->findByEmail($login)
            : $this->findByUsername($login);

        if ($data && $this->checkPassword($password, $data["password"])) {
            $response->getValidLoginMessage();
        } else {
            $response->getInvalidLoginMessage();
        }

        return $response;
    }
    private function checkPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    private function isEmail($param)
    {
        return filter_var($param, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function insert($user)
    {
        $this->response = new Response();
        if ($this->findByUsername($user["username"]) || $this->findByEmail($user["email"])) {
            $this->response->getAddConflictMessage("User");
            return $this->response->buildResponse();

        }
        $hash = password_hash($user["password"], PASSWORD_BCRYPT);
        $newUser = new User($user);
        $newUser->setPassword($hash);
        $sql = "INSERT INTO USERS VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        preparedQuerySQLObject($sql, "isssssssssi", $newUser, ["id", "name", "email", "password", "username", "phone", "image", "address", "document", "document_type", "active"]);
        $this->response->getCreatedSuccesfullyMessage("User");
        $this->response->setData($newUser->getUserCreatedInfo());
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
            $uploadPath = '../files/users_img/' . $newName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                return $newName;
            } else {
                return "File couldnt move";
            }
        } else {
            return "Ext not allowed";
        }
    }

    public function edit($data)
    {

    }

    public function delete($id)
    {
        $dbUser = $this->findById($id);
        $this->response = new Response();
        if (!$dbUser) {
            $this->response->getDeletedNotFoundMessage("User");
            return $this->response->buildResponse();
        }
        $sql = "DELETE FROM USERS WHERE ID_USER = ?";
        preparedQuerySQL($sql, "i", $id);
        $this->response->getDeletedSuccesfullyMessage("User");
        $this->response->setData(null);
        return $this->response->buildResponse();
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM USERS WHERE ID_USER=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return $data ? $data[0] : null;
    }
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM USERS WHERE USERNAME=?";
        $data = getDataPreparedQuerySQL($sql, "s", $username);
        return $data ? $data[0] : null;
    }
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM USERS WHERE EMAIL=?";
        $data = getDataPreparedQuerySQL($sql, "s", $email);
        return $data ? $data[0] : null;
    }
    public function findByPhone($phone)
    {
        $sql = "SELECT * FROM USERS WHERE PHONE=?";
        $data = getDataPreparedQuerySQL($sql, "s", $phone);
        return $data ? $data[0] : null;
    }
    public function findByDocument($document)
    {
        $sql = "SELECT * FROM USERS WHERE DOCUMENT=?";
        $data = getDataPreparedQuerySQL($sql, "s", $document);
        return $data ? $data[0] : null;
    }

    public function getUserCreatedInfo()
    {
        return [
            "name" => $this->getName(),
            "username" => $this->getUsername(),
        ];
    }

    public function getCountUsers()
    {
        $sql = "SELECT COUNT(*) AS RECORDS FROM USERS";
        return querySQL($sql);
    }
    public function findByName($name)
    {
        $sql = "SELECT * FROM USERS WHERE NAME = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $name);
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
        $response = new Response();
        $response->getRequiredFieldsMissingMessage();
        if ($method === "POST") {
            $requiredFields = ["name", "email", "password", "username"];
            $validatedFields = 0;
            foreach ($requiredFields as $field) {
                if (array_key_exists($field, $fields) && $fields[$field] !== "") {
                    $validatedFields++;
                } else {
                    $response->setDetails([$field => "This field is required"]);
                }

            }
            if ($validatedFields === count($requiredFields)) {
                return true;
            }
            return $response->buildResponse();
        } else {
            $requiredFields = ["id_user", "name", "email", "password", "phone", "document"];
            $validatedFields = 0;
            foreach ($requiredFields as $field) {
                if (array_key_exists($field, $fields) && $fields[$field] !== "") {
                    $validatedFields++;
                } else {
                    $response->setDetails([$field => "This field is required"]);
                }

            }
            if ($validatedFields === count($requiredFields)) {
                return true;
            }

            return $response->getDetails() ? $response->buildResponse() : true;
        }
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
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
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
     * Get the value of document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set the value of document
     *
     * @return  self
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the value of address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
    /**
     * Get the value of document_type
     */
    public function getDocumentType()
    {
        return $this->document_type;
    }

    /**
     * Set the value of document_type
     *
     * @return  self
     */
    public function setDocumentType($document_type)
    {
        $this->document_type = $document_type;

        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
}

