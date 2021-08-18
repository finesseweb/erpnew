<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Coursetype extends Zend_Db_Table_Abstract
{
    public $_name = 'course_type_master';
    protected $_id = 'ct_id';
  
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
		    ->from($this->_name,array('ct_id','ct_name'))
			->where("$this->_name.status!=?",2)
			->order('ct_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
		//echo'<pre>';print_r($result);die;
      $data = array();
        foreach($result as $k=>$vals) {
			
			$data[$vals['ct_id']] = $vals['ct_name'];
			
        }
		
        return $data; 
    }
	
	
	public function getcoursetype($coursetype) {

        $select = $this->_db->select()
                ->from($this->_name,array("ct_name","ct_id"))	
				->where("$this->_name.ct_name =?", $coursetype)
				->where("$this->_name.status!=?", 2);
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		

    }
	
}
?>