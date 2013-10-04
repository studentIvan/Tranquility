<?php
class JSONConfigTest extends PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $config = JSONConfig::read('json_config_test.json');
        $this->assertEquals($config, array('test' => array(1,2,4), 'test2' => false, 'test3' => 'okay'));
    }

    public function testSave()
    {
        $config = JSONConfig::read('json_config_test.json');
        JSONConfig::save('json_config_test.json', $config);
    }
}
 