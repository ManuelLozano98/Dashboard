<?php
class ArticleReview
{
    private ?int $id;
    private int $id_article;
    private int $id_user;
    private string $title;
    private string $comment;
    private float $rating;
    private string $created_at;
    private ?string $updated_at;
    private bool $active;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->id_article = $data['id_article'] ?? 0;
        $this->id_user = $data['id_user'] ?? 0;
        $this->title = $data['title'] ?? '';
        $this->comment = $data['comment'] ?? '';
        $this->rating = $data['rating'] ?? 0.0;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? null;
        $this->active = $data['active'] ?? true;
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM article_reviews";
        $query = querySQL($sql);
        $reviews = [];
        foreach ($query as $row) {
            $reviews[] = new ArticleReview($row);
        }
        return $reviews;
    }

    public static function insert(ArticleReview $review)
    {
        $sql = "INSERT INTO article_reviews (id_article, id_user, title, comment, rating, created_at, updated_at, active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $result = preparedQuerySQL(
            $sql,
            "iissdssi",
            $review->getArticleId(),
            $review->getUserId(),
            $review->getTitle(),
            $review->getComment(),
            $review->getRating(),
            $review->getCreatedAt(),
            $review->getUpdatedAt(),
            $review->getActive() ? 1 : 0
        );
        if ($result) {
            $review->setId(getId());
        }
        return $result;
    }

    public static function edit(ArticleReview $review)
    {
        $sql = "UPDATE article_reviews SET id_article=?, id_user=?, title=?, comment=?, rating=?, updated_at=?, active=? WHERE id=?";
        return preparedQuerySQL(
            $sql,
            "iissdsii",
            $review->getArticleId(),
            $review->getUserId(),
            $review->getTitle(),
            $review->getComment(),
            $review->getRating(),
            $review->getUpdatedAt(),
            $review->getActive() ? 1 : 0,
            $review->getId()
        );
    }

    public static function delete(int $id)
    {
        $sql = "DELETE FROM article_reviews WHERE id=?";
        return preparedQuerySQL($sql, "i", $id);
    }

    public static function findById(int $id)
    {
        $sql = "SELECT * FROM article_reviews WHERE id=?";
        $data = getDataPreparedQuerySQL($sql, "i", $id);
        return !empty($data) ? new ArticleReview($data[0]) : false;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'id_article' => $this->id_article,
            'id_user' => $this->id_user,
            'title' => $this->title,
            'comment' => $this->comment,
            'rating' => $this->rating,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'active' => $this->active
        ];
    }



    public function getId()
    {
        return $this->id;
    }
    public function getArticleId()
    {
        return $this->id_article;
    }
    public function getUserId()
    {
        return $this->id_user;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getComment()
    {
        return $this->comment;
    }
    public function getRating()
    {
        return $this->rating;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    public function getActive()
    {
        return $this->active;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }
    public function setArticleId(int $id)
    {
        $this->id_article = $id;
    }
    public function setUserId(int $id)
    {
        $this->id_user = $id;
    }
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }
    public function setRating(float $rating)
    {
        $this->rating = $rating;
    }
    public function setUpdatedAt(?string $date)
    {
        $this->updated_at = $date;
    }
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

}
