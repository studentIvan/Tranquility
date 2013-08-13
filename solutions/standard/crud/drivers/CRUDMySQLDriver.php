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
        return Database::count($this->getCRUDObject()->getTableName());
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
		foreach ($this->getCRUDObject()->getFields() as $fieldKey => $fieldData) 
		{
			if ($fieldData['type'] !== 'calculated') 
			{
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
        $tmpFields = array_unique(
            array_merge(
				array($this->getCRUDObject()->getDiffField()), 
				$this->getCRUDObject()->getDisplayable()
			)
        );
        $allFields = $this->getCRUDObject()->getFields();
        $foreignIndex = 'b';
        $orderFilter = false;
        $foreignIndexes = array();
        $joinString = $groupBy = '';
		$tableName = $this->getCRUDObject()->getTableName();
		$orderByField = $this->getCRUDObject()->getOrderByField();
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
        $orderBy = $orderByField ? "ORDER BY a.$orderByField DESC" : '';
        $orderBy = $orderFilter ? str_replace("a.$orderByField", $orderByField, $orderBy) : $orderBy;
        $statement = Database::getInstance()->prepare("
            SELECT $requiredFields
            FROM $tableName AS a $joinString
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
		
		foreach ($postedData as $key => $value) 
		{
			if ($value === false and $fields[$key]['default']) 
			{
				if (strtolower($fields[$key]['default']) !== 'null') 
				{
					if (strtolower($fields[$key]['default']) == 'now') 
					{
						switch (strtolower($fields[$key]['type'])) {
							case 'date':
								$insertedFieldsStatic[$key] = "CURRENT_DATE";
							break;
							case 'datetime':
								$insertedFieldsStatic[$key] = "NOW()";
							break;
						}
					}
					else 
					{
						switch (strtolower($fields[$key]['type'])) {
							case 'integer':
							case 'number':
							case 'select':
							case 'string':
							case 'text':
								$insertedFieldsStatic[$key] = "'{$fields[$key]['default']}'";
							break;
						}
					}
				}
			}
			elseif ($value)
			{
				$fType = strtolower($fields[$key]['type']);
				
				if ($fType == 'password') {
					/*\***** !DO PASSWORD! *****\*/
					$value = Security::getDigest($value);
				}
				
				$insertedFields[] = $key;
				$insertedValues[":$key"] = ($fType == 'integer' or $fType == 'number') ? 
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
		
		foreach ($postedData as $key => $value) 
		{
			if ($value !== false) 
			{
				$fType = strtolower($fields[$key]['type']);
				
				if ($fType == 'password') {
					/*\***** !DO PASSWORD! *****\*/
					$value = Security::getDigest($value);
				}
				
				if (($fType == 'date' or $fType == 'datetime') and empty($value)) 
				{
					$updatedFields[] = $key;
					$updatedValues[":$key"] = array(null, PDO::PARAM_NULL);
				} 
				else 
				{
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