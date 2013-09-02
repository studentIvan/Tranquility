<?php
class PartSettings extends PartObjectInterface
{
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
}