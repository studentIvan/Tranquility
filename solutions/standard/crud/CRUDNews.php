<?php
/**
 * Class CRUDNews
 */
class CRUDNews extends CRUDObjectInterface
{
    protected $menuName = 'Новости';
    protected $menuCreate = 'создать еще одну новость';
    protected $tableName = 'news';
    protected $orderByField = 'created_at';
    protected $menuIcon = 'th-list';

    protected $RBACPolicy = array(
        'create' => 'any',
        'read' => 'any',
        'update' => 'any',
        'delete' => 'any',
    );

    protected $filterOptions = array(
        'filter_string' => true,
        'filter_date' => true,
        'filter_less_or_more' => false,
    );

    protected $fields = array(
        'id' => array(
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NULL,
            CRUDField::PARAM_DESCRIPTION => 'ID новости',
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
        ),
        'title' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            CRUDField::PARAM_DESCRIPTION => 'Заголовок',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'content' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_TEXT_VISUAL,
            CRUDField::PARAM_DESCRIPTION => 'Контент',
        ),
        'tags' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_TAGS,
            CRUDField::PARAM_MANY_TO_MANY_SETTINGS => array(
                CRUDField::MANY_TO_MANY_JOIN_DATA_TABLE => 'tags',
                CRUDField::MANY_TO_MANY_DATA_TABLE_TARGET_JOIN_TABLE_FIELD => 'name',
                CRUDField::MANY_TO_MANY_RELATION_TABLE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'news_id',
                CRUDField::MANY_TO_MANY_DATA_TABLE_JOIN_CONDITION_RELATION_TABLE_FIELD => 'tag_id',
            ),
            CRUDField::PARAM_DESCRIPTION => 'Теги',
        ),
        'created_at' => array(
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NOW,
            CRUDField::PARAM_DESCRIPTION => 'Время создания',
            CRUDField::PARAM_TYPE => CRUDField::TYPE_DATETIME,
            CRUDField::PARAM_DISPLAY => true,
        ),
        'posted_by' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
            CRUDField::PARAM_DESCRIPTION => 'Автор',
            CRUDField::PARAM_DEFAULT => array('Session', 'getUID'),
            CRUDField::PARAM_MODIFY => '<a href="#" class="tooltipped" data-toggle="tooltip" title="Автор новости">@$1</a>',
            CRUDField::PARAM_MANY_TO_ONE_SETTINGS => array(
                CRUDField::MANY_TO_ONE_JOIN_TABLE => 'users',
                CRUDField::MANY_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                CRUDField::MANY_TO_ONE_TARGET_JOIN_TABLE_FIELD => 'login',
            ),
            CRUDField::PARAM_DISPLAY => true,
        ),
    );
}