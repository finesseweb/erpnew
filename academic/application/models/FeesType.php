<?php
/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_FeesType extends Zend_Db_Table_Abstract
{
    public $_name = 'master_fees_type';
    protected $_id = 'fee_type_id';
  
    //get details by record for edit
	public function getRecord($fee_type_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_id=?",$fee_type_id);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    //get details for show
    public function getRecords()
    {
        $select=$this->_db->select()
					->from($this->_name) 										
					->where("$this->_name.status!=2")
					->order('fee_type_id DESC');
        $result=$this->getAdapter()
                      ->fetchAll($select);
        return $result;
    }
	public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('fee_type_id', 'fee_type_title'))				
				->where("$this->_name.status!=2")
                ->order('fee_type_id ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['fee_type_id']] = $val['fee_type_title'];
        }
        return $data;
    }
	//discount
	public function getDiscountDropDown() {
        $select = $this->_db->select()
                ->from($this->_name, array('fee_type_id', 'fee_type_title'))				
				//->where("$this->_name.discount_type =?" ,1)
				->where("$this->_name.status!=2")				
                ->order('fee_type_id ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['fee_type_id']] = $val['fee_type_title'];
        }
        return $data;
    }
	//waiver
	public function getWaiverDropDown() {
        $select = $this->_db->select()
                ->from($this->_name, array('fee_type_id', 'fee_type_title'))
				//->where("$this->_name.discount_type =?" ,2)	
				->where("$this->_name.status!=2")
                ->order('fee_type_id ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['fee_type_id']] = $val['fee_type_title'];
        }
        return $data;
    }	
}