<?php
class Site
{
    public static function news($matches)
    {
        $page = isset($matches[1]) ? abs($matches[1]) : 1;
        $perPage = Process::$context['cms']['news']['limit_per_page'];
        $pagination = Data::paginate(Database::count('news'), $perPage, $page);
        Process::$context['news_list'] = News::listing($pagination['offset'], $perPage);
        Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
        Process::$context['page_title'] = 'Tranquility site';
    }
}

