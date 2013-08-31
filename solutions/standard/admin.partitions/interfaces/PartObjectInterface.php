<?php
abstract class PartObjectInterface
{
    private $menuName, $menuIcon;

    /**
     * Who can work with it?
     * @var array
     */
    private $RBACPolicyAccess = array(1);

    protected function setMenuName($menuName)
    {
        $this->menuName = $menuName;
    }

    protected function setMenuIcon($menuIcon)
    {
        $this->menuIcon = $menuIcon;
    }

    /**
     * @return array|bool
     */
    public function getInfo()
    {
        return ($this->checkRBACPolicy()) ? array(
            'name' => $this->getMenuName(),
            'uri' => $this->getMenuURI(),
            'icon' => $this->getMenuIconClass(),
            'count' => false,
        ) : false;
    }

    /**
     * @return bool
     */
    public function checkRBACPolicy()
    {
        return ($this->RBACPolicyAccess === 'any' or in_array(Session::getRole(), $this->RBACPolicyAccess, true));
    }

    /**
     * @param $roles
     */
    public function setAccessFor($roles = 'any')
    {
        $this->RBACPolicyAccess = $roles;
    }

    /**
     * @return string
     */
    public function getMenuName()
    {
        return $this->menuName;
    }

    /**
     * @return string
     */
    public function getMenuURI()
    {
        return strtolower(get_called_class());
    }

    /**
     * @return string
     */
    public function getMenuIconClass()
    {
        return $this->menuIcon;
    }

    /**
     * @return string
     */
    public function main()
    {
        return 'Coming soon...';
    }
}