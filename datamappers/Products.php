<?php
/**
 * Products demo data mapper (just for example)
 *
 * title - string
 * description - text
 * price - integer
 */
class Products
{
    public static function create($title, $description, $price)
    {
        $sql = "INSERT INTO products (title, description, price) VALUES (:title, :description, :price)";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':description', $description, PDO::PARAM_STR);
        $statement->bindParam(':price', $price, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function read($id)
    {
        $sql = "SELECT * FROM products WHERE id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $title, $description, $price)
    {
        $sql = "UPDATE products SET title=:title, description=:description, price=:price WHERE id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':description', $description, PDO::PARAM_STR);
        $statement->bindParam(':price', $price, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function delete($id)
    {
        if (is_array($id)) {
            return Database::getInstance()
                ->prepare("DELETE FROM products WHERE id IN (?)")
                ->execute($id);
        } else {
            return Database::getInstance()
                ->prepare("DELETE FROM products WHERE id=?")
                ->execute(array($id));
        }
    }

    public static function listing($offset = 0, $limit = 30)
    {
        $statement = Database::getInstance()->prepare("
            SELECT * FROM products
            LIMIT :limit OFFSET :offset
        ");

        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
