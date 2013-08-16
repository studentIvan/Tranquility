<?php
class Installer
{
    public static function install()
    {
        if (($secretToken = Data::uriVar('secure'))
            and ($siteName = Data::uriVar('site'))
            and ($mysqlHost = Data::uriVar('mysqlhost'))
            and ($mysqlLogin = Data::uriVar('mysqllogin')))
        {
            $mysqlPassword = Data::uriVar('mysqlpassword');
            $mysqlPassword = $mysqlPassword ? $mysqlPassword : '';
            $mysqlAuth = ($mysqlPassword != '') ?
                "-u{$mysqlLogin} -p{$mysqlPassword}" : "-u{$mysqlLogin}";

            Process::$context['setup'] = true;

            if (!is_writable('../config/config.php')) {
                Process::$context['errs'] = array('Файл /config/config.php не доступен для записи');
                return;
            }

            if (!preg_match('/distrib/i', shell_exec("mysql -V"))) {
                Process::$context['errs'] = array('mysql client не установлен или не доступен из окружения');
                return;
            }

            $dbName = substr(preg_replace('/[^a-z]/', '', Data::titleToLink($siteName)), 0, 15);
            Process::$context['ress'] = array();
            $command = "mysql $mysqlAuth -e \"DROP DATABASE $dbName;\"";
            shell_exec($command);
            Process::$context['ress'][] = $command;
            $command = "mysql $mysqlAuth -e \"CREATE DATABASE $dbName CHARACTER SET utf8 COLLATE utf8_general_ci;\"";
            shell_exec($command);
            Process::$context['ress'][] = $command;
            $command = "mysql --default-character-set=utf8 $mysqlAuth $dbName < ../solutions/installer/sql/install.sql";
            shell_exec($command);
            Process::$context['ress'][] = $command;

            $pdo = new PDO("mysql:host=$mysqlHost;dbname=$dbName", $mysqlLogin, $mysqlPassword, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            ));

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $passwd = md5(md5('123456') . $secretToken);
            $command = "UPDATE users SET password='$passwd' WHERE id=1";
            $pdo->query($command);
            Process::$context['ress'][] = $command;

            $replacement = array(
                ", 'installer'" => '',
                "mysql:host=localhost;dbname=tranquility" => "mysql:host=$mysqlHost;dbname=$dbName",
                "'username' => 'root'" => "'username' => '$mysqlLogin'",
                "'password' => ''" => "'password' => '$mysqlPassword'",
                "'security_token' => 'ololo'" => "'security_token' => '$secretToken'",
                "'Tranquility site'" => "'$siteName'",
                "'visitors' => false" => "'visitors' => true",
            );

            $cfg = file_get_contents('../config/config.php');
            $cfg = str_replace(array_keys($replacement), array_values($replacement), $cfg);
            file_put_contents('../config/config.php', $cfg);
            Process::$context['ress'][] = 'Изменение ../config/config.php';

            Process::$context['ress'][] = 'Установка завершена';
        }
        else
        {
            Process::$context['setup'] = false;

            if (!is_writable('../config/config.php')) {
                Process::$context['setup'] = true;
                Process::$context['errs'] = array('Файл /config/config.php не доступен для записи');
                return;
            }

            if (!preg_match('/distrib/i', shell_exec("mysql -V"))) {
                Process::$context['setup'] = true;
                Process::$context['errs'] = array('mysql client не установлен или не доступен из окружения');
                return;
            }
        }
    }
}
