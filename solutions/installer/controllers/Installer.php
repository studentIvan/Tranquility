<?php
class Installer
{
    public static function install()
    {
        if ($secretToken = Data::uriVar('secure'))
        {
            Process::$context['setup'] = true;
        }
        else
        {
            Process::$context['setup'] = false;
        }
    }
}
