<?php
class Roles
{
    public static function create($title)
    {
        $sql = "INSERT INTO roles (title) VALUES (:title)";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        return $statement->execute();
    }

    public static function edit($id, $title)
    {
        $sql = "UPDATE roles SET title=:title WHERE id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
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
                ->prepare("DELETE FROM roles WHERE id IN (?)")
                ->execute($id);
        } else {
            return Database::getInstance()
                ->prepare("DELETE FROM roles WHERE id=?")
                ->execute(array($id));
        }
    }

    /**
     * @static
     * @return array
     */
    public static function listing()
    {
        return Database::getInstance()
            ->query("SELECT * FROM roles")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
