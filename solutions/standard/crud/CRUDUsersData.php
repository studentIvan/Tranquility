<?php
class CRUDUsersData extends CRUDObject
{
    protected $menuName = 'Профили';
    protected $menuCreate = 'добавить профиль';
    protected $tableName = 'users_data';
    protected $menuIcon = 'icon-file';
    protected $diffField = 'user_id';
    protected $orderByField = 'user_id';
    protected $fields = array(
        'user_id' => array(
            'default' => 'null',
            'type' => 'integer',
            'edit_as' => 'login',
        ),
        'login' => array(
            'type' => 'select',
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
            'display' => true,
        ),
        'full_name' => array(
            'type' => 'string',
            'display' => true,
        ),
        'email' => array(
            'type' => 'string',
        ),
        'photo' => array(
            'type' => 'image_uri',
        ),
        'gender' => array(
            'type' => 'select',
            'values' => array(
                'm' => 'парень',
                'w' => 'девушка',
            ),
            'display' => true,
        ),
        'birthday' => array(
            'type' => 'date',
        ),
        'non_indexed_data' => array(
            'type' => 'text',
        ),
    );
}