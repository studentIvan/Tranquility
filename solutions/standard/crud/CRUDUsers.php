<?php
class CRUDUsers extends CRUDObjectInterface
{
    protected $menuName = 'Пользователи';
    protected $menuCreate = 'добавить пользователя';
    protected $menuIcon = 'user';
    protected $tableName = 'users';
    protected $orderByField = 'registered_at';

    protected $filterOptions = array(
        'filter_string' => true,
        'filter_date' => true,
        'filter_less_or_more' => false,
    );

    protected $fields = array(
        'id' => array(
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NULL,
            CRUDField::PARAM_DESCRIPTION => 'ID пользователя',
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
        ),
        'login' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            CRUDField::PARAM_DESCRIPTION => 'Логин',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'password' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_PASSWORD,
            CRUDField::PARAM_DESCRIPTION => 'Пароль',
        ),
        'role' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_SELECT,
            CRUDField::PARAM_DESCRIPTION => 'Роль',
            CRUDField::PARAM_MANY_TO_ONE_SETTINGS => array(
                CRUDField::MANY_TO_ONE_JOIN_TABLE => 'roles',
                CRUDField::MANY_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                CRUDField::MANY_TO_ONE_TARGET_JOIN_TABLE_FIELD => 'title',
            ),
            CRUDField::PARAM_DISPLAY => true,
        ),
        'registered_at' => array(
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NOW,
            CRUDField::PARAM_DESCRIPTION => 'Дата и время регистрации',
            CRUDField::PARAM_TYPE => CRUDField::TYPE_DATETIME,
            CRUDField::PARAM_DISPLAY => true,
        ),
    );
}