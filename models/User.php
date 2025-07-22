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
require_once __DIR__ . '/../configurations/database.php';


class User
{
    private ?int $id;
    private string $name;
    private string $username;
    private string $email;
    private string $password;
    private string $phone;
    private string $image;
    private string $address;
    private string $document;
    private int $document_type;
    private string $token;
    private string $tokenExpiredAt;

    private int $active;
    private string $registration_date;


    public function __construct($data = []) // If there is data, it is filled with the parameters provided; if not, it is set to null
    {
        $this->id = $data['id_user'] ?? NULL;
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
        $this->registration_date = empty($data['registration_date']) ? (new Datetime("now"))->format('Y-m-d H:i:s') : $data['registration_date'];
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM USERS";
        $query = querySQL($sql);
        $users = [];
        foreach ($query as $user) {
            $users[] = new User($user);
        }
        return $users;
    }

    public static function insert(User $user)
    {
        $sql = "INSERT INTO USERS VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        return preparedQuerySQLObject($sql, "issssssssiisss", $user, ["id", "name", "email", "password", "username", "phone", "image", "address", "document", "document_type", "active", "token", "tokenExpiredAt", "registration_date"]) ? $user : false;
    }


    public static function activateAccount(User $user)
    {
        $sql = "UPDATE USERS SET ACTIVE = ?, VERIFICATION_TOKEN = ? WHERE ID_USER = ?";
        return preparedQuerySQL($sql, "isi", 1, NULL, $user->getId());
    }

    public static function edit(User $user)
    {

        $sql = "UPDATE USERS SET NAME=?, EMAIL=?, PASSWORD=?, PHONE=?, IMAGE=?, ADDRESS=?, DOCUMENT=?, ID_DOCUMENT_TYPE=?, ACTIVE=? WHERE ID_USER=?";
        return preparedQuerySQLObject($sql, "sssssssiii", $user, ["name", "email", "password", "phone", "image", "address", "document", "document_type", "active", "id"]) ? $user : false;
    }

    public static function delete($id)
    {
        $sql = "DELETE FROM USERS WHERE ID_USER = ?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM USERS WHERE ID_USER=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new User($data[0]) : false;

    }
    public static function findByUsername($username)
    {
        $sql = "SELECT * FROM USERS WHERE USERNAME = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $username);
        return !empty($data) ? new User($data[0]) : false;
    }
    public static function findByEmail($email)
    {
        $sql = "SELECT * FROM USERS WHERE EMAIL=?";
        $data = getDataPreparedQuerySQL($sql, "s", $email);
        return !empty($data) ? new User($data[0]) : false;
    }
    public static function findByPhone($phone)
    {
        $sql = "SELECT * FROM USERS WHERE PHONE=?";
        $data = getDataPreparedQuerySQL($sql, "s", $phone);
        return !empty($data) ? new User($data[0]) : false;
    }
    public static function findByDocument($document)
    {
        $sql = "SELECT * FROM USERS WHERE DOCUMENT=?";
        $data = getDataPreparedQuerySQL($sql, "s", $document);
        return !empty($data) ? new User($data[0]) : false;
    }
    public static function findByToken($token)
    {
        $sql = "SELECT * FROM USERS WHERE VERIFICATION_TOKEN=?";
        $data = getDataPreparedQuerySQL($sql, "s", $token);
        return !empty($data) ? new User($data[0]) : false;
    }


    public static function getCountUsers()
    {
        $sql = "SELECT COUNT(*) AS RECORDS FROM USERS";
        return querySQL($sql);
    }
    public static function findByName($name)
    {
        $sql = "SELECT * FROM USERS WHERE NAME = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $name);
        $users = [];
        foreach ($data as $user) {
            $users[] = new User($user);
        }
        return $users;
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'phone' => $this->phone,
            'image' => $this->image,
            'address' => $this->address,
            'document' => $this->document,
            'document_type' => $this->document_type,
            'active' => $this->active,
            'verification_token' => $this->token,
            'tokenExpiredAt' => $this->tokenExpiredAt,
            'registration_date' => $this->registration_date,
        ];

    }
}

