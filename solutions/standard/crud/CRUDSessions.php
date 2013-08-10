<?php
class CRUDSessions extends CRUDObject
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
            'type' => 'string',
        ),
        'status' => array(
            'type' => 'calculated',
            'function' => 'statusField',
            'display' => true,
        ),
        'role' => array(
            'type' => 'select',
            'from' => array(
                'table' => 'roles',
                'field' => 'id',
                'as' => 'title',
                'join' => 'left',
                'group' => 'r',
            ),
            'display' => true,
        ),
        'ip' => array(
            'type' => 'integer',
            'function' => 'long2ip',
            'modify' => '<a href="http://ip-whois.net/ip_geo.php?ip=$1" target="_blank">$1</a>',
            'display' => true,
        ),
        'useragent' => array(
            'type' => 'string',
            'display' => true,
        ),
        'uid' => array(
            'type' => 'select',
            'modify' => '<a href="#" class="tooltipped" data-toggle="tooltip" title="Этот пользователь авторизован">@$1</a>',
            'from' => array(
                'table' => 'users',
                'field' => 'id',
                'as' => 'login',
                'join' => 'left',
                'group' => 'u',
            ),
            'display' => true,
        ),
        'uptime' => array(
            'type' => 'datetime',
            'display' => true,
        )
    );

    protected function statusField($key) {
        $sOpts = Session::getOptions();
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