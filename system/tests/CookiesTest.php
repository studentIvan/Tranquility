<?php
class CookiesTest extends PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        try {
            Cookies::set('test', 'php.unit');
        } catch (ErrorException $expected) {
            return;
        }
    }

    public function testGet()
    {
        $_COOKIE['php.unit'] = 'passed';
        $this->assertEquals('passed', Cookies::get('php.unit'));
        $this->assertEquals(array('passed'), Cookies::get(array('php.unit')));
        $this->assertEquals(false, Cookies::get('phpunit-fail'));
    }
}
 