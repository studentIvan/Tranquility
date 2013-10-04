<?php
class Data
{
    /**
     * @static
     * @param string $postVariableName
     * @return bool
     */
    public static function input($postVariableName)
    {
        return isset($_POST[$postVariableName]) ?
            (is_array($_POST[$postVariableName]) ?
                $_POST[$postVariableName] : ($_POST[$postVariableName] ?
                    trim($_POST[$postVariableName]) : false)) : false;
    }

    /**
     * @static
     * @param string $var
     * @return mixed
     */
    public static function uriVar($var)
    {
        return isset($_GET[$var]) ? $_GET[$var] : false;
    }

    /**
     * @static
     * @return array
     */
    public static function inputsList()
    {
        $result = array();
        foreach (func_get_args() as $postVariableName)
            $result[] = self::input($postVariableName);
        return $result;
    }

    /**
     * @static
     * @param string $title
     * @param string $diffChar
     * @return string
     */
    public static function titleToLink($title, $diffChar = '-')
    {
        $title = mb_strtolower(str_replace(array(' ', '.', '_', '-'), $diffChar,
            preg_replace('/[^a-zа-яё0-9\-\. ]/ui', '', $title)), 'UTF-8');

        return self::lowerRusToLat($title);
    }

    /**
     * @static
     * @param $string
     * @return string
     */
    public static function wordsToSoundex($string)
    {
        $a = explode(' ', $string);
        foreach ($a as &$v) $v = soundex($v);
        return join(' ', $a);
    }

    /**
     * @static
     * @param string $text
     * @return string
     */
    public static function lowerRusToLat($text)
    {
        $rus = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н',
            'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'shh', '', 'y', '', 'e', 'yu', 'ya');
        return str_replace($rus, $lat, $text);
    }

    /**
     * @static
     * @param int $total
     * @param int $count
     * @param int $page
     * @return array
     */
    public static function paginate($total, $count = 30, $page = 1)
    {
        $start = $count * ($page - 1);
        $prev = (($page - 1) > 0) ? $page - 1 : false;
        $pages = ceil($total / $count);
        $next = (($page + 1) <= $pages) ? $page + 1 : false;
        $error = ((!$next && !$prev) or ($prev >= $pages) or ($page < 1));

        return $error ?
            array(
                'prev_id' => false,
                'next_id' => 2,
                'total_pages' => $pages,
                'current_id' => 1,
                'offset' => 0,
            ) :
            array(
                'prev_id' => $prev,
                'next_id' => $next,
                'total_pages' => $pages,
                'current_id' => $page,
                'offset' => $start,
            );
    }

    /**
     * @static
     * @param string $dirName
     * @return int
     */
    public static function getDirSize($dirName)
    {
        $dirSize = 0;
        if (!is_dir($dirName)) return $dirSize;

        if ($dh = opendir($dirName)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    if (is_file($dirName . '/' . $file)) {
                        $dirSize += filesize($dirName . '/' . $file);
                    }

                    if (is_dir($dirName . '/' . $file)) {
                        $dirSize += self::getDirSize($dirName . '/' . $file);
                    }
                }
            }
        }
        closedir($dh);
        return $dirSize;
    }

    /**
     * @param string $value
     * @param null $key
     * @return string
     */
    public static function walkingTrim(&$value, $key = null)
    {
        return trim($value);
    }

    /**
     * @author yuri
     * @uri http://www.php.net/manual/ru/function.array-push.php#106717
     *
     * @param array $array
     * @param mixed $object
     * @param integer $position
     * @param mixed $name
     * @return array
     */
    public static function arrayPutToPosition(&$array, $object, $position, $name = null)
    {
        $count = 0;
        $return = array();
        foreach ($array as $k => $v) {
            if ($count == $position) {
                if (!$name) $name = $count;
                $return[$name] = $object;
                $inserted = true;
            }
            $return[$k] = $v;
            $count++;
        }
        if (!$name) $name = $count;
        if (!$inserted) $return[$name];
        $array = $return;
        return $array;
    }
}
