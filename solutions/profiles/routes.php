<?php
return array(
    '/profile/([a-zA-Z0-9_]+)\.html' => array(
        '!common:session', '!profiles:profilesController:viewProfile', 'profiles/userprofile'
    ),
    '/registration\.html' => array(
        '!common:session', '!profiles:profilesController:register', 'profiles/registration'
    ),
    '/captcha/(\S+)\.png' => array(
        '!common:session', '!profiles:profilesController:getCaptcha'
    ),
);