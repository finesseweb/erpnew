<?php

/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class Application_Model_GlobalSettings extends Zend_Db_Table_Abstract {

    public $_name = 'global_settings';
    protected $_id = 'global_setting_id';
    public $gs_category = array(
        1 => 'Email Template',
        2 => 'SMS Template',
        3 => 'Student Grade'
    );

    //get details by record for edit
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.gs_status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.gs_status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
     public function getDetailBySystemName($system_name) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.gs_system_name=?", $system_name)
                ->where("$this->_name.gs_status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

}

?>