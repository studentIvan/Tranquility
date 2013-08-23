<?php
class Comments
{
    public static function create($newsId, $message)
    {
        $sql = "INSERT INTO news_comments (news_id, message, author_id, posted_at)
          VALUES (:news_id, :message, :author_id, NOW())";
        $authorId = Session::getUid();
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':news_id', $newsId, PDO::PARAM_INT);
        $statement->bindParam(':message', $message, PDO::PARAM_STR);

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

    public static function update($id, $newsId, $message)
    {
        $sql = "UPDATE news_comments SET news_id=:news_id, message=:message WHERE id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':news_id', $newsId, PDO::PARAM_INT);
        $statement->bindParam(':message', $message, PDO::PARAM_STR);

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

    public static function listingForNewsId($newsId)
    {
        $statement = Database::getInstance()->prepare("
            SELECT c.id, c.message,
            u.id as poster_id, u.login as poster_login,
            c.posted_at, d.nickname as poster_nick,
            d.full_name as poster_full_name,
            d.photo as poster_photo
            FROM news_comments AS c
            LEFT JOIN users AS u
            ON u.id = c.author_id
            LEFT JOIN users_data AS d
            ON d.user_id = u.id
            WHERE c.news_id =:news_id
            ORDER BY c.posted_at DESC
        ");

        $statement->bindParam(':news_id', $newsId, PDO::PARAM_INT);
        $statement->execute();
        $comments = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comments as $key => $value) {
            $comments[$key]['message'] = preg_replace('/(@.+?),/u',
                '<span style="font-weight: bold; font-style: italic; color: rgb(42, 100, 150);">$1</span>,', $value['message']);
        }

        return $comments;
    }
} 