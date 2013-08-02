<?php
class CRUDRoles extends CRUDObject
{
    protected $menuName = 'Роли пользователей';
    protected $menuCreate = 'новая роль';
    protected $tableName = 'roles';
    protected $fields = array(
        'id' => array(
            'default' => 'null',
            'type' => 'integer',
        ),
        'title' => array(
            'type' => 'string',
            'display' => true,
        ),
    );
}