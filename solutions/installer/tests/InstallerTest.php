<?php
require_once dirname(__FILE__) . '/../controllers/Installer.php';

class InstallerTest extends PHPUnit_Framework_TestCase
{
    public function testInstall()
    {
        Installer::install();
    }
}
 