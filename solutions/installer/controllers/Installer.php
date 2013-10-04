<?php
class Installer
{
    public static function install()
    {
        if (($secretToken = Data::uriVar('secure'))
            and ($siteName = Data::uriVar('site'))
            and ($siteDescription = Data::uriVar('site-description'))
            and ($mysqlHost = Data::uriVar('mysql-host'))
            and ($mysqlLogin = Data::uriVar('mysql-login')))
        {
            $mysqlPassword = Data::uriVar('mysql-password');
            $mysqlPassword = $mysqlPassword ? $mysqlPassword : '';
            $mysqlAuth = ($mysqlPassword != '') ?
                "-u{$mysqlLogin} -p{$mysqlPassword}" : "-u{$mysqlLogin}";

            Process::$context['setup'] = true;

            if (!is_writable('../config/base.json')) {
                Process::$context['errs'] = array('Файл /config/base.json не доступен для записи');
                return;
            }

            if (!is_writable('../config/dynamical.json')) {
                Process::$context['errs'] = array('Файл /config/dynamical.json не доступен для записи');
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
                "'Just another feed channel'" => "'$siteName feed channel'",
                "'No description'" => "'$siteDescription'",
            );

            $cfgBase = JSONConfig::read('base.json');
            $cfgDynamical = JSONConfig::read('dynamical.json');

            JSONConfig::save('base.json.default', $cfgBase);
            Process::$context['ress'][] = 'Создание бекапа ../config/base.json.default';
            JSONConfig::save('dynamical.json.default', $cfgDynamical);
            Process::$context['ress'][] = 'Создание бекапа ../config/dynamical.json.default';

            $cfgBase['solutions'] = array("standard", "webdevtoolbar", "profiles");
            $cfgBase['security_token'] = $secretToken;

            $cfgBase['pdo_developer_mode']['dsn'] = "mysql:host=$mysqlHost;dbname=$dbName";
            $cfgBase['pdo_developer_mode']['username'] = $mysqlLogin;
            $cfgBase['pdo_developer_mode']['password'] = $mysqlPassword;

            $cfgBase['pdo_production_mode']['dsn'] = "mysql:host=$mysqlHost;dbname=$dbName";
            $cfgBase['pdo_production_mode']['username'] = $mysqlLogin;
            $cfgBase['pdo_production_mode']['password'] = $mysqlPassword;

            $cfgDynamical['default_site_title'] = $siteName;
            $cfgDynamical['cms']['visitors'] = true;
            $cfgDynamical['cms']['news']['feed_title'] = "$siteName feed channel";
            $cfgDynamical['cms']['news']['feed_description'] = $siteDescription;

            JSONConfig::save('base.json', $cfgBase);
            Process::$context['ress'][] = 'Изменение ../config/base.json';
            JSONConfig::save('dynamical.json', $cfgDynamical);
            Process::$context['ress'][] = 'Изменение ../config/dynamical.json';

            Process::$context['ress'][] = 'Установка завершена';
        }
        else
        {
            Process::$context['setup'] = false;

            if (!is_writable('../config/base.json')) {
                Process::$context['setup'] = true;
                Process::$context['errs'] = array('Файл /config/base.json не доступен для записи');
                return;
            }

            if (!is_writable('../config/dynamical.json')) {
                Process::$context['setup'] = true;
                Process::$context['errs'] = array('Файл /config/dynamical.json не доступен для записи');
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
