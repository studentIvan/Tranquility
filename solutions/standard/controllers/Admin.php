<?php
class Admin
{
    public static function control($matches)
    {
        Process::getTwigInstance()->display('admin/admin.html.twig', Process::$context);
    }
}
