<?php
class Sale
{
    private ?int $id;
    private int $id_user;
    private float $total_amount;
    private string $payment_method;
    private string $status;
    private string $created_at;
    private ?string $updated_at;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->id_user = $data['id_user'] ?? 0;
        $this->total_amount = $data['total_amount'] ?? 0.00;
        $this->payment_method = $data['payment_method'] ?? '';
        $this->status = $data['status'] ?? 'pending';
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM sales";
        $query = querySQL($sql);
        $sales = [];
        foreach ($query as $row) {
            $sales[] = new Sale($row);
        }
        return $sales;
    }

    public static function insert($sale)
    {
        $sql = "INSERT INTO sales (id_user, total_amount, payment_method, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?)";
        $result = preparedQuerySQL(
            $sql,
            "idssss",
            $sale->getUserId(),
            $sale->getTotalAmount(),
            $sale->getPaymentMethod(),
            $sale->getStatus(),
            $sale->getCreatedAt(),
            $sale->getUpdatedAt()
        );
        if ($result) {
            $sale->setId(getId());
        }
        return $result;
    }

    public static function edit($sale)
    {
        $sql = "UPDATE sales SET id_user=?, total_amount=?, payment_method=?, status=?, updated_at=? WHERE id=?";
        return preparedQuerySQL(
            $sql,
            "idsssi",
            $sale->getUserId(),
            $sale->getTotalAmount(),
            $sale->getPaymentMethod(),
            $sale->getStatus(),
            $sale->getUpdatedAt(),
            $sale->getId()
        );
    }

    public static function delete($id)
    {
        $sql = "DELETE FROM sales WHERE id=?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM sales WHERE id=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new Sale($data[0]) : false;
    }
    public function getItems()
    {
        if (!$this->id) {
            return [];
        }

        $sql = "SELECT * FROM sale_items WHERE sale_id = ?";
        $data = getDataPreparedQuerySQL($sql, "i", $this->id);

        $items = [];
        foreach ($data as $row) {
            $items[] = new SaleItem($row);
        }

        return $items;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'id_user' => $this->id_user,
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function getId()
    {
        return $this->id;
    }
    public function getUserId()
    {
        return $this->id_user;
    }
    public function getTotalAmount()
    {
        return $this->total_amount;
    }
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setUserId($id_user)
    {
        $this->id_user = $id_user;
    }
    public function setTotalAmount($amount)
    {
        $this->total_amount = $amount;
    }
    public function setPaymentMethod($method)
    {
        $this->payment_method = $method;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function setCreatedAt($datetime)
    {
        $this->created_at = $datetime;
    }
    public function setUpdatedAt($datetime)
    {
        $this->updated_at = $datetime;
    }
}
