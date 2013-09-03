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
        Process::$context['settings'] = json_decode(file_get_contents(dirname(__FILE__) . '/../../../config/dynamical.json'), true);
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
        foreach (array_keys($_POST) as $key) {
            if (substr($key, 0, 6) === 'input_') {
                $this->postConfig = $this->saveTreeRecursion($this->postConfig, $key, 5);
            }
        }
        $target = dirname(__FILE__) . "/../../../config/$postConfigFile.json";
        $load = json_decode(file_get_contents($target), true);
        $this->postConfig = array_merge_recursive($this->postConfig, $load);
        array_walk($this->postConfig, array($this, 'walkReload'));
        if ($postConfig = json_encode($this->postConfig)) {
            if (!is_writable($target)) {
                Process::$context['flash_danger'] = 'Данный файл настроек недоступен для записи.';
                return Process::getTwigInstance()->render('admin/alert.danger.html.twig', Process::$context);
            } else {
                file_put_contents($target, $postConfig);
                Process::$context['flash_success'] = 'Настройки сохранены.';
                return $this->main();
            }
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