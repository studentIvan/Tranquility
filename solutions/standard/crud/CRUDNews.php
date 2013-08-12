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
	
    protected $fields = array(
        'id' => array(
            'default' => 'null',
			'description' => 'ID новости',
            'type' => 'integer',
        ),
        'title' => array(
            'type' => 'string',
            'description' => 'Заголовок',
            'display' => true,
        ),
        'content' => array(
            'type' => 'text',
			'description' => 'Контент',
        ),
        'tags' => array(
            'type' => 'string',
			'description' => 'Теги',
        ),
        'created_at' => array(
            'default' => 'now',
			'description' => 'Время создания',
            'type' => 'datetime',
            'display' => true,
        ),
        'posted_by' => array(
            'type' => 'integer',
			'description' => 'Автор',
			'default' => array('Session', 'getUID'),
            'modify' => '<a href="#" class="tooltipped" data-toggle="tooltip" title="Автор новости">@$1</a>',
            'from' => array(
                'table' => 'users',
                'field' => 'id',
                'as' => 'login',
            ),
            'display' => true,
        ),
    );
}