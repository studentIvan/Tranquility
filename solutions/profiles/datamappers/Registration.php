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

            /**
             * TODO: FUCKING MESSAGE EMAIL
             */

            $subject = '';
            $text = '';
            $message = Mailer::createMessage($subject, $to, $text);

            return true;
        }
        catch (LogicException $e)
        {
            return false;
        }
    }
}
