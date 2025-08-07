<?php
class SaleItem
{
    private ?int $id;
    private ?int $id_sale;
    private int $id_article;
    private int $quantity;
    private float $price;
    private float $subtotal;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->id_sale = $data['id_sale'] ?? 0;
        $this->id_article = $data['id_article'] ?? 0;
        $this->quantity = $data['quantity'] ?? 0;
        $this->price = $data['price'] ?? 0.0;
        $this->subtotal = $data['subtotal'] ?? 0.0;
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM sale_items";
        $query = querySQL($sql);
        $items = [];
        foreach ($query as $row) {
            $items[] = new SaleItem($row);
        }
        return $items;
    }

    public static function insert($item)
    {
        $sql = "INSERT INTO sale_items (id_sale, id_article, quantity, price, subtotal)
                VALUES (?, ?, ?, ?, ?)";
        $result = preparedQuerySQL(
            $sql,
            "iiidd",
            $item->getSaleId(),
            $item->getArticleId(),
            $item->getQuantity(),
            $item->getPrice(),
            $item->getSubtotal()
        );
        if ($result) {
            $item->setId(getId());
        }
        return $result;
    }

    public static function edit($item)
    {
        $sql = "UPDATE sale_items SET id_sale=?, id_article=?, quantity=?, price=?, subtotal=? WHERE id=?";
        return preparedQuerySQL(
            $sql,
            "iiiddi",
            $item->getSaleId(),
            $item->getArticleId(),
            $item->getQuantity(),
            $item->getPrice(),
            $item->getSubtotal(),
            $item->getId()
        );
    }

    public static function delete($id)
    {
        $sql = "DELETE FROM sale_items WHERE id=?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM sale_items WHERE id=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new SaleItem($data[0]) : false;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'id_sale' => $this->id_sale,
            'id_article' => $this->id_article,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal
        ];
    }


    public function getId()
    {
        return $this->id;
    }
    public function getSaleId()
    {
        return $this->id_sale;
    }
    public function getArticleId()
    {
        return $this->id_article;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setSaleId($id_sale)
    {
        $this->id_sale = $id_sale;
    }
    public function setProductId($id_article)
    {
        $this->id_article = $id_article;
    }
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
    }
}
