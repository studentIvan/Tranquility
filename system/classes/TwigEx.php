<?php
/**
 * Example: {{ (users_count ~ " пользовател")|plural("ь", "я", "ей") }}
 */
class TwigEx extends Twig_Extension
{
    public function getName()
    {
        return 'plural';
    }

    public function getFilters()
    {
        return array(
            'plural' => new Twig_Filter_Method($this, 'plural'),
            'nplural' => new Twig_Filter_Method($this, 'nplural'),
            'tolink' => new Twig_Filter_Method($this, 'tolink'),
            'tagsView' => new Twig_Filter_Method($this, 'tagsView'),
        );
    }

    /**
     * @param string $title
     * @return mixed
     */
    public function tolink($title)
    {
        return Data::titleToLink($title);
    }

    /**
     * NPlural - число указывается вручную.
     * Set number manual.
     *
     * @param string $text
     * @param string $e1 1 пользователь 1 user
     * @param string $e2 2 пользователя 2 users
     * @param string $e3 10 пользователей 10 users
     * @param integer $digit
     * @return string
     */
    public function nplural($text, $e1, $e2, $e3, $digit)
    {
        $digit = ($digit > 20) ? $digit % 10 : $digit;
        if ($digit >= 5 || $digit == 0) return $text . $e3;
        if ($digit >= 2) return $text . $e2;
        return $text . $e1;
    }

    /**
     * Plural - берет первое число до пробела.
     * Take first number before space.
     *
     * @param string $text
     * @param string $e1
     * @param string $e2
     * @param string $e3
     * @return string
     */
    public function plural($text, $e1, $e2, $e3)
    {
        $digit = intval(strtok($text, ' '));
        return $this->nplural($text, $e1, $e2, $e3, $digit);
    }

    /**
     * @param string $string
     * @param string $linkToTag
     * @return string
     */
    public function tagsView($string, $linkToTag = '#$1')
    {
        return preg_replace('/([^,]+)/us', ' <a href="' . $linkToTag . '">#$1</a>', $string);
    }
}
