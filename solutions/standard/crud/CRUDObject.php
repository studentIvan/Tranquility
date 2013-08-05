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
    protected $elementsPerPage = 6;
    protected $onlyDisplay = false;

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
            $field['function'] = isset($field['function']) ? $field['function'] : false;
            $field['count_of'] = isset($field['count_of']) ? $field['count_of'] : false;
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
        $tmpFields = array_unique(
            array_merge(array($this->getDiffField()), $this->getDisplayable())
        );
        $allFields = $this->getFields();
        $foreignIndex = 'b';
        $orderFilter = false;
        $foreignIndexes = array();
        $joinString = $groupBy = '';
        foreach ($allFields as $f => $d) {
            if (in_array($f, $tmpFields)) {
                if ($d['type'] == 'calculated') {
                    foreach ($tmpFields as &$ff) {
                        if ($ff == $f) {
                            $ff = null;
                        }
                    }
                }
                if ($d['from']) {
                    foreach ($tmpFields as &$ff) {
                        if ($ff == $f) {
                            if (isset($d['from']['group'])) {
                                $foreignIndex = $d['from']['group'];
                            }
                            $foreignIndexes[] = $foreignIndex;
                            $ff = "$foreignIndex.{$d['from']['as']} AS $f";
                            $joinType = isset($d['from']['join']) ? $d['from']['join'] : 'INNER';
                            $on = isset($d['from']['on']) ? "a.{$d['from']['on']}" : "a.$f";
                            $joinString .= "
                            $joinType JOIN {$d['from']['table']} as $foreignIndex
                            ON $foreignIndex.{$d['from']['field']} = $on
                            ";
                        }
                    }
                }
                if ($d['count_of']) {
                    $groupBy = "
                        GROUP BY {$d['count_of']}
                    ";

                    foreach ($tmpFields as &$ff) {
                        if ($ff == $f) {
                            $ff = "COUNT(a.{$d['count_of']}) AS $f";
                            $orderFilter = $f;
                        }
                    }
                }
            }
        }

        $tmpFields2 = $tmpFields;
        $tmpFields = array();
        foreach ($tmpFields2 as $field) {
            if (!empty($field)) $tmpFields[] = $field;
        }

        foreach ($tmpFields as &$field) {
            $field = str_replace("a.COUNT", "COUNT", "a.$field");
            foreach ($foreignIndexes as $index) {
                $field = str_replace("a.$index.", "$index.", $field);
            }
        }

        $requiredFields = join(',', $tmpFields);
        $orderBy = $this->orderByField ? "ORDER BY a.{$this->orderByField} DESC" : '';
        $orderBy = $orderFilter ? str_replace("a.{$this->orderByField}", $this->orderByField, $orderBy) : $orderBy;
        $statement = Database::getInstance()->prepare("
            SELECT $requiredFields
            FROM {$this->tableName} AS a $joinString
            $groupBy $orderBy LIMIT :limit OFFSET :offset
        ");

        //echo $statement->queryString;
        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        /**
         * calculated field v.1.0
         */
        foreach ($allFields as $f => $d) {
            if ($d['type'] == 'calculated' and $d['display']) {
                foreach ($result as &$key) {
                    $calc = $d['function'];
                    $key[$f] = $this->$calc($key);
                }
            }
        }

        return $result;
    }
}