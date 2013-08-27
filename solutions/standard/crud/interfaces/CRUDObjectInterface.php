<?php
abstract class CRUDObjectInterface
{
    /**
     * Menu name
     * @var string
     */
    private $menuName = 'Something';

    /**
     * (Create new) string
     * @var string
     */
    private $menuCreate = 'create new';

    /**
     * Menu Icon from "Icon glyphs" set
     * @var string
     */
    private $menuIcon = 'folder-open';

    private $tableName = '';
    private $diffField = 'id';
    private $orderByField = 'id';
    private $fields = array();
    private $driver = false;
    private $filter = false;
    private $elementsPerPage = 5;
    private $onlyDisplay = false;
    private $debugSQL = DEVELOPER_MODE;

    /**
     * Who can work with it?
     * @var array
     */
    protected $RBACPolicy = array(
        'create' => array(1),
        'read' => array(1),
        'update' => array(1),
        'delete' => array(1),
    );

    /**
     * Do you wanna filter?
     * @var array
     */
    protected $filterOptions = array(
        'filter_string' => false,
        'filter_date' => false,
        'filter_less_or_more' => false,
    );

    protected $setupBlock = false;

    /**
     *
     */
    public function __construct($driver = 'MySQL')
    {
        $this->setup();
        $this->setupBlock = true;

        if (empty($this->tableName)) {
            throw new Exception('Вы не указали таблицу (tableName) в настройках этой модели');
        }

        $driver = "CRUD{$driver}Driver";

        if (!class_exists($driver))
            include_once dirname(__FILE__) . "/../drivers/{$driver}.php";

        $this->driver = new $driver($this);

        if (($this->driver instanceof CRUDDriverInterface) === false)
            throw new Exception('CRUD driver interface error');

        foreach ($this->fields as &$field) {
            $field['default'] = isset($field['default']) ? $field['default'] : false;
            $field['display'] = isset($field['display']) ? $field['display'] : false;
            $field['from'] = isset($field['from']) ? $field['from'] : false;
            $field['from_many'] = isset($field['from_many']) ? $field['from_many'] : false;
            $field['modify'] = isset($field['modify']) ? $field['modify'] : false;
            $field['function'] = isset($field['function']) ? $field['function'] : false;
            $field['count_of'] = isset($field['count_of']) ? $field['count_of'] : false;
            $field['coalesce'] = isset($field['coalesce']) ? $field['coalesce'] : false;

            if (is_array($field['default'])) {
                $field['default'] = call_user_func($field['default']);
            }

            if ($field['from'] and $field['type'] == 'select') {
                $field['options'] = $this->getSelectOptions($field['from']['table'], $field['from']['field'], $field['from']['as']);
            }

            if ($field['from']) {
                $field['from']['group'] = 'x' . substr(md5(rand(1000, 9000)), 0, 5);
            }

            if ($field['from_many']) {
                $field['from_many']['group_many_relation'] = 'y' . substr(md5(rand(1000, 9000)), 0, 5);
                $field['from_many']['group_many_data'] = 'z' . substr(md5(rand(1000, 9000)), 0, 5);
            }

            if (isset($field['values']) and $field['type'] == 'select') {
                $field['options'] = array();
                foreach ($field['values'] as $key => $value) {
                    $field['options'][] = array('name' => $value, 'value' => $key);
                }
            }
        }

        if (isset($this->fields[$this->diffField]) and $this->fields[$this->diffField]['modify']) {
            $this->fields[$this->diffField]['modify'] = false;
            Process::$context['flash_warning'] =
                'Параметр modify не поддерживается уникальным полем, создайте дополнительное поле';
        }

        do
        {
            $fieldsCursor = 0;
            $fieldBugFind = false;
            foreach ($this->fields as $fieldKey => $fieldData)
            {
                if ($fieldData['display'] and $fieldData['from'] and !$fieldData['default']
                    and ($fieldData['type'] == 'integer' or $fieldData['type'] == 'number'))
                {
                    $fieldCopy = $fieldData;
                    $fieldCopy['type'] = 'infinity';
                    if ($fieldCopy['description'])
                        $fieldCopy['description'] .= ' (объект)';
                    $fieldCopy['from']['group'] = 'x' . substr(md5(rand(1000, 9000)), 0, 5);

                    $fieldData['display'] = false;
                    $fieldData['from'] = false;
                    $this->fields[$fieldKey] = $fieldData;

                    Data::arrayPutToPosition(
                        $this->fields, $fieldCopy, $fieldsCursor + 1, $fieldKey . '_inf'
                    );

                    $fieldBugFind = true;
                }

                if ($fieldBugFind) {
                    $fieldBugFind = false;
                    break;
                }

                $fieldsCursor++;
            }
        }
        while ($fieldBugFind);
    }

    protected function setup()
    {

    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        if (!$this->setupBlock)
            $this->tableName = $tableName;
    }

    /**
     * @param $menuCreate
     */
    public function setMenuCreate($menuCreate)
    {
        if (!$this->setupBlock)
            $this->menuCreate = $menuCreate;
    }

    /**
     * @param $menuIcon
     */
    public function setMenuIcon($menuIcon)
    {
        if (!$this->setupBlock)
            $this->menuIcon = $menuIcon;
    }

    /**
     * @param $menuName
     */
    public function setMenuName($menuName)
    {
        if (!$this->setupBlock)
            $this->menuName = $menuName;
    }

    /**
     * @param $orderByField
     */
    public function setOrderByField($orderByField)
    {
        if (!$this->setupBlock)
            $this->orderByField = $orderByField;
    }

    /**
     * @param $diffField
     */
    public function setDiffField($diffField)
    {
        if (!$this->setupBlock)
            $this->diffField = $diffField;
    }

    /**
     * @param $onlyDisplay
     */
    public function setOnlyDisplay($onlyDisplay)
    {
        if (!$this->setupBlock)
            $this->onlyDisplay = $onlyDisplay;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        if (!$this->setupBlock)
            $this->fields = $fields;
    }

    /**
     * @param $filter
     */
    public function addFilter($filter)
    {
        if (!$this->setupBlock)
            $this->filterOptions[$filter] = true;
    }

    /**
     * @param string $action
     * @param array|string $roles
     */
    public function setPolicyForAction($action, $roles)
    {
        if (!$this->setupBlock)
            $this->RBACPolicy[$action] = $roles;
    }

    /**
     * @param $n
     */
    public function setElementsPerPage($n)
    {
        if (!$this->setupBlock)
            $this->elementsPerPage = $n;
    }

    /**
     * @throws Exception
     * @return CRUDDriverInterface
     */
    protected function getDriver()
    {
        if (!$this->driver) throw new Exception('CRUD driver not exists');
        return $this->driver;
    }

    /**
     * @return bool
     */
    public function getSQLDebugState()
    {
        return $this->debugSQL;
    }

    /**
     * @param string $table
     * @param string $valueField
     * @param string $descriptionField
     * @return array
     */
    protected function getSelectOptions($table, $valueField, $descriptionField)
    {
        return $this->getDriver()->getSelectOptions($table, $valueField, $descriptionField);
    }

    /**
     * @return array
     */
    public function getDisplayable()
    {
        $displayable = array();
        foreach ($this->fields as $field => $data) {
            if ($data['display']) $displayable[] = $field;
        }

        return $displayable;
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
    public function getCreateString()
    {
        return $this->menuCreate;
    }

    /**
     * @param string $action
     * @return bool
     */
    public function checkRBACPolicy($action = 'read')
    {
        return ($this->RBACPolicy[$action] === 'any' or in_array(Session::getRole(), $this->RBACPolicy[$action], true));
    }

    /**
     * @return string
     */
    public function getMenuURI()
    {
        return strtolower(get_called_class());
    }

    /**
     * @param array $filter
     */
    public function setFilter($filter)
    {
        $filter['text'] = trim($filter['text']);
        if (empty($filter['text']))
            $filter['text'] = false;
        if ($filter['lm'] and !$filter['text'])
            $filter['lm'] = false;
        if ($filter['lm'] and $filter['text'] and $filter['lm'] !== 'contain') {
            $filter['lm'] = array('operator' => $filter['lm'], 'operand' => $filter['text']);
            $filter['text'] = false;
        }
        $this->filter = $filter;
    }

    /**
     * @return array|bool
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return int
     */
    public function getElementsPerPageNum()
    {
        return $this->elementsPerPage;
    }

    /**
     * @return string
     */
    public function getMenuIconClass()
    {
        return $this->menuIcon;
    }

    /**
     * @return bool
     */
    public function isOnlyDisplay()
    {
        return $this->onlyDisplay;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return ($this->checkRBACPolicy()) ? array(
            'name' => $this->getMenuName(),
            'uri' => $this->getMenuURI(),
            'icon' => $this->getMenuIconClass(),
            'count' => $this->getCount(),
        ) : false;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        try {
            return $this->getDriver()->getCount();
        }
        catch (Exception $e) {
            return 0;
        }
    }

    /**
     * @param array $postedData
     * @throws ForbiddenException
     * @return bool
     */
    public function create($postedData)
    {
        if (!$this->checkRBACPolicy('create'))
            throw new ForbiddenException();
        return $this->getDriver()->create($postedData);
    }

    /**
     * @param string $unique
     * @param array $postedData
     * @throws ForbiddenException
     * @return bool
     */
    public function update($unique, $postedData)
    {
        if (!$this->checkRBACPolicy('update'))
            throw new ForbiddenException();
        return $this->getDriver()->update($unique, $postedData);
    }

    /**
     * @param mixed $unique
     * @throws ForbiddenException
     * @return bool
     */
    public function delete($unique)
    {
        if (!$this->checkRBACPolicy('delete'))
            throw new ForbiddenException();
        return $this->getDriver()->delete($unique);
    }

    /**
     * @throws ForbiddenException
     * @return bool
     */
    public function truncate()
    {
        if (!$this->checkRBACPolicy('delete'))
            throw new ForbiddenException();
        return $this->getDriver()->truncate();
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getOrderByField()
    {
        return $this->orderByField;
    }

    /**
     * @return array
     */
    public function getFilterOptions()
    {
        return $this->filterOptions;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @throws ForbiddenException
     * @return array
     */
    public function getListing($offset = 0, $limit = 30)
    {
        if (!$this->checkRBACPolicy()) throw new ForbiddenException();
        return $this->getDriver()->getListing($offset, $limit);
    }

    /**
     * @param mixed $unique
     * @throws ForbiddenException
     * @return array
     */
    public function readElement($unique)
    {
        if (!$this->checkRBACPolicy()) throw new ForbiddenException();
        try {
            return $this->getDriver()->readElement($unique);
        } catch (PDOException $e) {
            Process::$context['flash_error'] = $e->getMessage();
            return array();
        }
    }

    /**
     * @return string
     */
    public function getDiffField()
    {
        return $this->diffField;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}