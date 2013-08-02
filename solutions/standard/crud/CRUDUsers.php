<?php
class CRUDUsers extends CRUDObject
{
    protected $menuName = 'Пользователи';
    protected $menuCreate = 'добавить пользователя';
    protected $tableName = 'users';
    protected $orderByField = 'registered_at';
    protected $fields = array(
        'id' => array(
            'default' => 'null',
            'type' => 'integer',
        ),
        'login' => array(
            'type' => 'string',
            'display' => true,
        ),
        'password' => array(
            'type' => 'string',
        ),
        'registered_at' => array(
            'default' => 'now',
            'type' => 'datetime',
            'display' => true,
        ),
        'role' => array(
            'type' => 'integer',
            'modify' => '($1)',
            'from' => array(
                'table' => 'roles',
                'field' => 'id',
                'as' => 'title',
            ),
            'display' => true,
        ),
    );
}