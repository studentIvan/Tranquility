<?php
/**
 * Profiles solution
 */

if (!in_array('standard', Process::$solutions)) {
    throw new RuntimeException('"standard" solution required for "profiles"');
}

require_once dirname(__FILE__) . '/datamappers/Profiles.php';
require_once dirname(__FILE__) . '/models/UserProfile.php';

Process::$context['vk'] = Process::$context['fb'] = array();

Process::$context['vk']['app_id'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['vk']['app_id']))
    ? $config['cms']['social_auth']['vk']['app_id'] : false;

Process::$context['vk']['app_secure_key'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['vk']['app_secure_key']))
    ? $config['cms']['social_auth']['vk']['app_secure_key'] : '';

Process::$context['vk']['user_role'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['vk']['user_role']))
    ? $config['cms']['social_auth']['vk']['user_role'] : 3;

Process::$context['fb']['app_id'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['fb']['app_id']))
    ? $config['cms']['social_auth']['fb']['app_id'] : false;

Process::$context['fb']['app_secret'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['fb']['app_secret']))
    ? $config['cms']['social_auth']['fb']['app_secret'] : '';

Process::$context['fb']['user_role'] = (isset($config['cms']['social_auth']) and
    isset($config['cms']['social_auth']['fb']['user_role']))
    ? $config['cms']['social_auth']['fb']['user_role'] : 3;

Process::$context['turn_on_registration'] = isset($config['cms']['turn_on_registration'])
    ? $config['cms']['turn_on_registration'] : false;

if (Process::$context['turn_on_registration']) {
    include_once dirname(__FILE__) . '/datamappers/Registration.php';
}