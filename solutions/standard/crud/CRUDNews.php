<?php
/**
 * Class CRUDNews
 */
class CRUDNews extends CRUDObject
{
    protected $menuName = 'Новости';
    protected $menuCreate = 'создать еще одну новость';
    protected $tableName = 'news';
    protected $orderByField = 'created_at';
    protected $fields = array(
        'id' => array(
            'default' => 'null',
            'type' => 'integer',
        ),
        'title' => array(
            'type' => 'string',
            'description' => 'Заголовок',
            'display' => true,
        ),
        'content' => array(
            'type' => 'text',
			'description' => 'Содержание',
        ),
        'tags' => array(
            'type' => 'string',
			'description' => 'Ключевые слова',
        ),
        'created_at' => array(
            'default' => 'now',
            'type' => 'datetime',
            'display' => true,
        ),
        'posted_by' => array(
            'type' => 'select',
			'description' => 'Автор',
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