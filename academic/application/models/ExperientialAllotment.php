<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ExperientialAllotment extends Zend_Db_Table_Abstract
{
    public $_name = 'experiential_learning_allotment_master';
    protected $_id = 'allotment_id';
  
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
                        //->joinleft(array("academic"=>"course_type_master"),"academic.ct_id=$this->_name.ct_id",array("ct_name"))
						->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.batch_id",array("from_date","to_date","short_code"))
					  ->where("$this->_name.status !=?", 2)
					  ->order(array('',''));
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	
	
	public function getValidEmployeeRecord($batch_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.batch_id =?", $batch_id)
  ->where("$this->_name.status !=?", 2);
					 // ->where("$this->_name.year_id =?", $year_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
    public function getEvaluationItems($academic_year_id,$department_id,$employee_id) {
         $select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("allocation_items"=>"experiential_learning_allotment_items"),"allocation_items.allotment_id=$this->_name.allotment_id",array('course_id','department_id','employee_id','credit_id'))
					->joinLeft(array("course"=>"experiential_learning_components_master"),"course.elc_id=allocation_items.course_id",array("elc_id as course","elc_name as course_name"))	
					->where("$this->_name.batch_id=?",$academic_year_id)
					->where("allocation_items.department_id=?",$department_id)
					->where("$this->_name.status != 2")
                                        ->where("allocation_items.employee_id=?",$employee_id);
		//echo $select;die;			
		$result = $this->getAdapter()
			->fetchAll($select);
		//print_r($result); die;
		return $result;
    }
}
?>