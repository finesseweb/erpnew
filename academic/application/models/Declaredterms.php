<?php

/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class Application_Model_Declaredterms extends Zend_Db_Table_Abstract {

    public $_name = 'declared_terms';
    protected $_id = 'id';

    //get details by record for edit
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id = ?", $id)
                ->where("$this->_name.status != ?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    //Get all records
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status != ?", 2)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getDropDownList() {
       
        $select = $this->_db->select()
                ->from($this->_name, array('term_des', 'term_name'))
                ->where("$this->_name.status!=?", 2)
                ->order('term_des  ASC');
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        $st_year = '';
        $end_year = '';
       // echo "<pre>"; print_r();exit;
        foreach ($result as $val) {
            $data[$val['term_des']] = $val['term_name'];
        }
        return $data;
    }

}

?>