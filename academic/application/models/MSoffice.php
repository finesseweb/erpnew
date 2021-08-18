<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_MSoffice extends Zend_Db_Table_Abstract
{
    public $_name = 'master_msoffice';
    protected $_id = 'msoffice_id';
  
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
					  ->joinleft(array("academic"=>"master_academic"),"academic.academic_id=$this->_name.academic_id",array("from_date","to_date"))
					 ->joinleft(array("msoffice_items"=>"master_msoffice_item"),"msoffice_items.program_id=$this->_name.msoffice_id",array("GROUP_CONCAT(msoffice_items.program_name) as program"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_id DESC")
					  ->group("$this->_id");
					//  ->group("$this->_id");
        $result=$this->getAdapter()
                      ->fetchAll($select); 
					//print_r($result) ;die; 
        return $result;
    }
	
}
?>