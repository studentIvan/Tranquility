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

Process::$context['vk_app_id'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['vk_app_id']))
    ? $config['cms']['social_auth']['vk_app_id'] : false;

Process::$context['vk_app_secure_key'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['vk_app_secure_key']))
    ? $config['cms']['social_auth']['vk_app_secure_key'] : '';

Process::$context['vk_user_role'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['vk_user_role']))
    ? $config['cms']['social_auth']['vk_user_role'] : 3;