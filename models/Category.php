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
    private $id;
    private $name;
    private $description;
    private $active;


    function __construct($data = [])
    {
        $this->id = $data['id_category'] ?? "";
        $this->name = $data['name'] ?? "";
        $this->description = $data['description'] ?? "";
        $this->active = $data['active'] ?? 1;

    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
        ];
    }

    public static function insert(Category $category)
    {

        $sql = "INSERT INTO CATEGORIES VALUES (?,?,?,?)";
        return preparedQuerySQL($sql, "issi", $category->getId(), $category->getName(), $category->getDescription(), $category->getActive()) ? $category->setId(getId()) : false;

    }

    public static function edit(Category $category)
    {

        $sql = "UPDATE CATEGORIES SET NAME=?, description=?, active=? WHERE ID_CATEGORY=?";
        return preparedQuerySQL($sql, "ssii", $category->getName(), $category->getDescription(), $category->getActive(), $category->getId()) ? $category : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM CATEGORIES WHERE ID_CATEGORY = ?";
        return preparedQuerySQL($sql, "i", $id);
    }


    public static function findById($id)
    {
        $sql = "SELECT * FROM CATEGORIES WHERE ID_CATEGORY=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new Category($data[0]) : false;

    }


    public static function getAll()
    {
        $sql = "SELECT * FROM CATEGORIES";
        $query = querySQL($sql);
        $categories = [];
        foreach ($query as $category) {
            $categories[] = new Category($category);
        }
        return $categories;

    }
    public static function findByIdAndName()
    {
        $sql = "SELECT id_category, name FROM CATEGORIES";
        $query = querySQL($sql);
        $categories = [];
        foreach ($query as $category) {
            $categories[] = new Category($category);
        }
        return $categories;
    }

    public static function findByName($name)
    {
        $sql = "SELECT * FROM CATEGORIES WHERE NAME = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $name);
        return !empty($data) ? new Category($data[0]) : false;
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
