<?php
/**
 * Application bootstrap
 */
Process::$context['http_host'] = $_SERVER['HTTP_HOST'];

function afterSessionStartedCallback()
{
    if (Session::isAuth()) {
        $user = new UserProfile(Session::getUid());
        Process::$context['current_user'] = array(
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'full_name' => $user->getFullName(),
            'photo' => $user->getPhoto(),
        );
    }
}
