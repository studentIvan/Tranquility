<?php
class AdminTest extends PHPUnit_Framework_TestCase
{
    public function testIsAccessAllow()
    {
        $this->assertEquals(Admin::isAccessAllow(1, array(1)), true);
        $this->assertEquals(Admin::isAccessAllow(2, array(2)), true);
        $this->assertEquals(Admin::isAccessAllow(1, array(1, 2)), true);
        $this->assertEquals(Admin::isAccessAllow(2, array(1, 2)), true);
    }

    public function testIsAccessDenied()
    {
        $this->assertEquals(Admin::isAccessAllow(0, array(1, 2)), false);
        $this->assertEquals(Admin::isAccessAllow(0, array(1)), false);
        $this->assertEquals(Admin::isAccessAllow(0, array(false)), false);
        $this->assertEquals(Admin::isAccessAllow(0, false), false);
        $this->assertEquals(Admin::isAccessAllow(1, false), false);
    }

    public function testGetContainerNotFoundException()
    {
        try {
            Admin::getContainer('-----thispartitiondoesntexists----', 'lol');
        } catch (NotFoundException $expected) {
            try {
                Admin::getContainer('-----thispartitiondoesntexists----', 'create');
            } catch (NotFoundException $expected) {
                try {
                    Admin::getContainer('-----thispartitiondoesntexists----', 'edit');
                } catch (NotFoundException $expected) {
                    try {
                        Admin::getContainer('-----thispartitiondoesntexists----', 'view');
                    } catch (NotFoundException $expected) {
                        return;
                    }
                }
            }
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testGetContainerRBACForbiddenException()
    {
        try {
            Admin::getContainer('thispartitionreallyexists', false, array(new CRUDTestObject()));
        } catch (ForbiddenException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testGetContainerPDOException()
    {
        $CRUDTestObject = new CRUDTestObject();

        $CRUDTestObject->setRBACPolicy(array(
            'create' => 'any',
            'read' => 'any',
            'update' => 'any',
            'delete' => 'any',
        ));

        try {
            Admin::getContainer('thispartitionreallyexists', false, array($CRUDTestObject));
        } catch (PDOException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testLoad()
    {
        ob_start();
        Admin::load(false);
        ob_end_clean();
    }
}
 