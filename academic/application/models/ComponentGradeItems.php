<?php
/**
 * Application_Model_ErpItemsMaster
 *
 * @Framework Zend Framework
 * @Powered By TIS
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2014 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 */
class Application_Model_ComponentGradeItems extends Zend_Db_Table_Abstract {

    protected $_name = 'component_grade_items';
    protected $_id = 'component_grade_item_id';
    /**
     * Set Primary Key Id as a Parameter 
     *
     * @param string $item_id
     * @return single dimention array
     */	
   public function getRecords($component_grade_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.component_grade_id=?", $component_grade_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    /**
     * Retrieve all Records
     *
     * @return Array
     */
 public function trashItems($component_grade_id='') {

        $this->_db->delete($this->_name, "component_grade_id = $component_grade_id");

    }

  
     
    
}