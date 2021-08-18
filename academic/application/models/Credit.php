<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Credit extends Zend_Db_Table_Abstract
{
    public $_name = 'master_credit';
    protected $_id = 'credit_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)                      				   
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	public function getDropDownList()
	{
        $select = $this->_db->select()
		    ->from($this->_name,array("credit","credit_id"))
			->where("$this->_name.status!=?",2)
			->order('credit_id  ASC');
		
		
        $result = $this->getAdapter()->fetchAll($select);
		
      $data = array();
        foreach($result as $k=>$vals) {
			
			$data[$vals['credit_id']] = $vals['credit'];
			
        }
		
        return $data; 
    }	
	
}
?>