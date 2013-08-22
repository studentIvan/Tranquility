<?php
class CRUDNewsComments extends CRUDObjectInterface
{
    protected $menuName = 'Комментарии';
    protected $menuCreate = 'добавить комментарий';
    protected $tableName = 'news_comments';
    protected $orderByField = 'posted_at';
    protected $menuIcon = 'comment';

    protected $filterOptions = array(
        'filter_string' => true,
        'filter_date' => true,
        'filter_less_or_more' => false,
    );

    protected $fields = array(
        'id' => array(
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NULL,
            CRUDField::PARAM_DESCRIPTION => 'ID комментария',
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
        ),
        'parent_id' => array(
            CRUDField::PARAM_DESCRIPTION => 'ID родителя',
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
            CRUDField::PARAM_PLACEHOLDER => 'нет родителя',
        ),
        'message' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_TEXT,
            CRUDField::PARAM_DESCRIPTION => 'Текст',
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
            CRUDField::PARAM_MODIFY => 'К записи &quot;$1&quot;',
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
    );
} 