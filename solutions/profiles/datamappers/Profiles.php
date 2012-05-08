<?php
class Profiles
{
    /**
     * @static
     * @param int $userId
     * @param string $nickname
     * @param string $fullName
     * @param null|string $email
     * @param null|string $photo
     * @param null|string $gender
     * @param null|string $birthday
     * @param null|array $nonIndexedData
     * @return bool
     */
    public static function updateInfo($userId, $nickname = '', $fullName = '', $email = null, $photo = null,
                                      $gender = null, $birthday = null, $nonIndexedData = null)
    {
        $pdo = Database::getInstance();
        $statement = $pdo->prepare("SELECT COUNT(*) FROM users_data WHERE user_id=:userId");
        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();

        if (intval($statement->fetchColumn()) !== 0)
        {
            $sql = "UPDATE users_data
            SET nickname=:nickname, full_name=:fullName{{R}}
            WHERE user_id=:userId";

            if (!is_null($email)) {
                $sql = str_replace('{{R}}', ', email=:email{{R}}', $sql);
            }

            if (!is_null($photo)) {
                $sql = str_replace('{{R}}', ', photo=:photo{{R}}', $sql);
            }

            if (!is_null($gender)) {
                $sql = str_replace('{{R}}', ', gender=:gender{{R}}', $sql);
            }

            if (!is_null($birthday)) {
                $sql = str_replace('{{R}}', ', birthday=:birthday{{R}}', $sql);
            }

            if (!is_null($nonIndexedData) and count($nonIndexedData) > 0) {
                $sql = str_replace('{{R}}', ', non_indexed_data=:nonIndexedData{{R}}', $sql);
            }

            $sql = str_replace('{{R}}', '', $sql);

            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
            $statement->bindParam(':nickname', $nickname, PDO::PARAM_STR);
            $statement->bindParam(':fullName', $fullName, PDO::PARAM_STR);

            if (!is_null($email)) {
                $statement->bindParam(':email', $email, PDO::PARAM_STR);
            }

            if (!is_null($photo)) {
                $statement->bindParam(':photo', $photo, PDO::PARAM_STR);
            }

            if (!is_null($gender)) {
                $statement->bindParam(':gender', $gender, PDO::PARAM_STR);
            }

            if (!is_null($birthday)) {
                $statement->bindParam(':birthday', $birthday, PDO::PARAM_STR);
            }

            if (!is_null($nonIndexedData) and count($nonIndexedData) > 0) {
                $nonIndexedData = json_encode($nonIndexedData);
                $statement->bindParam(':nonIndexedData', $nonIndexedData, PDO::PARAM_STR);
            }

            return $statement->execute();
        }
        else
        {
            $sql = "INSERT INTO users_data
            (user_id, nickname, full_name{{RX}})
            VALUES (:userId, :nickname, :fullName{{R}})";

            if (!is_null($email)) {
                $sql = str_replace(array('{{RX}}', '{{R}}'), array(', email{{RX}}', ', :email{{R}}'), $sql);
            }

            if (!is_null($photo)) {
                $sql = str_replace(array('{{RX}}', '{{R}}'), array(', photo{{RX}}', ', :photo{{R}}'), $sql);
            }

            if (!is_null($gender)) {
                $sql = str_replace(array('{{RX}}', '{{R}}'), array(', gender{{RX}}', ', :gender{{R}}'), $sql);
            }

            if (!is_null($birthday)) {
                $sql = str_replace(array('{{RX}}', '{{R}}'), array(', birthday{{RX}}', ', :birthday{{R}}'), $sql);
            }

            if (!is_null($nonIndexedData) and count($nonIndexedData) > 0) {
                $sql = str_replace(array('{{RX}}', '{{R}}'), array(', non_indexed_data{{RX}}', ', :nonIndexedData{{R}}'), $sql);
            }

            $sql = str_replace(array('{{RX}}', '{{R}}'), '', $sql);

            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
            $statement->bindParam(':nickname', $nickname, PDO::PARAM_STR);
            $statement->bindParam(':fullName', $fullName, PDO::PARAM_STR);

            if (!is_null($email)) {
                $statement->bindParam(':email', $email, PDO::PARAM_STR);
            }

            if (!is_null($photo)) {
                $statement->bindParam(':photo', $photo, PDO::PARAM_STR);
            }

            if (!is_null($gender)) {
                $statement->bindParam(':gender', $gender, PDO::PARAM_STR);
            }

            if (!is_null($birthday)) {
                $statement->bindParam(':birthday', $birthday, PDO::PARAM_STR);
            }

            if (!is_null($nonIndexedData) and count($nonIndexedData) > 0) {
                $nonIndexedData = json_encode($nonIndexedData);
                $statement->bindParam(':nonIndexedData', $nonIndexedData, PDO::PARAM_STR);
            }

            return $statement->execute();
        }
    }

    /**
     * @static
     * @param $id
     * @param bool $decodeNonIndexedData
     * @return stdClass
     */
    public static function getDataObjectById($id, $decodeNonIndexedData = true)
    {
        $sql = "SELECT * FROM users_data WHERE user_id=:id";
        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);
        if ($result and $decodeNonIndexedData) {
            $result->non_indexed_data = json_decode($result->non_indexed_data);
        }

        return $result;
    }

    /**
     * @static
     * @param int $userId
     * @param bool $decodeNonIndexedData
     * @return array
     */
    public static function getUserProfileById($userId, $decodeNonIndexedData = true)
    {
        $sql = "SELECT * FROM users AS u
        LEFT JOIN users_data AS d ON u.id = d.user_id
        WHERE u.id=:userId";

        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result and $decodeNonIndexedData) {
            $result['non_indexed_data'] = json_decode($result['non_indexed_data'], true);
        }

        return $result;
    }

    /**
     * @static
     * @param string $login
     * @param bool $decodeNonIndexedData
     * @return array
     */
    public static function getUserProfileByLogin($login, $decodeNonIndexedData = true)
    {
        $sql = "SELECT * FROM users AS u
        LEFT JOIN users_data AS d ON u.id = d.user_id
        WHERE u.login=:login";

        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':login', $login, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result and $decodeNonIndexedData) {
            $result['non_indexed_data'] = json_decode($result['non_indexed_data'], true);
        }

        return $result;
    }

    public static function getUserProfileByEmail($email, $decodeNonIndexedData = true)
    {
        $sql = "SELECT * FROM users AS u
        LEFT JOIN users_data AS d ON u.id = d.user_id
        WHERE d.email=:email";

        $statement = Database::getInstance()->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result and $decodeNonIndexedData) {
            $result['non_indexed_data'] = json_decode($result['non_indexed_data'], true);
        }

        return $result;
    }
}
