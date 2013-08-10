<?php
class CRUDUsersData extends CRUDObjectInterface
{
    protected $menuName = 'Профили';
    protected $menuCreate = 'добавить профиль';
    protected $tableName = 'users_data';
    protected $menuIcon = 'icon-file';
    protected $diffField = 'user_id';
    protected $orderByField = 'user_id';
    protected $fields = array(
        'user_id' => array(
            'type' => 'integer',
			'description' => 'User ID',
        ),
        'login' => array(
            'type' => 'infinity',
            'from' => array(
                'table' => 'users',
                'field' => 'id',
                'as' => 'login',
                'on' => 'user_id',
            ),
            'display' => true,
        ),
        'nickname' => array(
            'type' => 'string',
			'description' => 'Ник',
            'display' => true,
        ),
        'full_name' => array(
            'type' => 'string',
			'description' => 'ФИО',
            'display' => true,
        ),
        'email' => array(
            'type' => 'email',
			'description' => 'Email',
        ),
        'photo' => array(
            'type' => 'image_uri',
			'description' => 'Аватар (uri)',
        ),
        'gender' => array(
            'type' => 'select',
			'description' => 'Пол',
            'values' => array(
                'm' => 'парень',
                'w' => 'девушка',
            ),
            'display' => true,
        ),
        'birthday' => array(
            'type' => 'date',
			'description' => 'Дата рождения',
        ),
        'non_indexed_data' => array(
            'type' => 'text',
            'default' => 'null',
        ),
    );
}