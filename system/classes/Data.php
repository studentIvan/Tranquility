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
        return isset($_POST[$postVariableName]) ? trim($_POST[$postVariableName]) : false;
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
     * @return mixed
     */
    public static function titleToLink($title)
    {
        $title = mb_strtolower(str_replace(array(' ', '.', '_'), '-',
            preg_replace('/[^a-zа-яё0-9\-\. ]/ui', '', $title)), 'UTF-8');
        $rus = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н',
            'о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я');
        $lat = array('a','b','v','g','d','e','yo','zh','z','i','j','k','l','m','n',
            'o','p','r','s','t','u','f','h','c','ch','sh','shh','','y','','e','yu','ya');
        return str_replace($rus, $lat, $title);
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
}
