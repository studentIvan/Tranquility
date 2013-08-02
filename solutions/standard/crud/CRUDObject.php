<?php
abstract class CRUDObject
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
    protected $driver = 'MySQL';
    protected $elementsPerPage = 15;

    /**
     *
     */
    public function __construct()
    {
        foreach ($this->fields as &$field) {
            $field['default'] = isset($field['default']) ? $field['default'] : false;
            $field['display'] = isset($field['display']) ? $field['display'] : false;
            $field['from'] = isset($field['from']) ? $field['from'] : false;
            $field['modify'] = isset($field['modify']) ? $field['modify'] : false;
        }
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
     * @return array
     */
    public function getInfo()
    {
        return array(
            'name' => $this->getMenuName(),
            'uri' => $this->getMenuURI(),
            'icon' => $this->getMenuIconClass(),
        );
    }

    /**
     * @return int
     */
    public function getCount()
    {
        $function = "get{$this->driver}Count";
        return $this->$function();
    }

    /**
     * @return int
     */
    protected function getMySQLCount()
    {
        return Database::count($this->tableName);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getListing($offset = 0, $limit = 30)
    {
        $function = "get{$this->driver}Listing";
        return $this->$function($offset, $limit);
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

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getMySQLListing($offset = 0, $limit = 30)
    {
        $tmpFields = array_merge(array($this->getDiffField()), $this->getDisplayable());
        $allFields = $this->getFields();
        $foreignIndex = 'b';
        $joinString = '';
        foreach ($allFields as $f => $d) {
            if ($d['from'] and in_array($f, $tmpFields)) {
                foreach ($tmpFields as &$ff) {
                    if ($ff == $f) {
                        $ff = "$foreignIndex.{$d['from']['as']} AS $f";
                        $joinString = "
                            INNER JOIN {$d['from']['table']} as $foreignIndex
                            ON $foreignIndex.{$d['from']['field']} = a.$f
                            ";
                    }
                }
            }
        }
        foreach ($tmpFields as &$field)
            $field = str_replace("a.$foreignIndex.", "$foreignIndex.", "a.$field");
        $requiredFields = join(',', $tmpFields);
        $orderBy = $this->orderByField ? "ORDER BY a.{$this->orderByField} DESC" : '';
        $statement = Database::getInstance()->prepare("
            SELECT $requiredFields
            FROM {$this->tableName} AS a $joinString
            $orderBy LIMIT :limit OFFSET :offset
        ");

        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}