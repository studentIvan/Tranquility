<?php
class Users
{
    public static function create($login, $password, $roleId)
    {
        $sql = "INSERT INTO users (login, password, role, registered_at)
                    VALUES (:login, :password, :role, NOW())";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':login', $login, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':role', $roleId, PDO::PARAM_INT);
        return $statement->execute();
    }

    public static function edit($id, $login, $password, $roleId)
    {
        $sql = "UPDATE users SET login=:login, password=:password, role=:role WHERE id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':login', $login, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':role', $roleId, PDO::PARAM_INT);
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
                ->prepare("DELETE FROM users WHERE id IN (?)")
                ->execute($id);
        } else {
            return Database::getInstance()
                ->prepare("DELETE FROM users WHERE id=?")
                ->execute(array($id));
        }
    }

    /**
     * @static
     * @param int $offset
     * @param bool|int $limit
     * @return array
     */
    public static function listingWithoutRoleTitles($offset = 0, $limit = 30)
    {
        $statement = Database::getInstance()->prepare("
            SELECT * FROM users
            ORDER BY registered_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @static
     * @param int $offset
     * @param bool|int $limit
     * @return array
     */
    public static function listing($offset = 0, $limit = 30)
    {
        $statement = Database::getInstance()->prepare("
            SELECT u.id AS id, u.login AS login, u.password AS password,
            u.role AS role, r.title AS title, u.registered_at AS registered_at
            FROM users AS u INNER JOIN roles AS r
            ON r.id=u.role
            ORDER BY u.registered_at DESC
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
            SELECT u.id AS id, u.login AS login, u.password AS password,
            u.role AS role, r.title AS title, u.registered_at AS registered_at
            FROM users AS u INNER JOIN roles AS r
            ON r.id=u.role
            WHERE u.id=:id
        ");

        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_OBJ);
    }
}
