<?php
/**
 * Profiles solution
 */

if (!in_array('standard', Process::$solutions)) {
    throw new RuntimeException('"standard" solution required for "profiles"');
}

require_once dirname(__FILE__) . '/datamappers/Profiles.php';
require_once dirname(__FILE__) . '/models/UserProfile.php';
require_once dirname(__FILE__) . '/datamappers/Registration.php';