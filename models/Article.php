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
require_once __DIR__ . '/../configurations/database.php';

class Article
{
    private ?int $id = NULL;
    private string $name;
    private string $description;
    private ?int $active;
    private string $code;
    private string $image;
    private int $stock;
    private string $price;

    private ?int $id_category;
    private string $created_at;


    function __construct($data = [])
    {
        $this->id = $data['id_article'] ?? NULL;
        $this->name = $data['name'] ?? "";
        $this->description = $data['description'] ?? "";
        $this->active = $data['active'] ?? 1;
        $this->code = $data['code'] ?? "";
        $this->image = $data['image'] ?? "";
        $this->stock = $data['stock'] ?? 0;
        $this->price = $data['price'] ?? "";
        $this->id_category = $data['id_category'] ?? NULL;
        $this->created_at = empty($data['created_at']) ? (new Datetime("now"))->format('Y-m-d H:i:s') : $data['created_at'];
    }

    public static function getAll()
    {
        $sql = "SELECT a.*, c.name as category_name FROM ARTICLES a INNER JOIN categories c ON a.id_category = c.id_category";
        $query = querySQL($sql);
        $articles = [];
        foreach ($query as $row) {
            $article = new Article($row);
            $articles[] = [
                'article' => $article,
                'category_name' => $row['category_name']
            ];
        }
        return $articles;
    }

    public static function getCountArticles()
    {
        $sql = "SELECT COUNT(*) AS RECORDS FROM ARTICLES";
        return querySQL($sql);
    }


    public static function insert(Article $article)
    {
        $sql = "INSERT INTO ARTICLES (name, image, description, code, price, stock, active, id_category, id_article,created_at) VALUES (?,?,?,?,?,?,?,?,?,?)";
        return preparedQuerySQL($sql, "sssssiiiis", $article->getName(), $article->getImage(), $article->getDescription(), $article->getCode(), $article->getPrice(), $article->getStock(), $article->getActive(), $article->getIdCategory(), $article->getId(),$article->getCreatedAt()) ? $article->setId(getId()) : false;

    }


    public static function edit(Article $article)
    {

        $sql = "UPDATE ARTICLES SET NAME=?, IMAGE=?, DESCRIPTION=?, CODE=?, PRICE=?, STOCK=?, ACTIVE=?, ID_CATEGORY=? WHERE ID_ARTICLE=?";
        return preparedQuerySQL($sql, "sssssiiii", $article->getName(), $article->getImage(), $article->getDescription(), $article->getCode(), $article->getPrice(), $article->getStock(), $article->getActive(), $article->getIdCategory(), $article->getId()) ? $article : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM ARTICLES WHERE ID_ARTICLE = ?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM ARTICLES WHERE ID_ARTICLE=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new Article($data[0]) : false;
    }

    public static function findByName($name)
    {
        $sql = "SELECT * FROM ARTICLES WHERE NAME = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $name);
        return !empty($data) ? new Article($data[0]) : false;
    }

    public static function findByCode($code)
    {
        $sql = "SELECT * FROM ARTICLES WHERE CODE = ?";
        $data = getDataPreparedQuerySQL($sql, "s", $code);
        return !empty($data) ? new Article($data[0]) : false;
    }

    public static function getNewArticlesCount(){
        $sql = "SELECT COUNT(id_article) as 'Total', DATE(created_at) as 'Date'
        FROM articles
        WHERE DATE(created_at) > DATE(NOW() - INTERVAL 1 DAY)";
        $data = querySQL($sql);
        return $data;
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

    /**
     * Get the value of code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @return  self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set the value of stock
     *
     * @return  self
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getIdCategory()
    {
        return $this->id_category;
    }

    /**
     * Set the value of id_category
     *
     * @return  self
     */
    public function setIdCategory($id_category)
    {
        $this->id_category = $id_category;

        return $this;
    }

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
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }


    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'image' => $this->image,
            'price' => $this->price,
            'stock' => $this->stock,
            'id_category' => $this->id_category,
            'active' => $this->active,
            'created_at' => $this->created_at
        ];

    }
}
