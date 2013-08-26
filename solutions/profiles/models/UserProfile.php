<?php
/**
 * User profile model (with russian exceptions)
 */
class UserProfile
{
    protected $id = null, $login = null, $password = null, $role = null, $registered_at = null;
    protected $nickname = '', $full_name = '', $email = null, $photo = null,
        $gender = null, $birthday = null, $non_indexed_data = null;
    protected $login_or_password_or_role_changed = false, $login_is_email = false;

    const GENDER_MAN = 'm';
    const GENDER_WOMAN = 'w';

    public static $exceptions = array(
        'email' => 'Email должен быть правильным, быть больше восьми и меньше 51 символа в длину',
        'login' => 'Логин может состоять только из символа "подчеркивание", латинских букв и цифр,
                быть больше 3х и меньше 21 символа в длину, и начинаться с буквы',
        'login_exists' => 'Такой логин уже зарегистрирован',
        'password' => 'Пароль не должен равняться логину',
        'password_length' => 'Пароль должен быть не менее четырёх символов в длину',
        'password_repeat' => 'Введённые пароли не совпадают',
        'user_not_exists' => 'Такого пользователя не существует',
        'role' => 'Неверно указана роль',
        'nickname' => 'Ник может состоять только из символа "подчеркивание", букв a-z, а-я и цифр,
                быть больше 3х и меньше 21 символа в длину, и начинаться с буквы',
        'full_name' => 'Полное имя может состоять только из символов "тире", апострофов, букв a-z, а-я и пробелов,
                быть больше одного и меньше 101 символа в длину, и начинаться с буквы',
        'photo' => 'Неверный адрес ссылки photo',
        'gender' => 'Неверно указан пол',
        'birthday' => 'Неверно указана дата рождения',
        'non_indexed_data' => 'data должна быть массивом',
    );

    /**
     * @param null|int $loadByUserId
     * @param null|string $loadByLogin
     * @throws InvalidArgumentException
     */
    public function __construct($loadByUserId = null, $loadByLogin = null)
    {
        if (!is_null($loadByUserId) and
            ($profile = Profiles::getUserProfileById($loadByUserId, false))) {
            foreach ($profile as $attribute => $value) {
                $this->$attribute = $value;
            }
        } elseif (!is_null($loadByLogin) and
            ($profile = Profiles::getUserProfileByLogin($loadByLogin, false))) {
            foreach ($profile as $attribute => $value) {
                $this->$attribute = $value;
            }
        } elseif (!is_null($loadByUserId) or !is_null($loadByLogin)) {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['user_not_exists']
            );
        }
    }

    /**
     * @static
     * @param string $login
     * @param string $password
     * @param int $role
     * @return UserProfile
     */
    public static function create($login, $password, $role)
    {
        $user = new self;
        $user->setLogin($login);
        $user->setPassword($password);
        $user->setRole($role);
        return $user;
    }

    /**
     * @static
     * @param int $userId
     * @return UserProfile
     */
    public static function loadFromId($userId)
    {
        return new self($userId);
    }

    /**
     * @static
     * @param string $login
     * @return UserProfile
     */
    public static function loadFromLogin($login)
    {
        return new self(null, $login);
    }

    /**
     * @param string $login
     * @param bool $loginIsEmail
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setLogin($login, $loginIsEmail = false)
    {
        if ($loginIsEmail) {
            $email = strtolower(trim($login));
            $length = strlen($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL) and
                $length > 8 and $length < 51) {
                $sql = "SELECT COUNT(*) FROM users WHERE LOWER(login) LIKE :email";
                $statement = Database::getInstance()->prepare($sql);
                $statement->bindParam(':email', $email);
                $statement->execute();
                if ($statement->fetchColumn() > 0) {
                    throw new InvalidArgumentException(
                        UserProfile::$exceptions['login_exists']
                    );
                }
                $this->login = $email;
                $this->login_or_password_or_role_changed = true;
                $this->login_is_email = true;
                return $this;
            } else {
                throw new InvalidArgumentException(
                    UserProfile::$exceptions['email']
                );
            }
        } else {
            $length = mb_strlen($login, 'UTF-8');
            if (preg_match('/^[a-z][a-z0-9_]+$/i', $login) and $length > 3 and $length < 21) {
                $sql = "SELECT COUNT(*) FROM users WHERE LOWER(login) LIKE LOWER(:login)";
                $statement = Database::getInstance()->prepare($sql);
                $statement->bindParam(':login', $login);
                $statement->execute();
                if ($statement->fetchColumn() > 0) {
                    throw new InvalidArgumentException(
                        UserProfile::$exceptions['login_exists']
                    );
                }
                $this->login = $login;
                $this->login_or_password_or_role_changed = true;
                return $this;
            } else {
                throw new InvalidArgumentException(
                    UserProfile::$exceptions['login']
                );
            }
        }
    }

    /**
     * @param string $password
     * @param bool|string $passwordRepeat
     * @throws InvalidArgumentException
     * @return UserProfile
     */
    public function setPassword($password, $passwordRepeat = false)
    {
        if ($passwordRepeat and ($password != $passwordRepeat)) {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['password_repeat']
            );
        }
        $length = mb_strlen($password, 'UTF-8');
        if ($length > 3) {
            if ($password != $this->login) {
                $this->password = Security::getDigest($password);
                $this->login_or_password_or_role_changed = true;
                return $this;
            } else {
                throw new InvalidArgumentException(
                    UserProfile::$exceptions['password']
                );
            }
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['password_length']
            );
        }
    }

    /**
     * @param int $roleId
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setRole($roleId)
    {
        $roleId = abs($roleId);
        $maxRoleId = abs(Database::getSingleResult("SELECT MAX(id) FROM roles"));
        if ($roleId > 0 and $roleId <= $maxRoleId) {
            $this->role = $roleId;
            $this->login_or_password_or_role_changed = true;
            return $this;
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['role']
            );
        }
    }

    /**
     * @param string $nickname
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setNickname($nickname)
    {
        $length = mb_strlen($nickname, 'UTF-8');
        if (preg_match('/^[a-zA-Zа-яА-ЯёЁ][a-zA-Zа-яА-ЯёЁ0-9_]+$/u', $nickname) and
            $length > 3 and $length < 21) {
            $this->nickname = $nickname;
            return $this;
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['nickname']
            );
        }
    }

    /**
     * @param string $fullName
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setFullName($fullName)
    {
        $fullName = trim($fullName);
        $length = mb_strlen($fullName, 'UTF-8');
        if (preg_match('/^[a-zA-Zа-яА-ЯёЁ][a-zA-Zа-яА-ЯёЁ ’\'\-]+$/u', $fullName) and
            $length > 1 and $length < 101) {
            $this->full_name = $fullName;
            return $this;
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['full_name']
            );
        }
    }

    /**
     * @param string $email
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setEmail($email)
    {
        $email = strtolower(trim($email));
        $length = strlen($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL) and
            $length > 8 and $length < 51) {
            $this->email = $email;
            return $this;
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['email']
            );
        }
    }

    /**
     * @param string $url external photo link
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setPhoto($url)
    {
        $url = trim($url);
        $length = strlen($url);
        if (filter_var($url, FILTER_VALIDATE_URL) and
            $length > 8 and $length < 256) {
            $this->photo = $url;
            return $this;
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['photo']
            );
        }
    }

    /**
     * @param string $gender
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setGender($gender)
    {
        if ($gender == 'm' or $gender == 'w') {
            $this->gender = $gender;
            return $this;
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['gender']
            );
        }
    }

    /**
     * @param int $day
     * @param int $month
     * @param int $year
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setBirthday($day, $month, $year)
    {
        if (checkdate($month, $day, $year)) {
            $date = new DateTime();
            $date->setDate($year, $month, $day);
            $this->birthday = $date->format('Y-m-d');
            return $this;
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['birthday']
            );
        }
    }

    /**
     * @param array $data
     * @return UserProfile
     * @throws InvalidArgumentException
     */
    public function setNonIndexedData($data)
    {
        if (is_array($data)) {
            $this->non_indexed_data = json_encode($data);
            return $this;
        } else {
            throw new InvalidArgumentException(
                UserProfile::$exceptions['non_indexed_data']
            );
        }
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function getLogin()
    {
        if (!is_null($this->login)) {
            return $this->login;
        } else {
            throw new LogicException();
        }
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function getPassword()
    {
        if (!is_null($this->password)) {
            return $this->password;
        } else {
            throw new LogicException();
        }
    }

    /**
     * @param bool $returnTitle
     * @throws LogicException
     * @return int|string
     */
    public function getRole($returnTitle = false)
    {
        if (!is_null($this->role))
        {
            return $returnTitle
                ? Database::getSingleResult('SELECT title FROM roles WHERE id=' . $this->role)
                : intval($this->role);
        }
        else
        {
            throw new LogicException();
        }
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function getRegisteredAt()
    {
        if (!is_null($this->registered_at)) {
            return $this->registered_at;
        } else {
            throw new LogicException();
        }
    }

    /**
     * @return string
     */
    public function getNickname() {
        return $this->nickname;
    }

    /**
     * @return string
     */
    public function getFullName() {
        return $this->full_name;
    }

    /**
     * @return string|null
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPhoto() {
        return $this->photo;
    }

    /**
     * @return string|null
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * @return string|null
     */
    public function getBirthday() {
        return $this->birthday;
    }

    /**
     * @param bool $decoded
     * @return mixed
     */
    public function getNonIndexedData($decoded = true) {
        return is_null($this->non_indexed_data) ? null : (
        $decoded ? json_decode($this->non_indexed_data, true) : $this->non_indexed_data);
    }

    /**
     * @return int
     */
    public function getId() {
        return intval($this->id);
    }

    /**
     * @return bool|int
     */
    public function save()
    {
        try
        {
            $pdo = Database::getInstance();
            $login = $this->getLogin();
            $password = $this->getPassword();
            $role = $this->getRole();

            if (is_null($this->id) and Users::create($login, $password, $role))
            {
                $this->id = $pdo->lastInsertId();
                $userId = $this->id;
                $nickname = $this->getNickname();
                $fullName = $this->getFullName();
                $email = $this->getEmail();
                $photo = $this->getPhoto();
                $gender = $this->getGender();
                $birthday = $this->getBirthday();
                $nonIndexedData = $this->getNonIndexedData();
                $result = Profiles::updateInfo($userId, $nickname, $fullName,
                    $email, $photo, $gender, $birthday, $nonIndexedData);
                return $result ? $userId : false;
            }
            elseif (!is_null($this->id))
            {
                $userId = $this->id;
                if ($this->login_or_password_or_role_changed) {
                    if (!Users::edit($userId, $login, $password, $role)) {
                        return false;
                    }
                }
                $nickname = $this->getNickname();
                $fullName = $this->getFullName();
                $email = $this->getEmail();
                $photo = $this->getPhoto();
                $gender = $this->getGender();
                $birthday = $this->getBirthday();
                $nonIndexedData = $this->getNonIndexedData();
                $result = Profiles::updateInfo($userId, $nickname, $fullName,
                    $email, $photo, $gender, $birthday, $nonIndexedData);
                return $result ? $userId : false;
            }
            else
            {
                return false;
            }
        }
        catch (LogicException $e)
        {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $userId = $this->id;

        return (!is_null($userId)) ?
            Users::remove($userId) : false;
    }

    /**
     * @param int $minutesInterval
     * @return bool
     */
    public function isOnline($minutesInterval)
    {
        $userId = $this->id;

        return (!is_null($userId)) ?
            Users::checkOnlineState($userId, $minutesInterval) : false;
    }
}
