<?php
class News
{
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
            n.content AS content,
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
            n.content AS content,
            n.created_at AS created_at, n.posted_by AS poster_id,
            u.login AS poster_login
            FROM news n LEFT JOIN users u
            ON n.posted_by=u.id
            WHERE n.id=:id
        ");

        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_OBJ);
    }
}
