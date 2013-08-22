<?php
class ProcessTest extends PHPUnit_Framework_TestCase
{
    public function testGetTwigInstance()
    {
        $this->assertInstanceOf('Twig_Environment', Process::getTwigInstance());
    }

    public function testCallRoute()
    {
        ob_start();
        Process::callRoute('exception', array());
        ob_end_clean();
    }

    public function testLoadHelperGDCaptcha()
    {
        $this->assertNotEquals(class_exists('GDCaptcha'), true);
        Process::load('GDCaptcha');
        $this->assertEquals(class_exists('GDCaptcha'), true);
        /* stress test */
        for ($i = 0; $i < 10000; $i++) {
            Process::load('GDCaptcha');
        }
    }

    public function testIsMobile()
    {
        $__DIR__ = dirname(__FILE__);
        $_SERVER['HTTP_USER_AGENT'] = 'Opera/9.80 (Series 60; Opera Mini/6.1.25759/25.872; U; en) Presto/2.5.25 Version/10.54'; //mobile
        $IsMobileA = include $__DIR__ . '/../ismobile.php';
        $_SERVER['HTTP_USER_AGENT'] = 'Opera/12.02 (Android 4.1; Linux; Opera Mobi/ADR-1111101157; U; en-US) Presto/2.9.201 Version/12.02'; //mobile
        $IsMobileB = include $__DIR__ . '/../ismobile.php';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/4.0 (compatible; MSIE 4.01 ) Browser/NetFront/3.3 LinuxOS/2.4.20 Profile/MIDP-2.0 Configuration/CLDC-1.1 Ucweb/5.1'; //mobile
        $IsMobileC = include $__DIR__ . '/../ismobile.php';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'; //pc
        $IsMobileD = include $__DIR__ . '/../ismobile.php';

        $this->assertEquals($IsMobileA, true);
        $this->assertEquals($IsMobileB, true);
        $this->assertEquals($IsMobileC, true);
        $this->assertEquals($IsMobileD, false);
    }
}
 