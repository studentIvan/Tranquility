<?php
class ULogin
{
   /**
     * @static
     * @param string $token
     * @return array
     */
    public static function getData($token) {
        $s = file_get_contents('http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']);
        return json_decode($s, true);
    }

    /**
     * @static
     * @return bool
     */
    public static function authorize()
    {
        if (!Session::isAuth() and $token = Data::input('token'))
        {
            $userData = self::getData($token);
            if (isset($userData['first_name']))
            {
                $fullName = $userData['first_name'];
                if (isset($userData['last_name'])) {
                    $fullName .= ' ' . $userData['last_name'];
                }

                $login = substr(preg_replace('/[^a-z0-9_]/i', '',
                    substr($userData['network'], -3, 3) . $userData['identity']), -20, 20);

                $pdo = Database::getInstance();
                $sql = "SELECT COUNT(*) FROM users WHERE LOWER(login) LIKE LOWER(:login)";
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':login', $login);
                $statement->execute();

                $password = Security::getDigest($login . $fullName);

                if ($statement->fetchColumn() == 0)
                {
                    try {
                        $user = UserProfile::create($login, $password, 3);
                        $user->setEmail($userData['email']);
                        $user->setFullName($fullName);
                    } catch (InvalidArgumentException $e) {
                        return false;
                    }

                    try {
                        if (isset($userData['nickname'])) {
                            $user->setNickname($userData['nickname']);
                        }
                    } catch (InvalidArgumentException $e) {

                    }

                    try {
                        if (isset($userData['photo'])) {
                            $user->setPhoto($userData['photo']);
                        }
                    } catch (InvalidArgumentException $e) {

                    }

                    try {
                        if (isset($userData['sex'])) {
                            $userData['sex'] = str_replace(array(1, 2), array('w', 'm'), $userData['sex']);
                            if ($userData['sex'] == 'm' or $userData['sex'] == 'w') {
                                $user->setGender($userData['sex']);
                            }
                        }
                    } catch (InvalidArgumentException $e) {

                    }

                    try {
                        if (isset($userData['bdate'])) {
                            $bDate = new DateTime($userData['bdate']);
                            list($day, $month, $year) = array(
                                $bDate->format('d'), $bDate->format('m'), $bDate->format('Y')
                            );
                            $user->setBirthday($day, $month, $year);
                        }
                    } catch (Exception $e) {

                    }

                    try {
                        $nonIndexedData = array();

                        if (isset($userData['photo_big'])) {
                            $nonIndexedData['photo_big'] = $userData['photo_big'];
                        }

                        if (isset($userData['phone']) and strlen($userData['phone']) > 0) {
                            $nonIndexedData['phone'] = $userData['phone'];
                        }

                        if (count($nonIndexedData) > 0) {
                            $user->setNonIndexedData($nonIndexedData);
                        }

                    } catch (InvalidArgumentException $e) {

                    }

                    if (!$user->save()) {
                        return false;
                    }
                }

                try {
                    Session::authorize($login, $password, false, false);
                    return true;
                } catch (Exception $e) {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}
