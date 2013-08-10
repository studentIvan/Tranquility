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
    protected $menuIcon = 'icon-folder-open';

    protected $tableName = '';
    protected $diffField = 'id';
    protected $orderByField = 'id';
    protected $fields = array();
    protected $driver = false;
    protected $elementsPerPage = 6;
    protected $onlyDisplay = false;

    /**
     *
     */
    public function __construct($driver = 'MySQL')
    {		
		$driver = "CRUD{$driver}Driver";
		
		if (!class_exists($driver)) 
			include_once dirname(__FILE__) . "/../drivers/{$driver}.php";
			
		$this->driver = new $driver($this);
		
		if (($this->driver instanceof CRUDDriverInterface) === false)
			throw new Exception('CRUD driver interface error');
	
        foreach ($this->fields as &$field) 
		{
            $field['default'] = isset($field['default']) ? $field['default'] : false;
            $field['display'] = isset($field['display']) ? $field['display'] : false;
            $field['from'] = isset($field['from']) ? $field['from'] : false;
            $field['modify'] = isset($field['modify']) ? $field['modify'] : false;
            $field['function'] = isset($field['function']) ? $field['function'] : false;
            $field['count_of'] = isset($field['count_of']) ? $field['count_of'] : false;
			
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
     * @return string
     */
    public function getMenuURI()
    {
        return strtolower(get_called_class());
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
        return array(
            'name' => $this->getMenuName(),
            'uri' => $this->getMenuURI(),
            'icon' => $this->getMenuIconClass(),
            'count' => $this->getCount(),
        );
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->getDriver()->getCount();
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
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getListing($offset = 0, $limit = 30)
    {
        return $this->getDriver()->getListing($offset, $limit);
    }
	
	/**
     * @param mixed $unique
     * @return array
     */
    public function readElement($unique)
    {
        return $this->getDriver()->readElement($unique);
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