<?php
require_once dirname(__FILE__) . '/../../../system/__init__.php';
require_once dirname(__FILE__) . '/../crud/interfaces/CRUDDriverInterface.php';
require_once dirname(__FILE__) . '/../crud/interfaces/CRUDObjectInterface.php';
require_once dirname(__FILE__) . '/../controllers/Admin.php';
require_once dirname(__FILE__) . '/../controllers/Feeder.php';

class CRUDTestObject extends CRUDObjectInterface {
    public function setRBACPolicy($RBACPolicy) {
        $this->RBACPolicy = $RBACPolicy;
    }
    public function getMenuURI() {
        return 'thispartitionreallyexists';
    }
}