<?php
require_once '../configurations/database.php';
class Document_Type
{
    private $id;
    private $name;


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

    public function getAll()
    {
        $sql = "SELECT ID_DOCUMENT_TYPE, NAME FROM DOCUMENT_TYPES";
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
}