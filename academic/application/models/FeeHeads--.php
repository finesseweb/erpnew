<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_FeeHeads extends Zend_Db_Table_Abstract
{
    public $_name = 'erp_fee_heads_master';
    protected $_id = 'feehead_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
						->joinleft(array("cat"=>"erp_fee_category_master"),"cat.category_id=$this->_name.feecategory_id",array("category_name"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	//View purpose
	
	public function getFeeheads()
	{
        $select = $this->_db->select()
		    ->from($this->_name,array("feecategory_id","feehead_id"))
			->joinleft(array("cat"=>"erp_fee_category_master"),"cat.category_id=$this->_name.feecategory_id",array("category_name","category_id"))
			->joinleft(array("feehead_items"=>"erp_fee_heads_items"),"feehead_items.feehead_id=$this->_name.feehead_id",array("feehead_name"))
			->where("$this->_name.status!=?",2)
			->order('feecategory_id  ASC');
	 //  echo $select;die;
        $result = $this->getAdapter()->fetchAll($select);
		//echo'<pre>';print_r($result);die;
        return $result; 
    }

}
?>