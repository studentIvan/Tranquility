<?php
require_once dirname(__FILE__) . '/../models/UserProfile.php';
class UserProfileTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $user = new UserProfile();
        $this->assertInstanceOf('UserProfile', $user);
    }

    public function testAll()
    {
        $user = new UserProfile();
        try {
            $user->setLogin('some login');
        } catch (InvalidArgumentException $e) {
            $user->setLogin('some_login_phpunit_s');
            $user->setPassword('123456');
            try {
                $user->setPassword('some_login_phpunit_s');
            } catch (InvalidArgumentException $e) {
                $user->setPassword('123456', '123456');
                $this->assertEquals($user->getPassword(), Security::getDigest('123456'));
                try {
                    $user->setPassword('123456', '1234567');
                } catch (InvalidArgumentException $e) {
                    try {
                        $user->setNickname('Some NickName');
                    } catch (InvalidArgumentException $e) {
                        $user->setNickname('SomeNickName');
                        $user->setFullName('Пётр Семенович Штрих');
                        $user->setFullName('Petr Semenovich Shtrich');
                        $user->setGender(UserProfile::GENDER_MAN);
                        $user->setGender(UserProfile::GENDER_WOMAN);
                        try {
                            $user->setGender('fuck');
                        } catch (InvalidArgumentException $e) {
                            $user->setPhoto('http://profile.ak.fbcdn.net/hprofile-ak-frc3/c28.10.125.125/s100x100/1239464_1387534991475212_148297301_a.jpg');
                            $user->setBirthday(12, 03, 1992);
                            $user->setEmail('phpunitsuperpupertest@examplephpunitemail.com');
                            $user->setRole(1);
                            $user->setNonIndexedData(array('about_me' => 'LMAO'));
                            $this->assertEquals($user->getRole(), 1);
                            $this->assertEquals($user->getNickname(), 'SomeNickName');
                            $this->assertEquals($user->getFullName(), 'Petr Semenovich Shtrich');
                            $this->assertNotEquals($user->getPassword(), '123456');
                            $this->assertEquals($user->getEmail(), 'phpunitsuperpupertest@examplephpunitemail.com');
                            $this->assertEquals($user->getBirthday(), '1992-03-12');
                            $this->assertEquals($user->getGender(), UserProfile::GENDER_WOMAN);
                            $this->assertEquals($user->getNonIndexedData(), array('about_me' => 'LMAO'));
                            $this->assertEquals($user->getNonIndexedData(false), json_encode(array('about_me' => 'LMAO')));
                            return;
                        }
                    }
                }
            }
        }

        $this->fail('ITS FAIL!');
    }

    public function testFullName()
    {
        $user = new UserProfile();
        try {
            $user->setFullName('Супер Имя у @МЕНЯ');
        } catch (InvalidArgumentException $e) {
            $user->setFullName('Мартина Грабер');
            $user->setFullName('Манаенков Дмитрий Петрович');
            $user->setFullName('Абу Джаффар ибн Мусси Аль Хорезми');
            $user->setFullName('Hans Trahenbyurger');
            $user->setFullName('Jafar ibn Abu Mussa Al Khwarizmi');
            $user->setFullName('Abu Jafar ibn Musa Al-Khwarizmi');
            $user->setFullName('Римский-Корсаков Николай Андреевич');
            $user->setFullName(' Николай Римский-Корсаков');
            $this->assertEquals($user->getFullName(), 'Николай Римский-Корсаков');
            try {
                $user->setFullName('أبو جعفر بن موسى الخوارزمي'); // рассово-неверное имя :D
            } catch (InvalidArgumentException $e) {
                return;
            }
        }

        $this->fail('ITS FAIL!');
    }
}
 