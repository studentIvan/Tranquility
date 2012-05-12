<?php
return array(
    /**
     * Available for uses:
     * smtp
     * sendmail
     * native
     */
    'used_transport' => 'smtp',

    /**
     * Configuration for smtp
     */
    'smtp' => array(
        'host' => 'example.com:25', // server:port
        'username' => 'admin@example.com',
        'password' => 'example',
        'encryption' => null, // 'encryption' => 'ssl'
    ),

    /**
     * Configuration for sendmail
     */
    'sendmail' => array(
        'command_string' => '/usr/sbin/sendmail -bs',
    ),

    /**
     * Configuration for native
     */
    'native' => array(
        'extra_params' => '-f%s',
    ),

    /**
     * VCard for Mailer::createMessage function
     * $mailer->setFrom($email => "$firstName $lastName");
     */
    'v_card' => array(
        'email' => 'admin@example.com',
        'first_name' => 'Admin',
        'last_name' => 'Example',
    ),
);