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
    }

    public static function showPost($matches)
    {
        $newsId = isset($matches[1]) ? abs($matches[1]) : false;

        if ((!$newsId) or (!$post = News::getObjectById($newsId))) {
            throw new NotFoundException();
        }

        Process::$context['page_title'] = $post->title;
        Process::$context['news_content'] = $post->content;
        Process::$context['news_created_at'] = $post->created_at;
    }
}

