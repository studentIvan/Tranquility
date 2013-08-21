<?php
class CRUDUsersData extends CRUDObjectInterface
{
    protected $menuName = 'Профили';
    protected $menuCreate = 'добавить профиль';
    protected $tableName = 'users_data';
    protected $menuIcon = 'link';
    protected $diffField = 'user_id';
    protected $orderByField = 'user_id';

    /*protected $filterOptions = array(
        'filter_string' => true,
        'filter_date' => false,
        'filter_less_or_more' => false,
    );*/

    protected $fields = array(
        'user_id' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
            CRUDField::PARAM_DESCRIPTION => 'User ID',
        ),
        'login' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INFINITY,
            CRUDField::PARAM_DESCRIPTION => 'Логин',
            CRUDField::PARAM_MODIFY => '<a href="#" class="tooltipped" data-toggle="tooltip" title="Владелец профиля">@$1</a>',
            CRUDField::PARAM_ONE_TO_ONE_SETTINGS => array(
                CRUDField::ONE_TO_ONE_JOIN_TABLE => 'users',
                CRUDField::ONE_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                CRUDField::ONE_TO_ONE_TARGET_JOIN_TABLE_FIELD => 'login',
                CRUDField::ONE_TO_ONE_JOIN_CONDITION_THIS_TABLE_FIELD => 'user_id',
            ),
            CRUDField::PARAM_DISPLAY => true,
        ),
        'nickname' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            CRUDField::PARAM_DESCRIPTION => 'Ник',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'full_name' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            CRUDField::PARAM_DESCRIPTION => 'ФИО',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'email' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_EMAIL,
            CRUDField::PARAM_DESCRIPTION => 'Email',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'photo' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            CRUDField::PARAM_MODIFY => '<img src="$1" alt="" height="40" />',
            CRUDField::PARAM_DESCRIPTION => 'Аватар (uri)',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'gender' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_SELECT,
            CRUDField::PARAM_DESCRIPTION => 'Пол',
            CRUDField::PARAM_STATIC_VALUES => array(
                'm' => 'парень',
                'w' => 'девушка',
            ),
            CRUDField::PARAM_DISPLAY => true,
        ),
        'birthday' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_DATE,
            CRUDField::PARAM_DESCRIPTION => 'Дата рождения',
        ),
        'non_indexed_data' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_TEXT,
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NULL,
        ),
    );
}