<?php
class PartCron extends PartObjectInterface
{
    public function __construct()
    {
        $this->setMenuName('Планировщик задач');
        $this->setMenuIcon('beaker');
    }
}