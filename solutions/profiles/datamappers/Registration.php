<?php
class Registration
{
    /**
     * @static
     * @param UserProfile $user
     * @return bool
     */
    public static function sendConfirmationEmail(UserProfile $user)
    {
        try
        {
            Process::load('mailer');

            $email = $user->getEmail();

            if (is_null($email))
            {
                $email = $user->getLogin();
                $to = $email;
            }
            else
            {
                $to = array(
                    $email => $user->getLogin()
                );
            }

            $subject = $_SERVER['HTTP_HOST'] . ' - подтверждение email';
            $text = '<h3>Подтверждение адреса электронной почты</h3>
            <p>С вашего адреса электронной почты была зафиксирована попытка
            регистрации аккаунта на сайте $site</p>
            <p>Если это были действительно Вы, то пройдите пожалуйста по ссылке активации:</p>
            <p><a href="$activate">$activate</a></p>';

            $message = Mailer::createMessage(
                $subject, $to, str_replace(array('$site', '$activate'),
                array($_SERVER['HTTP_HOST'], 'http://' . $_SERVER['HTTP_HOST'] . '/activate/' .
                    Registration::getActivationKey($user) . '/' . $user->getLogin() . '.html'), $text
            ));

            return Mailer::send($message, true);
        }
        catch (LogicException $e)
        {
            return false;
        }
    }

    /**
     * @static
     * @param UserProfile $user
     * @return string
     */
    public static function getActivationKey(UserProfile $user)
    {
        return Security::getDigest(array(
            Session::getToken(), $user->getLogin()
        ));
    }
}
