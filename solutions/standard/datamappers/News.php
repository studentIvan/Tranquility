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
            u.login AS poster_login,
            GROUP_CONCAT(DISTINCT t.name SEPARATOR ',') AS tags,
            COUNT(DISTINCT c.id) AS comments_total
            FROM news n LEFT JOIN users u
            ON n.posted_by=u.id
            LEFT JOIN tags_relation as tr
            ON tr.news_id=n.id
            LEFT JOIN tags as t
            ON t.id=tr.tag_id
            LEFT JOIN news_comments c
            ON c.news_id=n.id
            GROUP BY n.id, n.title, n.content, n.created_at, n.posted_by, u.login, c.news_id
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
