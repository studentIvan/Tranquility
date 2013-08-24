<?php
class CRUDSessions extends CRUDObjectInterface
{
    protected function setup()
    {
        $config = new CRUDConfig($this);

        $config->setMenuName('Онлайн');
        $config->setMenuCreate(false);
        $config->setTableName('sessions');
        $config->setDiffField('token');
        $config->setOrderByField('uptime');
        $config->setMenuIcon('dashboard');
        $config->setOnlyDisplay(true);

        $config->setFields(array(
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
                CRUDField::PARAM_MANY_TO_ONE_SETTINGS => array(
                    CRUDField::MANY_TO_ONE_JOIN_TABLE => 'roles',
                    CRUDField::MANY_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                    CRUDField::MANY_TO_ONE_TARGET_JOIN_TABLE_FIELD => 'title',
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
                CRUDField::PARAM_MANY_TO_ONE_SETTINGS => array(
                    CRUDField::MANY_TO_ONE_JOIN_TABLE => 'users',
                    CRUDField::MANY_TO_ONE_JOIN_CONDITION_JOIN_TABLE_FIELD => 'id',
                    CRUDField::MANY_TO_ONE_TARGET_JOIN_TABLE_FIELD => 'login',
                ),
                CRUDField::PARAM_DISPLAY => true,
            ),
            'uptime' => array(
                CRUDField::PARAM_TYPE => CRUDField::TYPE_DATETIME,
                CRUDField::PARAM_DISPLAY => true,
            )
        ));
    }

    public function statusField($key)
    {
        if (Session::getToken() == $key['token']) {
            return '<span class="glyphicon glyphicon-flash"></span>
            <div style="position: absolute; top: 25px;
             color: lightgray; white-space: nowrap">
            * это ваша текущая сессия
            </div>';
        } else {
            return '<span class="glyphicon glyphicon-flash"></span>';
        }
    }
}