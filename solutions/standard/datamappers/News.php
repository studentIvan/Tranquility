<?php
class News
{
    protected static $filter = false, $count = 0;

    /**
     * @param $filter
     */
    public static function setFilter($filter)
    {
        self::$filter = preg_replace('/[^a-zа-яё0-9\040\.\,\-_]/ui', '', trim(urldecode($filter)));
    }

    /**
     * @return int
     */
    public static function getCount()
    {
        return self::$count;
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

        $whereString = ($filter = self::$filter) ? "WHERE n.title LIKE '%$filter%'
            OR n.content LIKE '%$filter%' OR t.name LIKE '%$filter%'" : "";

        $statement = Database::getInstance()->prepare("
            SELECT n.id AS id, n.title AS title,
            n.content AS content,
            n.created_at AS created_at, n.posted_by AS poster_id,
            u.login AS poster_login,
            GROUP_CONCAT(DISTINCT t.name SEPARATOR ',') AS tags,
            COUNT(DISTINCT c.id) AS comments_total
            FROM news AS n LEFT JOIN users AS u
            ON n.posted_by=u.id
            LEFT JOIN tags_relation AS tr
            ON tr.news_id=n.id
            LEFT JOIN tags AS t
            ON t.id=tr.tag_id
            LEFT JOIN news_comments AS c
            ON c.news_id=n.id
            $whereString
            GROUP BY n.id, n.title, n.content, n.created_at, n.posted_by, u.login, c.news_id
            ORDER BY n.created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        self::$count = $statement->rowCount();

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
            u.login AS poster_login,
            GROUP_CONCAT(DISTINCT t.name SEPARATOR ',') AS tags
            FROM news AS n LEFT JOIN users AS u
            ON n.posted_by=u.id
            LEFT JOIN tags_relation AS tr
            ON tr.news_id=n.id
            LEFT JOIN tags AS t
            ON t.id=tr.tag_id
            WHERE n.id=:id
            GROUP BY n.id, t.id
        ");

        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_OBJ);
    }
}
