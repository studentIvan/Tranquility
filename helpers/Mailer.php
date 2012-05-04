<?php
class Mailer
{
    protected static $registered = false, $instance = null;
    public static $config = array();

    public static function register()
    {
        if (!self::$registered) {
            $__DIR____ = dirname(__FILE__);
            include_once $__DIR____ . '/../vendor/SwiftMailer/swift_required.php';
            self::$registered = true;
            self::$config = require $__DIR____ . '/../config/mailer.config.php';
        }
    }

    /**
     * @static
     * @param string $subject
     * @param mixed $to
     * @param string $body
     * @return Swift_Mime_Message
     */
    public static function createMessage($subject, $to, $body)
    {
        if (!self::$registered) self::register();
        $vCard = self::$config['v_card'];
        $message = Swift_Message::newInstance($subject);
        $message->setTo($to);
        $message->setBody($body);
        $message->setFrom(array(
            $vCard['email'] => "{$vCard['first_name']} {$vCard['last_name']}"
        ));

        return $message;
    }

    /**
     * @static
     * @return Swift_Mailer
     */
    public static function getInstance()
    {
        if (!self::$registered) self::register();
        if (is_null(self::$instance)) {
            $transport = self::$config;
            if ($transport['used_transport'] == 'smtp') {
                list($server, $port) = explode(':', $transport['smtp']['host']);
                $encryption = (isset($transport['smtp']['encryption']) and
                    $transport['smtp']['encryption']) ? $transport['smtp']['encryption'] : null;
                $swiftTransport = Swift_SmtpTransport::newInstance($server, $port, $encryption);
                $swiftTransport->setUsername($transport['smtp']['username']);
                $swiftTransport->setPassword($transport['smtp']['password']);
            } elseif ($transport['used_transport'] == 'sendmail') {
                $swiftTransport = Swift_SendmailTransport::newInstance($transport['sendmail']['command_string']);
            } else {
                $swiftTransport = Swift_MailTransport::newInstance($transport['native']['extra_params']);
            }

            self::$instance = Swift_Mailer::newInstance($swiftTransport);
        }

        return self::$instance;
    }

    public static function send(Swift_Mime_Message $message, $htmlContent = false)
    {
        if ($htmlContent)
        {
            $type = $message->getHeaders()->get('Content-Type');
            $type->setValue('text/html');
            $type->setParameter('charset', 'utf-8');
        }

        return self::getInstance()->send($message);
    }
}
