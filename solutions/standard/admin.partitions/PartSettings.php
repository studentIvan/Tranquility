<?php
class PartSettings extends PartObjectInterface
{
    public $postConfig = array();
    public function __construct()
    {
        $this->setMenuName('Конфигурация');
        $this->setMenuIcon('cogs');
    }

    public function main()
    {
        Process::$context['settings'] = array(
            'd' => JSONConfig::read('dynamical.json'),
            'm' => JSONConfig::read('mailer.json'),
            'n' => file_get_contents(dirname(__FILE__) . "/../../../config/navigation.json"),
            'a' => file_get_contents(dirname(__FILE__) . "/../../../config/admin.json"),
            'b' => file_get_contents(dirname(__FILE__) . "/../../../config/base.json"),
        );
        Process::$context['roles_list'] = Roles::listing();
        return Process::getTwigInstance()->render('admin/partitions/configuration.html.twig', Process::$context);
    }

    protected function saveTreeRecursion($postConfig, $keyOriginal, $levelLength)
    {
        $currentKey = substr($keyOriginal, $levelLength);
        if (substr($currentKey, 0, 2) === '__') {
            if ($result = preg_match('/^__(.+?)__(?:.+?)$/', $currentKey, $matches)) {
                $subCat = preg_replace('/^_+(.+?)$/', '$1', $matches[1]);
                if ($subCat and (!isset($postConfig[$subCat]) or !is_array($postConfig[$subCat])))
                    $postConfig[$subCat] = array();
                $levelLength = $levelLength + strlen($matches[1]) + 2;
                $postConfig[$subCat] = $this->saveTreeRecursion($postConfig[$subCat], $keyOriginal, $levelLength);
            } else {
                $postConfig[preg_replace('/^_+(.+?)$/', '$1', $currentKey)] = Data::input($keyOriginal);
            }
        } else {
            $postConfig[preg_replace('/^_+(.+?)$/', '$1', $currentKey)] = Data::input($keyOriginal);
        }
        return $postConfig;
    }

    public function save()
    {
        if (Data::uriVar('csrf_token') !== Process::$context['csrf_token'])
            throw new ForbiddenException();
        $postConfigFile = Data::input('f_config');
        if (!$postConfigFile or !in_array($postConfigFile, array(
                'admin', 'base', 'dynamical', 'mailer', 'navigation'
            )))
            throw new ForbiddenException();

        /**
         * КОСТЫЛЬ
         */
        switch ($postConfigFile) {
            case 'dynamical':
                $_POST['input__session__garbage_auto_dump'] = isset($_POST['input__session__garbage_auto_dump']);
                $_POST['input__cms__email_confirm'] = isset($_POST['input__cms__email_confirm']);
                $_POST['input__cms__turn_on_registration'] = isset($_POST['input__cms__turn_on_registration']);
                $_POST['input__cms__visitors'] = isset($_POST['input__cms__visitors']);
                break;
            case 'mailer':
                $_POST['input__smtp__encryption'] = isset($_POST['input__smtp__encryption']) ?
                    (
                        (
                            (strtolower($_POST['input__smtp__encryption']) === 'null') ?
                                null : $_POST['input__smtp__encryption']
                        )
                    ) : null;
                if (isset($_POST['input__smtp__encryption']) and !$_POST['input__smtp__encryption']) {
                    $_POST['input__smtp__encryption'] = null;
                }
                break;
            case 'navigation':
                if ($tmpCfg = Data::input('navigation_json_raw')) {
                    if (JSONConfig::save("$postConfigFile.json", json_decode($tmpCfg))) {
                        Process::$context['flash_success'] = 'Настройки сохранены.';
                        return $this->main();
                    } else {
                        Process::$context['flash_danger'] = 'Произошла ошибка при компиляции настроек.';
                        return Process::getTwigInstance()->render('admin/alert.danger.html.twig', Process::$context);
                    }
                } else {
                    Process::$context['flash_danger'] = 'Произошла ошибка при компиляции настроек.';
                    return Process::getTwigInstance()->render('admin/alert.danger.html.twig', Process::$context);
                }
                break;
            case 'admin':
                if ($tmpCfg = Data::input('admin_json_raw')) {
                    if (JSONConfig::save("$postConfigFile.json", json_decode($tmpCfg))) {
                        Process::$context['flash_success'] = 'Настройки сохранены.';
                        return $this->main();
                    } else {
                        Process::$context['flash_danger'] = 'Произошла ошибка при компиляции настроек.';
                        return Process::getTwigInstance()->render('admin/alert.danger.html.twig', Process::$context);
                    }
                } else {
                    Process::$context['flash_danger'] = 'Произошла ошибка при компиляции настроек.';
                    return Process::getTwigInstance()->render('admin/alert.danger.html.twig', Process::$context);
                }
                break;
            case 'base':
                if ($tmpCfg = Data::input('base_json_raw')) {
                    if (JSONConfig::save("$postConfigFile.json", json_decode($tmpCfg))) {
                        Process::$context['flash_success'] = 'Настройки сохранены.';
                        return $this->main();
                    } else {
                        Process::$context['flash_danger'] = 'Произошла ошибка при компиляции настроек.';
                        return Process::getTwigInstance()->render('admin/alert.danger.html.twig', Process::$context);
                    }
                } else {
                    Process::$context['flash_danger'] = 'Произошла ошибка при компиляции настроек.';
                    return Process::getTwigInstance()->render('admin/alert.danger.html.twig', Process::$context);
                }
                break;
        }

        foreach (array_keys($_POST) as $key) {
            if (substr($key, 0, 6) === 'input_') {
                $this->postConfig = $this->saveTreeRecursion($this->postConfig, $key, 5);
            }
        }
        $target = dirname(__FILE__) . "/../../../config/$postConfigFile.json";
        $load = JSONConfig::read("$postConfigFile.json");
        $this->postConfig = array_merge_recursive($this->postConfig, $load);
        array_walk($this->postConfig, array($this, 'walkReload'));
        $postConfig = $this->postConfig;

        if (JSONConfig::save("$postConfigFile.json", $postConfig)) {
            Process::$context['flash_success'] = 'Настройки сохранены.';
            return $this->main();
        } else {
            Process::$context['flash_danger'] = 'Произошла ошибка при компиляции настроек.';
            return Process::getTwigInstance()->render('admin/alert.danger.html.twig', Process::$context);
        }
    }

    public function walkReload(&$item, $key)
    {
        $itemKeys = is_array($item) ? array_keys($item) : array(true);
        if (is_array($item) and count($item) == 2 and ($itemKeys[0] === 0)) {
            if ($item[0] and ($item[1] === true)) {
                $item = true;
            } elseif($item === true) {
                $item = false;
            } else {
                $item = is_int($item[1]) ? intval($item[0]) : $item[0];
            }
        } elseif (is_array($item)) {
            array_walk($item, array($this, 'walkReload'));
        }
    }
}