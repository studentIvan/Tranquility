<?php
class ProfilesController
{
    public static function viewProfile($matches)
    {
        if (intval($matches[1]) > 0) {
            $userId = intval($matches[1]);
            $user = UserProfile::loadFromId($userId);
        } else {
            $login = $matches[1];
            $user = UserProfile::loadFromLogin($login);
        }

        Process::$context['user'] = array();
        Process::$context['user']['id'] = $user->getId();
        Process::$context['user']['login'] = $user->getLogin();
        Process::$context['user']['role'] = $user->getRole(true);
        Process::$context['user']['nickname'] = $user->getNickname();
        Process::$context['user']['full_name'] = $user->getFullName();
        Process::$context['user']['email'] = $user->getEmail();
        Process::$context['user']['gender'] = $user->getGender();
        Process::$context['user']['birthday'] = $user->getBirthday();
        Process::$context['user']['photo'] = $user->getPhoto();
        Process::$context['user']['registered_at'] = $user->getRegisteredAt();

        if ($data = $user->getNonIndexedData()) {
            foreach ($data as $key => $value) {
                Process::$context['user'][$key] = $value;
            }
        }
    }

    public static function register()
    {
        Process::$context['page_title'] = 'Регистрация';
    }

    public static function getCaptcha($matches)
    {
        $csrfToken = $matches[1];
        if ($csrfToken == Process::$context['csrf_token']) {
            Process::load('GDCaptcha');
            GDCaptcha::show(5, 120, 60, 6);
        } else {
            throw new ForbiddenException();
        }
    }
}
