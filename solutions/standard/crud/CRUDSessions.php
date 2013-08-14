<?php
class CRUDSessions extends CRUDObjectInterface
{
    protected $menuName = 'Онлайн';
    protected $menuCreate = false;
    protected $tableName = 'sessions';
    protected $menuIcon = 'icon-barcode';
    protected $diffField = 'token';
    protected $orderByField = 'uptime';
    protected $onlyDisplay = true;
	
    protected $fields = array(
        'token' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
        ),
        'status' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_CALCULATED,
            CRUDField::PARAM_DISPLAY_FUNCTION => 'statusField',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'role' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_SELECT,
            CRUDField::PARAM_ONE_TO_MANY_SETTINGS => array(
                CRUDField::PARAM_ONE_TO_MANY_JOIN_TABLE => 'roles',
                CRUDField::PARAM_ONE_TO_MANY_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                CRUDField::PARAM_ONE_TO_MANY_TARGET_JOIN_TABLE_FIELD => 'title',
                CRUDField::PARAM_ONE_TO_MANY_JOIN_TYPE => CRUDField::JOIN_TYPE_LEFT,
                CRUDField::PARAM_ONE_TO_MANY_JOIN_GROUP => 'r',
            ),
            CRUDField::PARAM_DISPLAY => true,
        ),
        'ip' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
            CRUDField::PARAM_DISPLAY_FUNCTION => 'long2ip',
            CRUDField::PARAM_MODIFY => '<a href="http://ip-whois.net/ip_geo.php?ip=$1" target="_blank">$1</a>',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'useragent' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            CRUDField::PARAM_DISPLAY => true,
        ),
        'uid' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_SELECT,
            CRUDField::PARAM_MODIFY => '<a href="#" class="tooltipped" data-toggle="tooltip" title="Этот пользователь авторизован">@$1</a>',
            CRUDField::PARAM_ONE_TO_MANY_SETTINGS => array(
                CRUDField::PARAM_ONE_TO_MANY_JOIN_TABLE => 'users',
                CRUDField::PARAM_ONE_TO_MANY_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                CRUDField::PARAM_ONE_TO_MANY_TARGET_JOIN_TABLE_FIELD => 'login',
                CRUDField::PARAM_ONE_TO_MANY_JOIN_TYPE => CRUDField::JOIN_TYPE_LEFT,
                CRUDField::PARAM_ONE_TO_MANY_JOIN_GROUP => 'u',
            ),
            CRUDField::PARAM_DISPLAY => true,
        ),
        'uptime' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_DATETIME,
            CRUDField::PARAM_DISPLAY => true,
        )
    );

    public function statusField($key) 
	{
        if (Session::getToken() == $key['token']) {
            return '<i class="icon-eye-close"></i>
            <div style="position: absolute; top: 25px;
             color: lightgray; white-space: nowrap">
            * это ваша текущая сессия
            </div>';
        } else {
            return '<i class="icon-eye-open"></i>';
        }
    }
}