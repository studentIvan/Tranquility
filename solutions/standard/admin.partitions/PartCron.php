<?php
class PartCron extends PartObjectInterface
{
    public function __construct()
    {
        $this->setMenuName('Планировщик задач');
        $this->setMenuIcon('beaker');
    }

    public function main()
    {
        Process::$context['cron_data'] = file_get_contents(dirname(__FILE__) . '/../../../production/cronjob.php');
        return Process::getTwigInstance()->render('admin/partitions/cron.stress.html.twig', Process::$context);
    }

    public function stress()
    {
        if (Data::uriVar('csrf_token') === Process::$context['csrf_token']) {
            include_once dirname(__FILE__) . '/../../../production/cronjob.php';
            Process::$context['flash_success'] = 'CRON операции выполнены успешно.';
            Process::getTwigInstance()->display('admin/alert.success.html.twig', Process::$context);
            exit;
        } else {
            throw new ForbiddenException();
        }
    }
}