<?php
abstract class CRUDDriverInterface
{
	protected $CRUDObject = null;
	
	public function __construct($CRUDObject) {
		$this->CRUDObject = $CRUDObject;
	}
	
	/**
	 * @return CRUDObjectInterface
	 */
	protected function getCRUDObject() {
		return $this->CRUDObject;
	}
	
	/**
	 * @param string $table
	 * @param string $valueField
	 * @param string $descriptionField
     * @return array
     */
	public function getSelectOptions($table, $valueField, $descriptionField) {}
	
	/**
     * @return int
     */
	public function getCount() {}
	
	/**
     * @param mixed $unique
     * @return array
     */
    public function readElement($unique) {}
	
	/**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getListing($offset = 0, $limit = 30) {}
}