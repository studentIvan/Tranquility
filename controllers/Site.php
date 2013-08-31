<?php
/**
 * Class Site
 * @TODO EDIT COMMENTS
 */
class Site
{
    public static function news($matches)
    {
        $page = isset($matches[1]) ? abs($matches[1]) : 1;
        $filter = isset($matches[2]) ? $matches[2] : false;

        if (!$filter) {
            $perPage = Process::$context['cms']['news']['limit_per_page'];
            $pagination = Data::paginate(Database::count('news'), $perPage, $page);
            Process::$context['news_list'] = News::listing($pagination['offset'], $perPage);
        } else {
            Process::$context['filter_string'] = urldecode($filter);
            Process::$context['news_list'] = News::listing(0, 10);
        }

        foreach (Process::$context['news_list'] as &$news) {
            if (isset($news['tags']) and $news['tags']) {
                $news['tags'] = preg_replace('/([^,]+),?/us',
                    '<a href="/look/$1" class="tag-link">
                        <span class="label label-default">$1</span>
                    </a>', $news['tags']);
            }
        }

        if (!$filter) {
            Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
        }
    }

    public static function search($matches)
    {
        if (isset($matches[1])) {
            if (News::setFilter($matches[1])) {
                self::news(array('', isset($matches[2]) ? $matches[2] : null, $matches[1]));
            } else {
                Process::$context['flash_warning'] = 'Слишком короткий поисковый запрос';
                self::news(array());
            }
        } else {
            throw new NotFoundException();
        }
    }

    public static function socialDispatcher()
    {
        if (Data::uriVar('csrf_token') !== Process::$context['csrf_token'])
            throw new ForbiddenException('attack');
        list($provider, $session, $profile) = Data::inputsList('provider', 'session', 'profile');
        if ($provider and $session and $profile) {
            switch ($provider) {
                case 'facebook':
                    try {
                        $accessToken = $session['accessToken'];
                        $userId = $session['userID'];
                        $result = file_get_contents("http://graph.facebook.com/$userId/?accessToken=$accessToken");
                        if ($result) {
                            $result = json_decode($result);
                            if (isset($result->error)) {
                                throw new Exception('Facebook API error');
                            } else {
                                if (strval($userId) !== strval($result->id) and
                                    strval($userId) !== strval($profile['id']))
                                    throw new ForbiddenException('attack!');
                                $user = new UserProfile();
                                $login = 'fb_' . $result->username;
                                try {
                                    $user->setLogin($login);
                                } catch (InvalidArgumentException $e) {
                                    Session::authorize($login, false, false, false);
                                    echo 'ok';
                                    exit;
                                }
                                $user->setPassword(rand(1000, 9000));
                                $user->setRole(Process::$context['fb']['user_role']);
                                $user->setPhoto($profile['photo_100']);
                                $user->setFullName($result->first_name . ' ' . $result->last_name);
                                $user->setGender(($result->gender === 'male') ?
                                    UserProfile::GENDER_MAN : UserProfile::GENDER_WOMAN);
                                $user->save();
                                Session::authorize($login, false, false, false);
                                try {
                                    $token = Session::getToken();
                                    Database::getInstance()->query("DELETE FROM captcha WHERE token='$token'");
                                } catch (Exception $e) {}
                                echo 'ok';
                                exit;
                            }
                        } else {
                            throw new Exception('Connect to facebook.com fail!');
                        }
                    } catch (Exception $e) {
                        header('Content-Type: application/json');
                        echo json_encode($e->getMessage());
                        exit;
                    }
                    break;
                case 'vk':
                    $sigCalculated = 'expire=' . $session['expire'];
                    $sigCalculated .= 'mid=' . $session['mid'];
                    $sigCalculated .= 'secret=' . $session['secret'];
                    $sigCalculated .= 'sid=' . $session['sid'];
                    $sigCalculated .= Process::$context['vk']['app_secure_key'];
                    $sigCalculated = md5($sigCalculated);
                    if ($session['sig'] === $sigCalculated) {
                        try {
                            $user = new UserProfile();
                            $login = 'vk_' . $profile['screen_name'];
                            try {
                                $user->setLogin($login);
                            } catch (InvalidArgumentException $e) {
                                Session::authorize($login, false, false, false);
                                echo 'ok';
                                exit;
                            }
                            $user->setPassword(rand(1000, 9000));
                            $user->setRole(Process::$context['vk']['user_role']);
                            $user->setPhoto($profile['photo_100']);
                            $user->setFullName($profile['first_name'] . ' ' . $profile['last_name']);
                            $user->setGender((intval($profile['sex']) === 2) ?
                                    UserProfile::GENDER_MAN : UserProfile::GENDER_WOMAN);
                            $user->save();
                            Session::authorize($login, false, false, false);
                            try {
                                $token = Session::getToken();
                                Database::getInstance()->query("DELETE FROM captcha WHERE token='$token'");
                            } catch (Exception $e) {}
                            echo 'ok';
                            exit;
                        } catch (Exception $e) {
                            header('Content-Type: application/json');
                            echo json_encode($e->getMessage());
                            exit;
                        }
                    } else {
                        throw new ForbiddenException('attack');
                    }
                    break;
                default:
                    throw new ForbiddenException('unsupported provider');
            }
        }
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
            try {
                if (mb_strlen($commentWhichWasPosted, 'utf8') < 2)
                    throw new InvalidArgumentException('Слишком мало текста, попробуйте больше!');
                Process::$context['last_comment_deep'] = $commentWhichWasPosted;
                if (!Session::getUid()) {
                    if (!$captcha = Data::input('captcha'))
                        throw new InvalidArgumentException('Не введён код с картинки');
                    Process::load('GDCaptcha');
                    if (!GDCaptcha::checkCorrect($captcha)) {
                        throw new InvalidArgumentException("Неверно введён код с картинки");
                    }
                }
                if (Comments::create($newsId, $commentWhichWasPosted)) {
                    Process::$context['last_comment_deep'] = null;
                } else {
                    throw new InvalidArgumentException('Разрешено оставлять не более 1 комментария за 10 секунд');
                }
            } catch (InvalidArgumentException $e) {
                Process::$context['flash_error'] = $e->getMessage();
            }
        }

        Process::$context['page_title'] = $post->title;
        Process::$context['news_content'] = $post->content;
        if (isset($post->tags) and $post->tags) {
            Process::$context['news_tags'] = preg_replace('/([^,]+),?/us',
                '<a href="/look/$1" class="tag-link">
                    <span class="label label-default">$1</span>
                </a>', $post->tags);
        }
        Process::$context['news_created_at'] = $post->created_at;
        Process::$context['news_poster_id'] = $post->poster_id;
        Process::$context['news_poster_login'] = $post->poster_login;
        Process::$context['comments'] = Comments::listingForNewsId($newsId);

        if (!isset(Process::$context['current_user']))
            Process::$context['current_user'] = array(
                'id' => false, 'login' => false, 'nickname' => false, 'full_name' => false, 'role' => false,
            );

        if (!empty(Process::$context['current_user']['nickname'])) {
            $displayName = Process::$context['current_user']['nickname'];
        } elseif (!empty(Process::$context['current_user']['full_name'])) {
            $displayName = Process::$context['current_user']['full_name'];
        } else {
            $displayName = Process::$context['current_user']['login'];
        }

        Process::$context['current_user']['display_name'] =
            $displayName ? $displayName : 'Гость';

        if (Process::$context['current_user']['role'] === Process::$context['vk']['user_role']) {
            Process::$context['vk']['user_link'] = substr(Process::$context['current_user']['login'], 3);
        } elseif (Process::$context['current_user']['role'] === Process::$context['fb']['user_role']) {
            Process::$context['fb']['user_link'] = substr(Process::$context['current_user']['login'], 3);
        }
    }

    public static function logout()
    {
        if (Process::$context['csrf_token'] === Data::uriVar('csrf_token'))
        {
            Session::stop();
            $rPath = Data::uriVar('rpath');
            Process::redirect($rPath ? $rPath : '/');
        } else {
            throw new ForbiddenException();
        }
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

