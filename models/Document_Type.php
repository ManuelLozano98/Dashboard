<?php
require_once __DIR__.'/../configurations/database.php';
class Document_Type
{
    private ?int $id;
    private string $name;


    public function __construct($data = [])
    {
        $this->id = $data['id_document_type'] ?? NULL;
        $this->name = $data['name'] ?? "";
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

    public static function getAll()
    {
        $sql = "SELECT * FROM DOCUMENT_TYPES";
        $query = querySQL($sql);
        $documents = [];
        foreach ($query as $document) {
            $documents[] = new Document_Type($document);
        }
        return $documents;
    }
     public static function findById($id)
    {
        $sql = "SELECT * FROM DOCUMENT_TYPES WHERE ID_DOCUMENT_TYPE=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new Document_Type($data[0]) : false;

    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];

    }
}