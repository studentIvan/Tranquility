<?php
abstract class CRUDObjectInterface
{
    /**
     * Menu name
     * @var string
     */
    protected $menuName = 'Something';

    /**
     * (Create new) string
     * @var string
     */
    protected $menuCreate = 'create new';

    /**
     * Menu Icon from "Icon glyphs" set
     * @var string
     */
    protected $menuIcon = 'folder-open';

    protected $tableName = '';
    protected $diffField = 'id';
    protected $orderByField = 'id';
    protected $fields = array();
    private $driver = false;
    private $filter = false;
    protected $elementsPerPage = 5;
    protected $onlyDisplay = false;
    protected $debugSQL = false;

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

    /**
     *
     */
    public function __construct($driver = 'MySQL')
    {
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

            if (isset($field['values']) and $field['type'] == 'select') {
                $field['options'] = array();
                foreach ($field['values'] as $key => $value) {
                    $field['options'][] = array('name' => $value, 'value' => $key);
                }
            }
        }
    }

    /**
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
        return ($this->RBACPolicy[$action] == 'any' or in_array(Session::getRole(), $this->RBACPolicy[$action]));
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