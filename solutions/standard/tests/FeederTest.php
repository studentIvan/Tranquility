<?php
class FeederTest extends PHPUnit_Framework_TestCase
{
    public function testReadRSS()
    {
        $rss = Feeder::getRSS('php.unit');
    }

    public function testReadAtom()
    {
        $atom = Feeder::getAtom('php.unit');
    }
}
 