<?php
class Comments
{
    public static function create($newsId, $message, $parentId = false)
    {
        $sql = "INSERT INTO news_comments (news_id, message, parent_id, author_id)
        VALUES (:news_id, :message, :parent_id, :author_id)";
        $authorId = Session::getUid();
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':news_id', $newsId, PDO::PARAM_INT);
        $statement->bindParam(':message', $message, PDO::PARAM_STR);
        if ($parentId) {
            $statement->bindParam(':parent_id', $parentId, PDO::PARAM_INT);
        } else {
            $parentId = null;
            $statement->bindParam(':parent_id', $parentId, PDO::PARAM_NULL);
        }
        if ($authorId !== 0) {
            $statement->bindParam(':author_id', $authorId, PDO::PARAM_INT);
        } else {
            $authorId = null;
            $statement->bindParam(':author_id', $authorId, PDO::PARAM_NULL);
        }

        return $statement->execute();
    }

    public static function read($id)
    {
        $sql = "SELECT * FROM news_comments WHERE id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $newsId, $message, $parentId = false)
    {
        $sql = "UPDATE news_comments SET news_id=:news_id, message=:message, parent_id=:parent_id WHERE id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':news_id', $newsId, PDO::PARAM_INT);
        $statement->bindParam(':message', $message, PDO::PARAM_STR);
        if ($parentId) {
            $statement->bindParam(':parent_id', $parentId, PDO::PARAM_INT);
        } else {
            $parentId = null;
            $statement->bindParam(':parent_id', $parentId, PDO::PARAM_NULL);
        }

        return $statement->execute();
    }

    public static function delete($id)
    {
        if (is_array($id)) {
            return Database::getInstance()
                ->prepare("DELETE FROM news_comments WHERE id IN (?)")
                ->execute($id);
        } else {
            return Database::getInstance()
                ->prepare("DELETE FROM news_comments WHERE id=?")
                ->execute(array($id));
        }
    }

    public static function listingForNewsId($newsId, $offset = 0, $limit = 30)
    {
        $statement = Database::getInstance()->prepare("
            SELECT c.id, c.message, u.login, c.posted_at
            FROM news_comments AS c
            LEFT JOIN users AS u
            ON u.id = c.author_id
            WHERE c.news_id =:news_id
            LIMIT :limit OFFSET :offset
        ");

        $statement->bindParam(':news_id', $newsId, PDO::PARAM_INT);
        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
} 