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
			'description' => 'Логин',
            'modify' => '<a href="#" class="tooltipped" data-toggle="tooltip" title="Владелец профиля">@$1</a>',
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
			'display' => true,
        ),
        'photo' => array(
            'type' => 'image_uri',
            'modify' => '<img src="$1" alt="" height="40" />',
			'description' => 'Аватар (uri)',
			'display' => true,
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