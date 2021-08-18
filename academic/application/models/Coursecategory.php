<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Coursecategory extends Zend_Db_Table_Abstract
{
    public $_name = 'course_category_master';
    protected $_id = 'cc_id';
  
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
		public function getCourseCategory($course_name) {

        $select = $this->_db->select()
                ->from($this->_name,array("cc_name","cc_id"))	
				->where("$this->_name.cc_name =?", $course_name)
				->where("$this->_name.status!=?", 2);
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		

    }
	
	public function getDropDownList(){
        $select = $this->_db->select()
     ->from($this->_name, array('cc_id','cc_name'))				
				->where("$this->_name.status!=?",2)
                ->order('cc_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['cc_id']] = $val['cc_name'];
			
        }
        return $data;
    }
	
}
?>