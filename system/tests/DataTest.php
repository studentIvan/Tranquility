<?php
class DataTest extends PHPUnit_Framework_TestCase
{
    public function testInput()
    {
        $fail = Data::input('php.unit');
        $_POST['php.unit'] = 'passed';
        $pass = Data::input('php.unit');

        $this->assertEquals($fail, false);
        $this->assertNotEquals($pass, true);
        $this->assertEquals($pass, 'passed');
    }

    public function testInputsList()
    {
        $_POST['php.unit'] = 'passed';
        $this->assertEquals(array(false, 'passed'), Data::inputsList('fail', 'php.unit'));
    }

    public function testUriVar()
    {
        $_GET['php.unit'] = 'passed';
        $this->assertEquals(false, Data::uriVar('fail'));
        $this->assertEquals('passed', Data::uriVar('php.unit'));
    }

    public function testTitleToLink()
    {
        $this->assertEquals('demo-demo', Data::titleToLink('DEmo Демо'));
        $this->assertEquals('demo_demo', Data::titleToLink('DEmo Демо', '_'));
        $this->assertEquals('demodemo', Data::titleToLink('DEmo Демо', false));
    }
}
 