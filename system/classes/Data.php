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
        return isset($_POST[$postVariableName]) ? $_POST[$postVariableName] : false;
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
}
