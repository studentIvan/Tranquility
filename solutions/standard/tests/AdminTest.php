<?php
require_once dirname(__FILE__) . '/../crud/factoring/CRUDConfig.php';
require_once dirname(__FILE__) . '/../crud/factoring/CRUDField.php';
require_once dirname(__FILE__) . '/../crud/interfaces/CRUDDriverInterface.php';
require_once dirname(__FILE__) . '/../crud/interfaces/CRUDObjectInterface.php';
require_once dirname(__FILE__) . '/../controllers/Admin.php';
require_once dirname(__FILE__) . '/../controllers/Feeder.php';

class CRUDTestObject extends CRUDObjectInterface
{
    public function setRBACPolicy($RBACPolicy) {
        $this->RBACPolicy = $RBACPolicy;
    }

    public function getMenuURI() {
        return 'thispartitionreallyexists';
    }

    protected function setup()
    {
        $config = new CRUDConfig($this);
        $config->setTableName('nothing');
    }
}

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
        try
        {
            $CRUDTestObject = new CRUDTestObject();

            $CRUDTestObject->setRBACPolicy(array(
                'create' => array(999),
                'read' => array(999),
                'update' => array(999),
                'delete' => array(999),
            ));

            Admin::getContainer('thispartitionreallyexists', false, array($CRUDTestObject));
        } catch (ForbiddenException $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testGetContainerFlashError()
    {
        $CRUDTestObject = new CRUDTestObject();

        $CRUDTestObject->setRBACPolicy(array(
            'create' => 'any',
            'read' => 'any',
            'update' => 'any',
            'delete' => 'any',
        ));

        Admin::getContainer('thispartitionreallyexists', false, array($CRUDTestObject));

        if (isset(Process::$context['flash_error'])) {
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
 