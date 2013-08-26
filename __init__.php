<?php
/**
 * Application bootstrap
 */
Process::$context['http_host'] = $_SERVER['HTTP_HOST'];

function afterSessionStartedCallback()
{
    if (Session::isAuth()) {
        if (class_exists('UserProfile')) {
            $user = new UserProfile(Session::getUid());
            Process::$context['current_user'] = array(
                'id' => $user->getId(),
                'login' => $user->getLogin(),
                'role' => $user->getRole(),
                'nickname' => $user->getNickname(),
                'full_name' => $user->getFullName(),
                'photo' => $user->getPhoto(),
            );
        } else {
            $uid = Session::getUid();
            Process::$context['current_user'] = array(
                'id' => $uid,
                'login' => Database::getSingleResult("SELECT login FROM users WHERE id=$uid"),
                'role' => Session::getRole(),
            );
        }
    }
}
