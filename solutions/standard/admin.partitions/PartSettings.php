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
        return Process::getTwigInstance()->render('admin/partitions/configuration.html.twig', Process::$context);
    }
}