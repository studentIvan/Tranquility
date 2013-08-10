<?php
class CRUDUsers extends CRUDObject
{
    protected $menuName = 'Пользователи';
    protected $menuCreate = 'добавить пользователя';
    protected $menuIcon = 'icon-user';
    protected $tableName = 'users';
    protected $orderByField = 'registered_at';
    protected $fields = array(
        'id' => array(
            'default' => 'null',
			'description' => 'ID пользователя',
            'type' => 'integer',
        ),
        'login' => array(
            'type' => 'string',
			'description' => 'Логин',
            'display' => true,
        ),
        'password' => array(
            'type' => 'password',
			'description' => 'Пароль',
        ),
        'role' => array(
            'type' => 'select',
			'description' => 'Роль',
            'from' => array(
                'table' => 'roles',
                'field' => 'id',
                'as' => 'title',
            ),
            'display' => true,
        ),
        'registered_at' => array(
            'default' => 'now',
			'description' => 'Дата и время регистрации',
            'type' => 'datetime',
            'display' => true,
        ),
    );
}