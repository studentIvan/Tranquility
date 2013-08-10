<?php
class CRUDRoles extends CRUDObjectInterface
{
    protected $menuName = 'Роли';
    protected $menuCreate = 'новая роль';
    protected $tableName = 'roles';
    protected $menuIcon = 'icon-briefcase';
    protected $fields = array(
        'id' => array(
            'default' => 'null',
			'description' => 'ID роли',
            'type' => 'integer',
        ),
        'title' => array(
            'type' => 'string',
			'description' => 'Наименование',
            'display' => true,
        ),
        'info' => array(
            'type' => 'calculated',
            'function' => 'roleTypeField',
            'display' => true,
        ),
    );

    public function roleTypeField($key) 
	{
        $roleId = $key['id'];
        $sOpts = Session::getOptions();
        if ($roleId == $sOpts['bot_role']) {
            return '* <span style="color: darkblue">роль, присваиваемая поисковым ботам (id = ' . $roleId . ')</span>';
        } elseif ($roleId == $sOpts['guest_role']) {
            return '* <span style="color: darkgreen">роль, присваиваемая всем автоматически (id = ' . $roleId . ')</span>';
        } elseif ($roleId == 1) {
            return '* <span style="color: darkred">главная роль, дающая полный доступ к админ-панели (id = ' . $roleId . ')</span>';
        } else {
            return '* пользовательская роль (id = ' . $roleId . ')';
        }
    }
}