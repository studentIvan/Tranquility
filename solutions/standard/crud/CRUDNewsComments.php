<?php
class CRUDNewsComments extends CRUDObjectInterface
{
    protected function setup()
    {
        $config = new CRUDConfig($this);

        $config->setMenuName('Комментарии');
        $config->setMenuCreate('добавить комментарий');
        $config->setTableName('news_comments');
        $config->setOrderByField('posted_at');
        $config->setMenuIcon('comments');

        $config->setFields(array(
            'id' => array(
                CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NULL,
                CRUDField::PARAM_DESCRIPTION => 'ID комментария',
                CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
            ),
            'avatar' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_CALCULATED,
                CRUDField::PARAM_MODIFY => '<img src="$1" height="50" alt="">',
                CRUDField::PARAM_DISPLAY_FUNCTION => 'avatarField',
                CRUDField::PARAM_DISPLAY => true,
            ),
            'message' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_TEXT,
                CRUDField::PARAM_DESCRIPTION => 'Текст',
                CRUDField::PARAM_DISPLAY_FUNCTION => 'messageField',
                CRUDField::PARAM_DISPLAY => true,
            ),
            'author_id' => array(
                CRUDField::PARAM_DESCRIPTION => 'Автор',
                CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
                CRUDField::PARAM_DEFAULT => array('Session', 'getUID'),
                CRUDField::PARAM_MODIFY => 'Написал $1',
                CRUDField::PARAM_MANY_TO_ONE_SETTINGS => array(
                    CRUDField::MANY_TO_ONE_JOIN_TABLE => 'users',
                    CRUDField::MANY_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                    CRUDField::MANY_TO_ONE_TARGET_JOIN_TABLE_FIELD => 'login',
                ),
                CRUDField::PARAM_DISPLAY => true,
                CRUDField::PARAM_COALESCE => 'Гость',
            ),
            'news_id' => array(
                CRUDField::PARAM_DESCRIPTION => 'ID новости',
                CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
                CRUDField::PARAM_MODIFY => '<div title="$1"
                    style="max-width: 150px; overflow:hidden; border-bottom: 1px dotted gray; cursor: help; ' .
                    'white-space: nowrap; text-overflow: ellipsis;">$1</div>',
                CRUDField::PARAM_MANY_TO_ONE_SETTINGS => array(
                    CRUDField::MANY_TO_ONE_JOIN_TABLE => 'news',
                    CRUDField::MANY_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                    CRUDField::MANY_TO_ONE_TARGET_JOIN_TABLE_FIELD => 'title',
                ),
                CRUDField::PARAM_DISPLAY => true,
            ),
            'posted_at' => array(
                CRUDField::PARAM_DESCRIPTION => 'Время создания',
                CRUDField::PARAM_TYPE => CRUDField::TYPE_DATETIME,
                CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NOW,
                CRUDField::PARAM_DISPLAY => true,
            ),
            'ip' => array(
                CRUDField::PARAM_DESCRIPTION => 'ip',
                CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
                CRUDField::PARAM_DISPLAY_FUNCTION => 'long2ip',
                CRUDField::PARAM_MODIFY => '<a href="http://ip-whois.net/ip_geo.php?ip=$1" target="_blank">$1</a>',
                CRUDField::PARAM_DEFAULT => ip2long($_SERVER['REMOTE_ADDR']),
            ),
        ));

        $config->addFilter(CRUDConfig::FILTER_STRING);
        $config->addFilter(CRUDConfig::FILTER_DATE);
    }

    public function messageField($message)
    {
        return preg_replace('/(@.+?),/u', '<span class="comment-nick">$1</span>,', $message);
    }

    public function avatarField($data)
    {
        $author = $data['author_id'];
        if ($author !== 'Гость') {
            $sql = "SELECT p.photo FROM users AS u
            LEFT JOIN users_data AS p ON p.user_id=u.id WHERE u.login=:login LIMIT 1";
            $statement = Database::getInstance()->prepare($sql);
            $statement->bindParam(':login', $author);
            if ($statement->execute()) {
                $avatar = $statement->fetchColumn();
                return empty($avatar) ? '/img/anonymous.png' : $avatar;
            } else {
                return '/img/anonymous.png';
            }
        } else {
            return '/img/anonymous.png';
        }
    }
} 