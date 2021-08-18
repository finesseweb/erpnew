<?php
/**
 * Application_Model_ErpInventoryGrnItems
 *
 * @Framework Zend Framework
 * @Powered By TIS
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2014 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 */
class Application_Model_ElectiveSelectionItems extends Zend_Db_Table_Abstract {

    protected $_name = 'erp_elective_selection_items';
    protected $_id = 'items_id';

    /**
     * Set Primary Key Id as a Parameter 
     *
     * @param string $id
     * @return single dimention array
     */
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_id=?", $id);
				//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
				
        return $result;
    }

    /**
     * Retrieve all Records
     *
     * @return Array
     */
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name);
				//->where("$this->_name.items_status !=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

	
   public function trashItems($elective_id='') {

        $this->_db->delete($this->_name, "elective_id = $elective_id");

    }
	
	public function getItemsRecords($elective_id) {
        $select = $this->_db->select()
                ->from($this->_name)
				 ->where("$this->_name.elective_id=?", $elective_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
	
        return $result;
    }
	
	
}