<?php
class CRUDMySQLDriver extends CRUDDriverInterface
{
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
                            $filterContain = preg_replace('![^a-zа-яё0-9\040]!iu', '', $filter['text']);
                            $filterString = ($filterString) ? "$filterString OR " : " WHERE ";
                            $filterString .= "$f LIKE '%$filterContain%' ";
                            break;
                        case 'integer':
                        case 'decimal':
                        case 'number':
                            if ($filter['lm'] and isset($filter['lm']['operator']) and isset($filter['lm']['operand'])) {
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
                            break;
                        case 'date':
                        case 'datetime':
                            if ($filter['date']) {
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
        $joinString = '';
        $diffField = $this->getCRUDObject()->getDiffField();
        $tableName = $this->getCRUDObject()->getTableName();
        $selectedFields = array();
        foreach ($this->getCRUDObject()->getFields() as $fieldKey => $fieldData) {
            if ($fieldData['type'] !== 'calculated') {
                if ($fieldData['from']) {
                    $foreignIndex = isset($fieldData['from']['group']) ? $fieldData['from']['group'] : 'b';
                    $selectedFields[] = "$foreignIndex.{$fieldData['from']['as']} AS $fieldKey";
                    $joinType = isset($fieldData['from']['join']) ? $fieldData['from']['join'] : 'INNER';
                    $on = isset($fieldData['from']['on']) ? "a.{$fieldData['from']['on']}" : "a.$fieldKey";
                    $joinString .= "
						$joinType JOIN {$fieldData['from']['table']} as $foreignIndex 
						ON $foreignIndex.{$fieldData['from']['field']} = $on
					";
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
			LIMIT 1
        ");

        $statement->bindParam(':unique', $unique, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
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
        $joinString = $filterString = $groupBy = '';
        $tableName = $this->getCRUDObject()->getTableName();
        $orderByField = $this->getCRUDObject()->getOrderByField();

        if ($filter = $this->getCRUDObject()->getFilter()) {
            foreach ($allFields as $f => $d) {
                $allFields[$f]['use_in_text_filter'] = false;
                $allFields[$f]['use_in_date_filter'] = false;
                $allFields[$f]['use_in_lm_filter'] = false;
                if ($d['display'] == true) {
                    switch ($d['type']) {
                        case 'date':
                        case 'datetime':
                            $allFields[$f]['use_in_date_filter'] = ($filter['date']);
                            break;
                        case 'string':
                        case 'text':
                        case 'visual':
                            $allFields[$f]['use_in_text_filter'] = ($filter['text']);
                            break;
                        case 'integer':
                        case 'decimal':
                        case 'number':
                            $allFields[$f]['use_in_lm_filter'] = ($filter['lm']);
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

                if ($filter and $d['use_in_text_filter']) {
                    $filterContain = preg_replace('![^a-zа-яё0-9\040]!iu', '', $filter['text']);
                    $filterString = ($filterString) ? "$filterString OR " : " WHERE ";
                    $index = (!$d['from']) ? 'a.' : '';
                    $filterString .= "{$index}{$f} LIKE '%$filterContain%' ";
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
            $field = str_replace("a.COUNT", "COUNT", "a.$field");
            foreach ($foreignIndexes as $index) {
                $field = str_replace("a.$index.", "$index.", $field);
            }
        }

        $requiredFields = join(',', $tmpFields);
        $orderBy = $orderByField ? "ORDER BY a.$orderByField DESC" : '';
        $orderBy = $orderFilter ? str_replace("a.$orderByField", $orderByField, $orderBy) : $orderBy;
        $statement = Database::getInstance()->prepare("
            SELECT $requiredFields
            FROM $tableName AS a 
			$joinString
			$filterString
            $groupBy $orderBy LIMIT :limit OFFSET :offset
        ");

        //Process::$context['flash_error'] = $statement->queryString;
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
                    $key[$f] = $this->getCRUDObject()->$calc($key);
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
                                $insertedFieldsStatic[$key] = "'{$fields[$key]['default']}'";
                                break;
                        }
                    }
                }
            } elseif ($value) {
                $fType = strtolower($fields[$key]['type']);

                if ($fType == 'password') {
                    /*\***** !DO PASSWORD! *****\*/
                    $value = Security::getDigest($value);
                } elseif ($fType == 'decimal') {
                    $value = str_replace(',', '.', $value);
                }

                $insertedFields[] = $key;
                $insertedValues[":$key"] = ($fType == 'integer' or $fType == 'number' or $fType == 'decimal') ?
                    array($value, PDO::PARAM_INT) : array($value, PDO::PARAM_STR);
            }
        }

        $insertedFieldsResult = join(',', $insertedFields);
        if (count($insertedFieldsStatic) > 0) $insertedFieldsResult .= ',' . join(',', array_keys($insertedFieldsStatic));
        $insertedValuesResult = join(',', array_keys($insertedValues));
        if (count($insertedFieldsStatic) > 0) $insertedValuesResult .= ',' . join(',', array_values($insertedFieldsStatic));
        $statement = Database::getInstance()->prepare("INSERT INTO $table ($insertedFieldsResult) VALUES ($insertedValuesResult)");
        foreach ($insertedValues as $valueKey => $valueData)
            $statement->bindParam($valueKey, $valueData[0], $valueData[1]);
        return $statement->execute();
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
                $fType = strtolower($fields[$key]['type']);

                if ($fType == 'password') {
                    /*\***** !DO PASSWORD! *****\*/
                    $value = Security::getDigest($value);
                }

                if (($fType == 'date' or $fType == 'datetime') and empty($value)) {
                    $updatedFields[] = $key;
                    $updatedValues[":$key"] = array(null, PDO::PARAM_NULL);
                } else {
                    $updatedFields[] = $key;
                    $updatedValues[":$key"] = ($fType == 'integer' or $fType == 'number') ?
                        array($value, PDO::PARAM_INT) : array($value, PDO::PARAM_STR);
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
}