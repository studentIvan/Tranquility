<?php
class CRUDConfig
{
    const FILTER_STRING = 'filter_string';
    const FILTER_DATE = 'filter_date';
    const FILTER_LESS_OR_MORE = 'filter_less_or_more';

    const ACTION_CREATE = 'create';
    const ACTION_READ = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    const FULL_ACCESS = 'any';

    /**
     * @var CRUDObjectInterface
     */
    private $object = null;

    public function __construct(CRUDObjectInterface $object) {
        $this->object = $object;
    }

    /**
     * @param string $filter
     * @return CRUDConfig
     */
    public function addFilter($filter)
    {
        $this->object->addFilter($filter);
        return $this;
    }

    /**
     * @param string $action
     * @param array|string $policy
     * @return CRUDConfig
     */
    public function setPolicyForAction($action, $policy)
    {
        $this->object->setPolicyForAction($action, $policy);
        return $this;
    }

    /**
     * @param string $tableName
     * @return CRUDConfig
     */
    public function setTableName($tableName)
    {
        $this->object->setTableName($tableName);
        return $this;
    }

    /**
     * @param string|bool $menuCreate
     * @return CRUDConfig
     */
    public function setMenuCreate($menuCreate)
    {
        $this->object->setMenuCreate($menuCreate);
        return $this;
    }

    /**
     * @param string $menuIcon
     * @return CRUDConfig
     */
    public function setMenuIcon($menuIcon)
    {
        $this->object->setMenuIcon($menuIcon);
        return $this;
    }

    /**
     * @param string $menuName
     * @return CRUDConfig
     */
    public function setMenuName($menuName)
    {
        $this->object->setMenuName($menuName);
        return $this;
    }

    /**
     * @param string $orderByField
     * @return CRUDConfig
     */
    public function setOrderByField($orderByField)
    {
        $this->object->setOrderByField($orderByField);
        return $this;
    }

    /**
     * @param string $diffField
     * @return CRUDConfig
     */
    public function setDiffField($diffField)
    {
        $this->object->setDiffField($diffField);
        return $this;
    }

    /**
     * @param bool $onlyDisplay
     * @return CRUDConfig
     */
    public function setOnlyDisplay($onlyDisplay)
    {
        $this->object->setOnlyDisplay($onlyDisplay);
        return $this;
    }

    /**
     * @param integer $n
     * @return CRUDConfig
     */
    public function setElementsPerPage($n)
    {
        $this->object->setElementsPerPage($n);
        return $this;
    }

    /**
     * @param array $fields
     * @return CRUDConfig
     */
    public function setFields($fields)
    {
        $this->object->setFields($fields);
        return $this;
    }
}