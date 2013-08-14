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
            CRUDField::PARAM_TYPE => CRUDField::TYPE_TEXT,
			CRUDField::PARAM_DESCRIPTION => 'Контент',
        ),
        'tags' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
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
            CRUDField::PARAM_ONE_TO_MANY_SETTINGS => array(
                CRUDField::PARAM_ONE_TO_MANY_JOIN_TABLE => 'users',
                CRUDField::PARAM_ONE_TO_MANY_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                CRUDField::PARAM_ONE_TO_MANY_TARGET_JOIN_TABLE_FIELD => 'login',
            ),
            CRUDField::PARAM_DISPLAY => true,
        ),
    );
}