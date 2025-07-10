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
require_once 'Mail.php';


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
    private $token;
    private $tokenExpiredAt;

    private $active;
    private $registration_date;

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
        $this->document_type = $data['document_type'] ?? 1;
        $this->active = $data['active'] ?? 0;
        $this->token = $data['verification_token'] ?? "";
        $this->tokenExpiredAt = $data['token_expires_at'] ?? "";
        $this->registration_date = (new Datetime("now"))->format('Y-m-d H:i:s');
    }

    public function getUsers()
    {
        $sql = "SELECT ID_USER,NAME,EMAIL,USERNAME,PHONE,IMAGE,ADDRESS,DOCUMENT,ID_DOCUMENT_TYPE,ACTIVE,VERIFICATION_TOKEN,TOKEN_EXPIRES_AT,REGISTRATION_DATE FROM USERS";
        $records = $this->getCountUsers();
        $query = querySQL($sql);
        $data = array(
            "records" => (int) $records[0]["RECORDS"],
            "data" => $query
        );
        return $data;
    }

    private function generateToken(User $user)
    {
        $user->setToken(bin2hex(random_bytes(32)));
        $user->setTokenExpiredAt((new DateTime('+1 day'))->format('Y-m-d H:i:s'));
    }

    public function checkLogin($params)
    {
        $response = new Response();
        $login = $params["login"];
        $password = $params["password"];

        $data = $this->isEmail($login)
            ? $this->findByEmail($login)
            : $this->findByUsername($login);

        if ($data && $data["active"] === 1 && $this->checkPassword($password, $data["password"])) {
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
            return $this->response;

        }
        $hash = password_hash($user["password"], PASSWORD_BCRYPT);
        $newUser = new User($user);
        $newUser->setPassword($hash);
        $this->generateToken($newUser);
        $sql = "INSERT INTO USERS VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if (preparedQuerySQLObject($sql, "issssssssiisss", $newUser, ["id", "name", "email", "password", "username", "phone", "image", "address", "document", "document_type", "active", "token", "tokenExpiredAt", "registration_date"])) {
            $this->response->getCreatedSuccesfullyMessage("User");
            $this->response->setData($newUser->getUserCreatedInfo());
        } else {
            $this->response->setMessage("An error occurred, registration could not be completed");
        }
        if (Mail::sendEmailVerification($newUser->getEmail(), $newUser->getUsername(), $newUser->getToken())) {
            $this->response->setDetails(["email" => "Email sent successfully"]);
        } else {
            $this->response->setDetails(["email" => "Email could not be sent. Mailer Error"]);
        }

        return $this->response;
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
        $response = new Response();
        $dbUser = $this->findById($data["id_user"]);
        if (!$dbUser) {
            return $response->getUpdatedNotFoundMessage("User");
        }
        $emailTaken = $this->findByEmail($data["email"]);
        if ($emailTaken) {
            if (($emailTaken["email"] === $data["email"] && $dbUser["id_user"] !== $emailTaken["id_user"])) {
                $response->getAddConflictMessage("user");
                return $response;
            }
        }

        $img = $dbUser["image"];
        if (isset($_FILES["image"]) && $_FILES["image"]['error'] === UPLOAD_ERR_OK) {
            $img = $this->saveImage($_FILES["image"]);
        }

        $set = "";
        $types = "";
        $password = "";
        $email = "";

        $user = new User($data);
        $user->setId($dbUser["id_user"]);
        $user->setImage($img);

        if ($data["password"] !== "") {
            $set = "PASSWORD=?, ";
            $types = "s";
            $password = "password";
            $hash = password_hash($data["password"], PASSWORD_BCRYPT);
            $user->setPassword($hash);

        }
        if (!empty($data["email"] && $data["email"] !== $dbUser["email"])) {
            $set .= "EMAIL=?, ";
            $types .= "s";
            $email = "email";
        }

        $sql = "UPDATE USERS SET NAME=?, " . $set . "PHONE=?, IMAGE=?, ADDRESS=?, DOCUMENT=?, ID_DOCUMENT_TYPE=?, ACTIVE=? WHERE ID_USER=?";
        $properties = ["name"];
        if ($password !== "")
            array_push($properties, $password);
        if ($email !== "")
            array_push($properties, $email);
        array_push($properties, "phone", "image", "address", "document", "document_type", "active", "id");

        if (preparedQuerySQLObject($sql, "s" . $types . "ssssiii", $user, $properties)) {
            $response->getUpdatedSuccesfullyMessage("User");
            $response->setData($user);
        } else {
            $response->getUpdatedExistsMessage("users");
        }
        return $response;

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
    public function findByToken($token)
    {
        $sql = "SELECT * FROM USERS WHERE VERIFICATION_TOKEN=?";
        $data = getDataPreparedQuerySQL($sql, "s", $token);
        return $data ? $data[0] : null;
    }
    public function verifyToken($token)
    {
        $response = new Response();
        $response->setMessage("The token has already expired");
        $response->setStatus(409);
        $user = $this->findByToken($token);
        $date = (new DateTime('now'))->format('Y-m-d H:i:s');
        if ($user && !$user["active"] && $user["token_expires_at"] > $date) {
            $sql = "UPDATE USERS SET ACTIVE = 1, VERIFICATION_TOKEN = NULL WHERE ID_USER = ?";
            if (preparedQuerySQL($sql, "i", $user["id_user"])) {
                $response->setMessage("The user has been successfully verified");
                $response->setStatus(201);

            }
        }
        return $response;
    }

    public function getUserCreatedInfo()
    {
        return [
            "name" => $this->getName(),
            "username" => $this->getUsername(),
            "email" => $this->getEmail()
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
            $requiredFields = ["id_user"];
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

    /**
     * Get the value of token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @return  self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of tokenExpiredAt
     */
    public function getTokenExpiredAt()
    {
        return $this->tokenExpiredAt;
    }

    /**
     * Set the value of tokenExpiredAt
     *
     * @return  self
     */
    public function setTokenExpiredAt($tokenExpiredAt)
    {
        $this->tokenExpiredAt = $tokenExpiredAt;

        return $this;
    }

    /**
     * Get the value of registration_date
     */
    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    /**
     * Set the value of registration_date
     *
     * @return  self
     */
    public function setRegistrationDate($registration_date)
    {
        $this->registration_date = $registration_date;

        return $this;
    }
}

