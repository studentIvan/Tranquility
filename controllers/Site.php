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

        $commentWhichWasPosted = Data::input('asdjooqw8djsndasdasyo437trhs');
        $CSRFToken = Data::input('csrf_token');

        Process::$context['is_cool_boy'] = in_array(Session::getRole(), Process::$context['cool_roles']);

        if ($commentWhichWasPosted and $CSRFToken === Process::$context['csrf_token']) {
            Comments::create($newsId, $commentWhichWasPosted);
        }

        Process::$context['page_title'] = $post->title;
        Process::$context['news_content'] = $post->content;
        Process::$context['news_created_at'] = $post->created_at;
        Process::$context['comments'] = Comments::listingForNewsId($newsId);
    }

    public static function logout()
    {
        if (Process::$context['csrf_token'] === Data::uriVar('csrf_token'))
            Session::stop();
        Process::redirect('/');
    }

    public static function login()
    {
        list ($login, $password) = Data::inputsList('login', 'password');
        if (Process::$context['csrf_token'] == Data::input('csrf_token')) {
            try {
                Session::authorize($login, $password, true);
                Process::redirect('/');
            } catch (AuthException $e) {
                Process::redirect('/?error=1');
            } catch (Exception $e) {
                throw new ForbiddenException();
            }
        } else {
            throw new NotFoundException();
        }
    }

    public static function openAuth()
    {
        Process::load('uLogin');
        if (Process::$context['csrf_token'] == Data::uriVar('csrf_token')) {
            if (!ULogin::authorize()) {
                throw new ForbiddenException();
            } else {
                Process::redirect('/');
            }
        } else {
            throw new NotFoundException();
        }
    }
}

