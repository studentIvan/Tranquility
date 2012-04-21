<?php
class News
{
    /**
     * @static
     * @param int $authorId
     * @param string $title
     * @param string $content
     * @param string $tags
     * @return bool
     */
    public static function create($authorId, $title, $content, $tags)
    {
        $sql = "INSERT INTO news (title, content, tags, created_at, posted_by)
                    VALUES (:title, :content, :tags, NOW(), :posted_by)";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':content', $content, PDO::PARAM_STR);
        $statement->bindParam(':tags', $tags, PDO::PARAM_STR);
        $statement->bindParam(':posted_by', $authorId, PDO::PARAM_INT);
        return $statement->execute();
    }

    /**
     * @static
     * @param int $id
     * @param string $title
     * @param string $content
     * @param string $tags
     * @return bool
     */
    public static function edit($id, $title, $content, $tags)
    {
        $sql = "UPDATE news SET title=:title, content=:content, tags=:tags WHERE id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':content', $content, PDO::PARAM_STR);
        $statement->bindParam(':tags', $tags, PDO::PARAM_STR);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        return $statement->execute();
    }

    /**
     * @static
     * @param int|array $id
     * @return bool
     */
    public static function remove($id)
    {
        if (is_array($id)) {
            return Database::getInstance()
                ->prepare("DELETE FROM news WHERE id IN (?)")
                ->execute($id);
        } else {
            return Database::getInstance()
                ->prepare("DELETE FROM news WHERE id=?")
                ->execute(array($id));
        }
    }

    /**
     * @static
     * @param int $offset
     * @param bool|int $limit
     * @return array
     */
    public static function listing($offset = 0, $limit = false)
    {
        if (!$limit) $limit = Process::$context['cms']['news']['limit_per_page'];

        $statement = Database::getInstance()->prepare("
            SELECT n.id AS id, n.title AS title,
            n.content AS content, n.tags AS tags,
            n.created_at AS created_at, n.posted_by AS poster_id,
            u.login AS poster_login
            FROM news n LEFT JOIN users u
            ON n.posted_by=u.id
            ORDER BY n.created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @static
     * @param int $id
     * @return mixed
     */
    public static function getObjectById($id)
    {
        $statement = Database::getInstance()->prepare("
            SELECT n.id AS id, n.title AS title,
            n.content AS content, n.tags AS tags,
            n.created_at AS created_at, n.posted_by AS poster_id,
            u.login AS poster_login
            FROM news n LEFT JOIN users u
            ON n.posted_by=u.id
            ORDER BY n.created_at DESC
            WHERE n.id=:id
        ");

        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_OBJ);
    }
}
