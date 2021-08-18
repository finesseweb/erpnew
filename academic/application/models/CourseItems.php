<?php
/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_CourseItems extends Zend_Db_Table_Abstract
{
    public $_name = 'master_course_item';
    protected $_id = 'item_id';
  
    //get details by record for edit
	public function getRecord($course_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array("group_concat($this->_name.category) as category","group_concat($this->_name.subject_code) as subject_code","group_concat($this->_name.subject) as subject","group_concat($this->_name.credit) as credit","term_name"))
                      ->where("$this->_name.course_id=?",$course_id)
					  ->group("$this->_name.term_name");
								  
        $result=$this->getAdapter()
                      ->fetchAll($select); 
					  
     			  
        return $result;
    }
	public function trashItems($course_id='') { 

        $this->_db->delete($this->_name, "course_id=$course_id");

    }
	
	
}