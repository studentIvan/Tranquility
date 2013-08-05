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
            'display' => true,
        ),
        'content' => array(
            'type' => 'text',
        ),
        'tags' => array(
            'type' => 'string',
        ),
        'created_at' => array(
            'default' => 'now',
            'type' => 'datetime',
            'display' => true,
        ),
        'posted_by' => array(
            'type' => 'select',
            'modify' => '<a href="#">@$1</a>',
            'from' => array(
                'table' => 'users',
                'field' => 'id',
                'as' => 'login',
            ),
            'display' => true,
        ),
    );
}