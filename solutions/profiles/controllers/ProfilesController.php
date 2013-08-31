<?php
class ProfilesController
{
    public static function viewProfile($matches)
    {
        try {
            if (intval($matches[1]) > 0) {
                $userId = intval($matches[1]);
                $user = UserProfile::loadFromId($userId);
            } else {
                $login = $matches[1];
                $user = UserProfile::loadFromLogin($login);
            }
        } catch (Exception $e) {
            throw new NotFoundException();
        }


        Process::$context['user'] = array();
        Process::$context['user']['id'] = $user->getId();
        Process::$context['user']['login'] = $user->getLogin();
        Process::$context['user']['role'] = $user->getRole(true);
        Process::$context['user']['role_id'] = $user->getRole();
        Process::$context['user']['nickname'] = $user->getNickname();
        Process::$context['user']['full_name'] = $user->getFullName();
        Process::$context['user']['email'] = $user->getEmail();
        Process::$context['user']['gender'] = $user->getGender();
        Process::$context['user']['birthday'] = $user->getBirthday();
        Process::$context['user']['photo'] = $user->getPhoto();
        Process::$context['user']['registered_at'] = $user->getRegisteredAt();

        if (!empty(Process::$context['user']['nickname'])) {
            $displayName = Process::$context['user']['nickname'];
        } elseif (!empty(Process::$context['user']['full_name'])) {
            $displayName = Process::$context['user']['full_name'];
        } else {
            $displayName = Process::$context['user']['login'];
        }

        Process::$context['user']['display_name'] = $displayName;
        Process::$context['page_title'] .= ',  ' . $displayName;

        if (Process::$context['user']['role_id'] === Process::$context['vk']['user_role']) {
            Process::$context['vk']['user_link'] = substr(Process::$context['user']['login'], 3);
        } elseif (Process::$context['user']['role_id'] === Process::$context['fb']['user_role']) {
            Process::$context['fb']['user_link'] = substr(Process::$context['user']['login'], 3);
        }

        if ($data = $user->getNonIndexedData()) {
            foreach ($data as $key => $value) {
                Process::$context['user'][$key] = $value;
            }
        }
    }

    public static function activate($matches)
    {
        if (!Process::$context['turn_on_registration'])
            throw new NotFoundException();
        if (!(isset(Process::$context['cms']['email_confirm']) and
            Process::$context['cms']['email_confirm'])) {
            throw new NotFoundException();
        }

        $key = $matches[1];
        $login = $matches[2];

        try {
            $user = UserProfile::loadFromLogin($login);
        } catch (Exception $e) {
            throw new ForbiddenException();
        }

        if (
            $data = $user->getNonIndexedData() and
            isset($data['email_confirm']) and !$data['email_confirm'] and
            isset($data['email_confirm_key']) and $data['email_confirm_key'] and
            $data['email_confirm_key'] === $key
        ) {
            unset($data['email_confirm_key']);
            $data['email_confirm'] = true;
            $user->setNonIndexedData($data)->save();
            Process::redirect('/');
        } else {
            throw new ForbiddenException();
        }
    }

    public static function register()
    {
        if (!Process::$context['turn_on_registration'])
            throw new NotFoundException();
        Process::$context['page_title'] = 'Регистрация';
        $formCode = substr(Process::$context['csrf_token'], 0, 3);
        Process::$context['form_code'] = $formCode;
        list($login, $password, $passwordRepeat, $email, $captcha) =
            Data::inputsList(
                "xcya94n8cdjscam$formCode", "p218398s9gdy$formCode", "padm8300092gdy$formCode",
                "e7a9fg0h790awf$formCode", "cydas89gfy8431sas$formCode"
            );

        if ($login and $password and $passwordRepeat and $email and $captcha)
        {
            try
            {
                Process::load('GDCaptcha');

                if (!GDCaptcha::checkCorrect($captcha)) {
                    throw new InvalidArgumentException("Неверно введён код с картинки");
                }

                $user = new UserProfile();
                $user->setLogin($login);
                $user->setPassword($password, $passwordRepeat);
                $user->setEmail($email);
                $user->setRole(3);

                if (isset(Process::$context['cms']['email_confirm']) and
                    Process::$context['cms']['email_confirm'])
                {
                    $user->setNonIndexedData(array(
                        'email_confirm' => false,
                        'email_confirm_key' => Registration::getActivationKey($user),
                    ));
                }

                if ($user->save())
                {
                    Process::$context['complete'] = true;
                    if (isset(Process::$context['cms']['email_confirm']) and
                        Process::$context['cms']['email_confirm']) {
                        if (!Registration::sendConfirmationEmail($user)) {
                            $user->remove();
                            throw new InvalidArgumentException("Не удалось отправить письмо подтверждения");
                        }
                    }
                }
                else
                {
                    throw new InvalidArgumentException("Ошибка сервера, попробуйте позднее");
                }
            }
            catch (InvalidArgumentException $e) {
                Process::$context['flash_error'] = $e->getMessage();
                Process::$context['x_login'] = $login;
                Process::$context['x_email'] = $email;
                Process::$context['rrr'] = rand(111,999);
            }
        }
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
