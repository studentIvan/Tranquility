<?php
class CRUDMySQLDriver extends CRUDDriverInterface
{
    protected $manyToManyCreateRegistry = array();

    /**
     * @param string $table
     * @param string $valueField
     * @param string $descriptionField
     * @return array
     */
    public function getSelectOptions($table, $valueField, $descriptionField)
    {
        $statement = Database::getInstance()->prepare("
            SELECT $valueField as value, $descriptionField as name
            FROM $table
        ");

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @return int
     */
    public function getCount()
    {
        if (!$filter = $this->getCRUDObject()->getFilter()) {
            return Database::count($this->getCRUDObject()->getTableName());
        } else {
            $filterString = '';
            $table = $this->getCRUDObject()->getTableName();
            $allFields = $this->getCRUDObject()->getFields();

            foreach ($allFields as $f => $d) {
                if ($d['display'] == true) {
                    switch ($d['type']) {
                        case 'string':
                        case 'text':
                        case 'visual':
                        case 'email':
                        case 'path':
                        case 'tags':
                            if ($d['from'] or $d['from_many']) {

                            } else {
                                $filterContain = preg_replace('![^a-zа-яё0-9\040\.]!iu', '', $filter['text']);
                                $filterString = ($filterString) ? "$filterString OR " : " WHERE ";
                                $filterString .= "$f LIKE '%$filterContain%' ";
                            }
                            break;
                        case 'integer':
                        case 'decimal':
                        case 'number':
                            if ($filter['lm'] and isset($filter['lm']['operator']) and isset($filter['lm']['operand'])) {
                                if ($d['from'] or $d['from_many']) {

                                } else {
                                    switch ($filter['lm']['operator']) {
                                        case 'more_than':
                                            $filterContain = floatval($filter['lm']['operand']);
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "$f > '$filterContain' ";
                                            break;
                                        case 'less_than':
                                            $filterContain = floatval($filter['lm']['operand']);
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "$f < '$filterContain' ";
                                            break;
                                    }
                                }
                            }
                            break;
                        case 'date':
                        case 'datetime':
                            if ($filter['date']) {
                                if ($d['from'] or $d['from_many']) {

                                } else {
                                    switch ($filter['date']) {
                                        case 'all':
                                            break;
                                        case 'today':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)=DATE(NOW()) ";
                                            break;
                                        case 'yesterday':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)=DATE(NOW() - INTERVAL 1 DAY) ";
                                            break;
                                        case 'this_week':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)>=DATE(NOW() - INTERVAL 1 WEEK) ";
                                            break;
                                        case 'last_week':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)<DATE(NOW() - INTERVAL 1 WEEK) AND DATE($f)>=DATE(NOW() - INTERVAL 2 WEEK) ";
                                            break;
                                        case 'this_month':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)>=DATE(NOW() - INTERVAL 1 MONTH) ";
                                            break;
                                        case 'last_month':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)<DATE(NOW() - INTERVAL 1 MONTH) AND DATE($f)>=DATE(NOW() - INTERVAL 2 MONTH) ";
                                            break;
                                        case 'for_two_months':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)>=DATE(NOW() - INTERVAL 2 MONTH) ";
                                            break;
                                        case 'for_four_months':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)>=DATE(NOW() - INTERVAL 4 MONTH) ";
                                            break;
                                        case 'for_half_year':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)>=DATE(NOW() - INTERVAL 6 MONTH) ";
                                            break;
                                        case 'this_year':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)>=DATE(NOW() - INTERVAL 1 YEAR) ";
                                            break;
                                        case 'last_year':
                                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                                            $filterString .= "DATE($f)<DATE(NOW() - INTERVAL 1 YEAR) AND DATE($f)>=DATE(NOW() - INTERVAL 2 YEAR) ";
                                            break;
                                    }
                                }
                            }
                            break;
                    }
                }
            }

            return Database::getSingleResult("SELECT COUNT(*) FROM $table $filterString");
        }
    }

    /**
     * @param mixed $unique
     * @return array
     */
    public function readElement($unique)
    {
        $joinString = $groupByString = '';
        $diffField = $this->getCRUDObject()->getDiffField();
        $tableName = $this->getCRUDObject()->getTableName();
        $selectedFields = $tagsValues = array();
        foreach ($this->getCRUDObject()->getFields() as $fieldKey => $fieldData) {
            if ($fieldData['type'] !== 'calculated' and !$fieldData['count_of']) {
                if ($fieldData['from']) {
                    $foreignIndex = isset($fieldData['from']['group']) ? $fieldData['from']['group'] : 'b';
                    $selectedFields[] = "$foreignIndex.{$fieldData['from']['as']} AS $fieldKey";
                    $joinType = isset($fieldData['from']['join']) ? $fieldData['from']['join'] : 'LEFT';
                    $on = isset($fieldData['from']['on']) ?
                        "a.{$fieldData['from']['on']}" : str_replace('_inf', '', "a.$fieldKey");
                    $joinString .= "
						$joinType JOIN {$fieldData['from']['table']} as $foreignIndex 
						ON $foreignIndex.{$fieldData['from']['field']} = $on
					";
                } elseif ($fieldData['from_many']) {
                    /* relation table joining */
                    $foreignRelationIndex = isset($fieldData['from_many']['group_many_relation']) ?
                        $fieldData['from_many']['group_many_relation'] : 'r';
                    $foreignDataIndex = isset($fieldData['from_many']['group_many_data']) ?
                        $fieldData['from_many']['group_many_data'] : 'd';
                    $selectedFields[] = "GROUP_CONCAT($foreignDataIndex.{$fieldData['from_many']['as_many_data']} SEPARATOR ',') AS $fieldKey";
                    $relationTable = isset($fieldData['from_many']['table_many_relation'])
                        ? $fieldData['from_many']['table_many_relation'] : $fieldData['from_many']['table_many_data'] . '_relation';
                    $relationOnField = isset($fieldData['from_many']['on_many_relation']) ?
                        $fieldData['from_many']['on_many_relation'] : 'id';
                    $dataIndexField = isset($fieldData['from_many']['field_many_data']) ?
                        $fieldData['from_many']['field_many_data'] : 'id';
                    $joinString .= "
						LEFT JOIN $relationTable as $foreignRelationIndex
						ON $foreignRelationIndex.{$fieldData['from_many']['field_many_relation']} = a.$relationOnField
						LEFT JOIN {$fieldData['from_many']['table_many_data']} as $foreignDataIndex
						ON $foreignDataIndex.$dataIndexField = $foreignRelationIndex.{$fieldData['from_many']['on_many_data']}
					";

                    /*if ($fieldData['type'] == 'tags') {
                        $stEx = Database::getInstance()->query("
                            SELECT {$fieldData['from_many']['as_many_data']} AS tags_fills
                            FROM {$fieldData['from_many']['table_many_data']}
                        ");
                        $r = $stEx->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($r as $key) {
                            $tagsValues[$fieldKey . '__many'][$key] = $key;
                        }
                        $tagsValues[$fieldKey . '__many'] = json_encode($tagsValues[$fieldKey . '__many']);

                    }*/
                    /*$groupByString = (!empty($groupByString)) ? "$groupByString, " : "GROUP BY ";
                    $groupByString .= "$foreignDataIndex.{$fieldData['from_many']['field_many_data']}";*/
                } else {
                    $selectedFields[] = "a.$fieldKey";
                }
            }
        }
        $selectedFields = join(',', $selectedFields);
        $statement = Database::getInstance()->prepare("
            SELECT $selectedFields 
			FROM $tableName AS a
			$joinString
			WHERE a.$diffField=:unique
			$groupByString
			LIMIT 1
        ");

        $statement->bindParam(':unique', $unique, PDO::PARAM_STR);
        if ($this->getCRUDObject()->getSQLDebugState())
            Process::$context['flash_console'][] = 'SQL Query Debug:' . preg_replace('/\s+/s', ' ', $statement->queryString);
        $statement->execute();

        $returns = $statement->fetch(PDO::FETCH_ASSOC);
        if (count($tagsValues) > 0)
            $returns = array_merge($returns, $tagsValues);
        //var_dump($returns);
        return $returns;
    }

    /**
     * @param array $params
     */
    protected function manyToManyCreateRegistryAdd($params)
    {
        $this->manyToManyCreateRegistry[] = $params;
    }

    /**
     * @param $unique
     */
    protected function manyToManyCreateRegistryExecute($unique)
    {
        foreach ($this->manyToManyCreateRegistry as $x)
        {
            $this->manyToManyCreate($unique, $x[0], $x[1]);
        }
    }

    /**
     * @param string $value
     * @return array
     */
    protected function manyToManyValuesParse($value)
    {
        $values = explode(',', preg_replace('/\,+/', ',', $value));
        array_walk($values, array('Data', 'walkingTrim'));
        foreach ($values as &$xVal)
            if (empty($xVal)) $xVal = null;
        return $values;
    }

    /**
     * @param integer|string $unique
     * @param array $settings
     * @param string $value
     */
    protected function manyToManyCreate($unique, $settings, $value)
    {
        $valuesOriginal = $values = $this->manyToManyValuesParse($value);

        $pdo = Database::getInstance();
        $dataField = $settings['as_many_data'];
        $dataTable = $settings['table_many_data'];
        $sql = "INSERT IGNORE INTO $dataTable ($dataField) VALUES ";
        foreach ($values as &$xValue)
            $xValue = '(' . $pdo->quote(htmlspecialchars($xValue, ENT_QUOTES)) . ')';
        $sql .= join(',', $values);
        $pdo->query($sql);

        $relationTable = isset($settings['table_many_relation'])
            ? $settings['table_many_relation'] : $settings['table_many_data'] . '_relation';
        $dataIndexField = isset($settings['field_many_data']) ?
            $settings['field_many_data'] : 'id';
        $relationAField = $settings['field_many_relation'];
        $relationBField = $settings['on_many_data'];
        foreach ($valuesOriginal as &$value)
            $value = $pdo->quote(htmlspecialchars($value, ENT_QUOTES));
        $valuesOriginal = join(',', $valuesOriginal);
        $sql = "SELECT $dataIndexField FROM $dataTable WHERE $dataField IN ($valuesOriginal)";
        $insertRelationBuffer = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        $sql = "INSERT INTO $relationTable ($relationAField, $relationBField) VALUES ";
        $unique = $pdo->quote($unique);
        foreach ($insertRelationBuffer as &$dataId)
            $dataId = "($unique, $dataId)";
        $sql .= join(',', $insertRelationBuffer);
        $pdo->query($sql);
    }

    /**
     * @param integer|string $unique
     * @param array $settings
     * @param string $value
     */
    protected function manyToManyUpdate($unique, $settings, $value)
    {
        $valuesOriginal = $values = $this->manyToManyValuesParse($value);

        $pdo = Database::getInstance();
        $dataField = $settings['as_many_data'];
        $dataTable = $settings['table_many_data'];
        $sql = "INSERT IGNORE INTO $dataTable ($dataField) VALUES ";
        foreach ($values as &$xValue)
            $xValue = '(' . $pdo->quote(htmlspecialchars($xValue, ENT_QUOTES)) . ')';
        $sql .= join(',', $values);
        $pdo->query($sql);

        $relationTable = isset($settings['table_many_relation'])
            ? $settings['table_many_relation'] : $settings['table_many_data'] . '_relation';
        $dataIndexField = isset($settings['field_many_data']) ?
            $settings['field_many_data'] : 'id';
        $relationAField = $settings['field_many_relation'];
        $relationBField = $settings['on_many_data'];
        $unique = $pdo->quote($unique);

        $pdo->query("DELETE FROM $relationTable WHERE $relationAField=$unique");

        foreach ($valuesOriginal as &$value)
            $value = $pdo->quote(htmlspecialchars($value, ENT_QUOTES));
        $valuesOriginal = join(',', $valuesOriginal);
        $sql = "SELECT $dataIndexField FROM $dataTable WHERE $dataField IN ($valuesOriginal)";
        $insertRelationBuffer = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        $sql = "INSERT INTO $relationTable ($relationAField, $relationBField) VALUES ";

        foreach ($insertRelationBuffer as &$dataId)
            $dataId = "($unique, $dataId)";
        $sql .= join(',', $insertRelationBuffer);
        $pdo->query($sql);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getListing($offset = 0, $limit = 30)
    {
        $displayable = $this->getCRUDObject()->getDisplayable();
        $tmpFields = array_unique(array_merge(array($this->getCRUDObject()->getDiffField()), $displayable));
        $allFields = $this->getCRUDObject()->getFields();
        $foreignIndex = 'b';
        $orderFilter = false;
        $foreignIndexes = array();
        $joinString = $filterString = $groupByString = '';
        $tableName = $this->getCRUDObject()->getTableName();
        $orderByField = $this->getCRUDObject()->getOrderByField();
        //$diffField = $this->getCRUDObject()->getDiffField();

        if ($filter = $this->getCRUDObject()->getFilter()) {
            foreach ($allFields as $f => $d) {
                $allFields[$f]['use_in_text_filter'] = false;
                $allFields[$f]['use_in_text_filter_having'] = false;
                $allFields[$f]['use_in_date_filter'] = false;
                $allFields[$f]['use_in_lm_filter'] = false;
                if ($d['display'] == true) {
                    switch ($d['type']) {
                        case 'date':
                        case 'datetime':
                            if ($d['from'] or $d['from_many']) {

                            } else {
                                $allFields[$f]['use_in_date_filter'] = ($filter['date']);
                            }
                            break;
                        case 'string':
                        case 'text':
                        case 'visual':
                        case 'email':
                        case 'path':
                        case 'tags':
                            if ($d['from'] or $d['from_many']) {
                                $allFields[$f]['use_in_text_filter_having'] = ($filter['text']);
                            } else {
                                $allFields[$f]['use_in_text_filter'] = ($filter['text']);
                            }
                            break;
                        case 'integer':
                        case 'decimal':
                        case 'number':
                            if ($d['from'] or $d['from_many']) {

                            } else {
                                $allFields[$f]['use_in_lm_filter'] = ($filter['lm']);
                            }
                            break;
                    }
                }
            }
        }

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
                            $ff = ($d['coalesce'])
                                ? "COALESCE($foreignIndex.{$d['from']['as']}, null, '{$d['coalesce']}') AS $f"
                                : "$foreignIndex.{$d['from']['as']} AS $f";
                            $joinType = isset($d['from']['join']) ? $d['from']['join'] : 'LEFT';
                            $on = isset($d['from']['on']) ?
                                "a.{$d['from']['on']}" : str_replace('_inf', '', "a.$f");
                            $joinString .= "
                            $joinType JOIN {$d['from']['table']} as $foreignIndex
                            ON $foreignIndex.{$d['from']['field']} = $on
                            ";
                        }
                    }
                } elseif ($d['from_many']) {
                    foreach ($tmpFields as &$ff) {
                        if ($ff == $f) {
                            $foreignRelationIndex = isset($d['from_many']['group_many_relation']) ?
                                $d['from_many']['group_many_relation'] : 'r';
                            $foreignIndexes[] = $foreignRelationIndex;
                            $foreignDataIndex = isset($d['from_many']['group_many_data']) ?
                                $d['from_many']['group_many_data'] : 'd';
                            $foreignIndexes[] = $foreignDataIndex;
                            $ff = "GROUP_CONCAT($foreignDataIndex.{$d['from_many']['as_many_data']} SEPARATOR ',') AS $f";
                            $relationTable = isset($d['from_many']['table_many_relation'])
                                ? $d['from_many']['table_many_relation'] : $d['from_many']['table_many_data'] . '_relation';
                            $relationOnField = isset($d['from_many']['on_many_relation']) ?
                                $d['from_many']['on_many_relation'] : 'id';
                            $dataIndexField = isset($d['from_many']['field_many_data']) ?
                                $d['from_many']['field_many_data'] : 'id';
                            $joinString .= "
                                LEFT JOIN $relationTable as $foreignRelationIndex
                                ON $foreignRelationIndex.{$d['from_many']['field_many_relation']} = a.$relationOnField
                                LEFT JOIN {$d['from_many']['table_many_data']} as $foreignDataIndex
                                ON $foreignDataIndex.$dataIndexField = $foreignRelationIndex.{$d['from_many']['on_many_data']}
                            ";
                            /*$groupByString = (!empty($groupByString)) ? "$groupByString, " : "GROUP BY ";
                            $groupByString .= "$foreignDataIndex.{$d['from_many']['field_many_data']}";*/
                        }
                    }
                }
                elseif ($d['type'] !== 'calculated' and !$d['count_of'])
                {
                    if ($d['coalesce'])
                    {
                        foreach ($tmpFields as &$ff)
                        {
                            if ($ff == $f) {
                                $ff = "COALESCE(a.$f, null, '{$d['coalesce']}') AS $f";
                            }
                        }
                    }
                    $groupByString = (!empty($groupByString)) ? "$groupByString, " : "GROUP BY ";
                    $groupByString .= "a.$f";
                }
                if ($d['count_of']) {
                    $groupByString = (!empty($groupByString)) ? "$groupByString, " : "GROUP BY ";
                    $groupByString .= $d['count_of'];

                    foreach ($tmpFields as &$ff) {
                        if ($ff == $f) {
                            $ff = "COUNT(a.{$d['count_of']}) AS $f";
                            $orderFilter = $f;
                        }
                    }
                }

                if ($filter and $d['use_in_text_filter']) {
                    $filterContain = preg_replace('![^a-zа-яё0-9\040\.]!iu', '', $filter['text']);
                    $filterString = ($filterString) ? "$filterString OR " : " WHERE ";
                    $index = (!$d['from']) ? 'a.' : '';
                    $filterString .= "{$index}{$f} LIKE '%$filterContain%' ";
                } elseif ($filter and $d['use_in_text_filter_having']) {

                } elseif ($filter and $d['use_in_lm_filter'] and isset($filter['lm']['operator']) and isset($filter['lm']['operand'])) {
                    switch ($filter['lm']['operator']) {
                        case 'more_than':
                            $filterContain = floatval($filter['lm']['operand']);
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "{$index}{$f} > '$filterContain' ";
                            break;
                        case 'less_than':
                            $filterContain = floatval($filter['lm']['operand']);
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "{$index}{$f} < '$filterContain' ";
                            break;
                    }
                } elseif ($filter and $d['use_in_date_filter']) {
                    switch ($filter['date']) {
                        case 'all':
                            break;
                        case 'today':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})=DATE(NOW()) ";
                            break;
                        case 'yesterday':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})=DATE(NOW() - INTERVAL 1 DAY) ";
                            break;
                        case 'this_week':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})>=DATE(NOW() - INTERVAL 1 WEEK) ";
                            break;
                        case 'last_week':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})<DATE(NOW() - INTERVAL 1 WEEK) AND DATE({$index}{$f})>=DATE(NOW() - INTERVAL 2 WEEK) ";
                            break;
                        case 'this_month':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})>=DATE(NOW() - INTERVAL 1 MONTH) ";
                            break;
                        case 'last_month':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})<DATE(NOW() - INTERVAL 1 MONTH) AND DATE({$index}{$f})>=DATE(NOW() - INTERVAL 2 MONTH) ";
                            break;
                        case 'for_two_months':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})>=DATE(NOW() - INTERVAL 2 MONTH) ";
                            break;
                        case 'for_four_months':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})>=DATE(NOW() - INTERVAL 4 MONTH) ";
                            break;
                        case 'for_half_year':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})>=DATE(NOW() - INTERVAL 6 MONTH) ";
                            break;
                        case 'this_year':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})>=DATE(NOW() - INTERVAL 1 YEAR) ";
                            break;
                        case 'last_year':
                            $filterString = ($filterString) ? "$filterString AND " : " WHERE ";
                            $index = (!$d['from']) ? 'a.' : '';
                            $filterString .= "DATE({$index}{$f})<DATE(NOW() - INTERVAL 1 YEAR) AND DATE({$index}{$f})>=DATE(NOW() - INTERVAL 2 YEAR) ";
                            break;
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
            $field = str_replace(
                array("a.COALESCE", "a.GROUP_CONCAT", "a.COUNT"),
                array("COALESCE", "GROUP_CONCAT", "COUNT"),
                "a.$field"
            );
            foreach ($foreignIndexes as $index) {
                $field = str_replace("a.$index.", "$index.", $field);
            }
        }

        $requiredFields = join(',', $tmpFields);
        $orderBy = $orderByField ? "ORDER BY a.$orderByField DESC" : '';
        $orderBy = $orderFilter ? str_replace("a.$orderByField", $orderByField, $orderBy) : $orderBy;
        //$groupByString = (!empty($groupByString)) ? "$groupByString, " : "GROUP BY ";
        //$groupByString .= "a.$diffField";
        $statement = Database::getInstance()->prepare("
            SELECT $requiredFields
            FROM $tableName AS a 
			$joinString
			$filterString
            $groupByString
            $orderBy
            LIMIT :limit OFFSET :offset
        ");

        if ($this->getCRUDObject()->getSQLDebugState())
            Process::$context['flash_console'][] =
                'SQL Query Debug:' . preg_replace('/\s+/s', ' ', $statement->queryString);

        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($allFields as $f => $d) {
            if ($d['type'] == 'calculated' and $d['display']) {
                foreach ($result as &$key) {
                    if ($calc = $d['function']) {
                        $key[$f] = $this->getCRUDObject()->$calc($key);
                    } else {
                        Process::$context['flash_warning'] =
                            "Не удалось вычислить $f - PARAM_DISPLAY_FUNCTION не указан";
                    }
                }
            }
            if ($d['from_many'] !== false and $d['display']) {
                foreach ($result as &$key) {
                    $key[$f] = preg_replace('/(.+?)\,(.+?)/', '$1, $2', $key[$f]);
                }
            }
        }

        return $result;
    }

    /**
     * @param array $postedData
     * @return bool
     */
    public function create($postedData)
    {
        $table = $this->getCRUDObject()->getTableName();
        $fields = $this->getCRUDObject()->getFields();

        $insertedFields = $insertedFieldsStatic = $insertedValues = array();

        foreach ($postedData as $key => $value) {
            if ($value === false and $fields[$key]['default']) {
                if (strtolower($fields[$key]['default']) !== 'null') {
                    if (strtolower($fields[$key]['default']) == 'now') {
                        switch (strtolower($fields[$key]['type'])) {
                            case 'date':
                                $insertedFieldsStatic[$key] = "CURRENT_DATE";
                                break;
                            case 'datetime':
                                $insertedFieldsStatic[$key] = "NOW()";
                                break;
                        }
                    } else {
                        switch (strtolower($fields[$key]['type'])) {
                            case 'integer':
                            case 'number':
                            case 'select':
                            case 'string':
                            case 'text':
                            case 'visual':
                            case 'email':
                            case 'path':
                                $insertedFieldsStatic[$key] = "'{$fields[$key]['default']}'";
                                break;
                        }
                    }
                }
            } elseif ($value) {
                if ($fields[$key]['from_many']) {
                    $this->manyToManyCreateRegistryAdd(
                        array($fields[$key]['from_many'], $value)
                    );
                } else {
                    $fType = strtolower($fields[$key]['type']);

                    if ($fType == 'password') {
                        /*\***** !DO PASSWORD! *****\*/
                        $value = Security::getDigest($value);
                    } elseif ($fType == 'decimal') {
                        $value = str_replace(',', '.', $value);
                    }

                    if (($fType == 'date' or $fType == 'datetime'
                            or $fType == 'integer' or $fType == 'number') and empty($value)) {
                        $insertedFields[] = $key;
                        $insertedValues[":$key"] = array(null, PDO::PARAM_NULL);
                    } else {
                        $insertedFields[] = $key;
                        $insertedValues[":$key"] = ($fType == 'integer'
                            or $fType == 'number' or $fType == 'decimal') ?
                            array($value, PDO::PARAM_INT) : array($value, PDO::PARAM_STR);
                    }
                }
            }
        }

        $insertedFieldsResult = join(',', $insertedFields);
        if (count($insertedFieldsStatic) > 0) $insertedFieldsResult .= ',' . join(',', array_keys($insertedFieldsStatic));
        $insertedValuesResult = join(',', array_keys($insertedValues));
        if (count($insertedFieldsStatic) > 0) $insertedValuesResult .= ',' . join(',', array_values($insertedFieldsStatic));
        $statement = Database::getInstance()->prepare("INSERT INTO $table ($insertedFieldsResult) VALUES ($insertedValuesResult)");
        foreach ($insertedValues as $valueKey => $valueData)
            $statement->bindParam($valueKey, $valueData[0], $valueData[1]);
        $result = $statement->execute();
        if ($result) {
            $this->manyToManyCreateRegistryExecute(
                Database::getInstance()->lastInsertId()
            );
        }
        if ($this->getCRUDObject()->getSQLDebugState())
            Process::$context['flash_console'][] =
                'SQL Query Debug:' . preg_replace('/\s+/s', ' ', $statement->queryString);
        return $result;
    }

    /**
     * @param string $unique
     * @param array $postedData
     * @return bool
     */
    public function update($unique, $postedData)
    {
        $table = $this->getCRUDObject()->getTableName();
        $fields = $this->getCRUDObject()->getFields();
        $diffField = $this->getCRUDObject()->getDiffField();

        $updatedFields = $updatedValues = $updatedResult = array();

        foreach ($postedData as $key => $value) {
            if ($value !== false) {
                if ($fields[$key]['from_many']) {
                    $this->manyToManyUpdate(
                        $unique, $fields[$key]['from_many'], $value
                    );
                } else {
                    $fType = strtolower($fields[$key]['type']);

                    if ($fType == 'password') {
                        /*\***** !DO PASSWORD! *****\*/
                        $value = Security::getDigest($value);
                    }

                    if (($fType == 'date' or $fType == 'datetime'
                            or $fType == 'integer' or $fType == 'number') and empty($value)) {
                        $updatedFields[] = $key;
                        $updatedValues[":$key"] = array(null, PDO::PARAM_NULL);
                    } else {
                        $updatedFields[] = $key;
                        $updatedValues[":$key"] = ($fType == 'integer' or $fType == 'number') ?
                            array($value, PDO::PARAM_INT) : array($value, PDO::PARAM_STR);
                    }
                }
            }
        }

        for ($i = 0, $size = count($updatedFields); $i < $size; ++$i) {
            $updatedResult[] = "{$updatedFields[$i]} =:{$updatedFields[$i]}";
        }

        $updatedResult = (count($updatedResult) > 1) ? join(',', $updatedResult) : $updatedResult[0];
        $statement = Database::getInstance()->prepare("UPDATE $table SET $updatedResult WHERE $diffField=:unique");
        $statement->bindParam(':unique', $unique,
            in_array($fields[$diffField]['type'], array('integer', 'number')) ? PDO::PARAM_INT : PDO::PARAM_STR);
        foreach ($updatedValues as $valueKey => $valueData)
            $statement->bindParam($valueKey, $valueData[0], $valueData[1]);
        if ($this->getCRUDObject()->getSQLDebugState())
            Process::$context['flash_console'][] =
                'SQL Query Debug:' . preg_replace('/\s+/s', ' ', $statement->queryString);
        return $statement->execute();
    }

    /**
     * @param mixed $unique
     * @return bool
     */
    public function delete($unique)
    {
        $table = $this->getCRUDObject()->getTableName();
        $diffField = $this->getCRUDObject()->getDiffField();

        if (is_array($unique)) {
            return Database::getInstance()
                ->prepare("DELETE FROM $table WHERE $diffField IN (?)")
                ->execute($unique);
        } else {
            return Database::getInstance()
                ->prepare("DELETE FROM $table WHERE $diffField=?")
                ->execute(array($unique));
        }
    }

    /**
     * @return bool
     */
    public function truncate()
    {
        $table = $this->getCRUDObject()->getTableName();
        return Database::getInstance()->query("TRUNCATE $table");
    }
}