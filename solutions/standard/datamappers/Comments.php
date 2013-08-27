<?php
class Comments
{
    public static function create($newsId, $message)
    {
        /**
         * @TODO message compare
         */
        $authorId = Session::getUid();
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $sql = "SELECT MAX(posted_at) FROM news_comments WHERE ip=INET_ATON(:ip)";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':ip', $ip, PDO::PARAM_STR);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            $lastPosted = $statement->fetchColumn();
            $period = time() - strtotime($lastPosted);
            if ($period < 10) {
                return false;
            }
        }

        $sql = "INSERT INTO news_comments (news_id, message, author_id, posted_at, ip)
          VALUES (:news_id, :message, :author_id, NOW(), INET_ATON(:ip))";

        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':news_id', $newsId, PDO::PARAM_INT);
        $statement->bindParam(':message', $message, PDO::PARAM_STR);
        $statement->bindParam(':ip', $ip, PDO::PARAM_STR);

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
            COALESCE(INET_NTOA(c.ip), 0, '127.0.0.1') as poster_ip,
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
            $comments[$key]['message'] = self::prepare($value['message']);
        }

        return $comments;
    }

    /**
     * @param string $message
     * @return string
     */
    public static function prepare($message)
    {
        return preg_replace('/(@.+?),/u', '<span class="comment-nick">$1</span>,', $message);
    }
} 