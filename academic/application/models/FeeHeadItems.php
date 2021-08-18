<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_FeeHeadItems extends Zend_Db_Table_Abstract
{
    public $_name = 'erp_fee_heads_items';
    protected $_id = 'items_id';
  
    //get details by record for edit
	public function getRecord($id)  //company_id is main Company Id
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_id=?",$id);
        $result=$this->getAdapter()
                      ->fetchRow($select);  
//print_r($result); die;					  
        return $result;
    }
	
	
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name);
        $result=$this->getAdapter()
                      ->fetchAll($select);   
		//print_r($result);die;	  
        return $result;
    }
	
	//View purpose
	
	
	public function trashItems($feehead_id) {

        $this->_db->delete($this->_name, "feehead_id=$feehead_id");

    }
	
	
	public function getItemRecords($feehead_id)  //company_id is main Company Id
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.feehead_id=?",$feehead_id);
        $result=$this->getAdapter()
                      ->fetchAll($select);  
//print_r($result); die;					  
        return $result;
    }
}
?>