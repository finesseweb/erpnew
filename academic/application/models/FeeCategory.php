<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_FeeCategory extends Zend_Db_Table_Abstract
{
    public $_name = 'erp_fee_category_master';
    protected $_id = 'category_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_id=?", $id)				   
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
					  ->order("$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select); 
        return $result;
    }
	
			
   public function getDropDownList()
	{
        $select = $this->_db->select()
		    ->from($this->_name,array('category_id', 'category_name'))
			->where("$this->_name.status!=?",2)
			->order('category_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
		//echo'<pre>';print_r($result);die;
      $data = array();
        foreach($result as $k=>$vals) {
			
			$data[$vals['category_id']] = $vals['category_name'];
			
        }
		
        return $data; 
    }
	
	
	public function getCategory()
	{
        $select = $this->_db->select()
		    ->from($this->_name,array('category_name','category_id'))
			//->joinleft(array("feeheads"=>"erp_fee_heads_master"),"$this->_name.category_id=feeheads.feecategory_id",array("feecategory_id"))
			//->joinleft(array("feehead_items"=>"erp_fee_heads_items"),"feehead_items.feehead_id=feeheads.feehead_id",array("feehead_name"))
			->where("$this->_name.status!=?",2)
			->order('category_id  ASC');
		//echo $select;die;
        $result = $this->getAdapter()->fetchAll($select);
		//echo'<pre>';print_r($result);die;
        return $result; 
    }
	

	public function getCategoryIds()
	{
        $select = $this->_db->select()
		    ->from($this->_name,'category_id')
			//->joinleft(array("feeheads"=>"erp_fee_heads_master"),"$this->_name.category_id=feeheads.feecategory_id",array("feecategory_id"))
			//->joinleft(array("feehead_items"=>"erp_fee_heads_items"),"feehead_items.feehead_id=feeheads.feehead_id",array("feehead_name"))
			->where("$this->_name.status!=?",2)
			->order('category_id  ASC');
		//echo $select;die;
        $result = $this->getAdapter()->fetchAll($select);
		//echo'<pre>';print_r($result);die;
        return $result; 
    }
}
?>