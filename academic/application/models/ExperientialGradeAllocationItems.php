<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ExperientialGradeAllocationItems extends Zend_Db_Table_Abstract
{
    public $_name = 'experiential_grade_allocation_items';
    protected $_id = 'grade_allocation_item_id';
  
    //get details by record for edit
	public function getRecords($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.grade_allocation_id=?", $id);				   
					  //->where("$this->_name.status !=?", 2);
					 // echo $select;die;
        $result=$this->getAdapter()
                      ->fetchAll($select);    
   				  
        return $result;
    }
	
	public function trashItems($grade_allocation_id) {

        $this->_db->delete($this->_name, "grade_allocation_id=$grade_allocation_id");

    }
    public function getStudentPenalty($penalty_master_id, $student_id){
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.id=?", $penalty_master_id)                      
                      ->where("$this->_name.student_id=?", $student_id);		   
					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
	
	
	
	

}
?>