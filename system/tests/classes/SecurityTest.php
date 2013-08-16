<?php
class SecurityTest extends PHPUnit_Framework_TestCase
{
    public function testGetDigestFalseInNotZero()
    {
        $this->assertNotEquals(Security::getDigest(0), Security::getDigest(false));
    }

    public function testChangeSecret()
    {
        $this->setExpectedException('Exception', 'Too many security_token changes');
        Security::setSecret('secret attack');
    }

    public function testGetDigest()
    {
        $secret = 'phpunit secret';
        $this->assertEquals(
            SecurityDebug::getDigestWithSpecialSecret('test', $secret),
            md5(md5('test') . $secret)
        );
    }

    public function testGetDigestForDifferentIP()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $a = Security::getUniqueDigestForUserIP('test');
        $_SERVER['REMOTE_ADDR'] = '127.0.0.2';
        $b = Security::getUniqueDigestForUserIP('test');
        $this->assertNotEquals($a, $b);
    }
}
 