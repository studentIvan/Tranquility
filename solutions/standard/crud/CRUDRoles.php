<?php
class CRUDRoles extends CRUDObjectInterface
{
    protected $menuName = 'Роли';
    protected $menuCreate = 'новая роль';
    protected $tableName = 'roles';
    protected $menuIcon = 'icon-briefcase';

    protected $fields = array(
        'id' => array(
            CRUDField::PARAM_DEFAULT => CRUDField::DEFAULT_NULL,
            CRUDField::PARAM_DESCRIPTION => 'ID роли',
            CRUDField::PARAM_TYPE => CRUDField::TYPE_INTEGER,
        ),
        'title' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_STRING,
            CRUDField::PARAM_DESCRIPTION => 'Название',
            CRUDField::PARAM_DISPLAY => true,
        ),
        'info' => array(
            CRUDField::PARAM_TYPE => CRUDField::TYPE_CALCULATED,
            CRUDField::PARAM_DISPLAY_FUNCTION => 'roleTypeField',
            CRUDField::PARAM_DISPLAY => true,
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