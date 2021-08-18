<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_GradeAllocation extends Zend_Db_Table_Abstract
{
    public $_name = 'grade_allocation_master';
    protected $_id = 'grade_id';
  
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
					  ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array("short_code AS academic_year"))
					 ->joinLeft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					 ->joinLeft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))

					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    public function getRecordsByFacultyId($faculty_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array("short_code AS academic_year"))
					 ->joinLeft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					 ->joinLeft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))

					  ->where("$this->_name.status !=?", 2)
                                          ->where("$this->_name.employee_id =?", $faculty_id)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	//View purpose
	
	
public function getStudentRecords($grade_allocation_id){
	
	$select = $this->_db->select()
			->from($this->_name)
			->joinLeft(array("allocation_items"=>"grade_allocation_items"),"allocation_items.grade_allocation_id=$this->_name.grade_id",array("student_id","grade_value"))
			->joinLeft(array("student"=>"erp_student_information"),"student.student_id=allocation_items.student_id",array("CONCAT(student.stu_fname,student.stu_lname) AS student_name","student.student_id","student.stu_id"))
			->where("$this->_name.status!=2")
			->where("$this->_name.grade_id=?",$grade_allocation_id);
		$result = $this->getAdapter()
			->fetchAll($select);	
	return $result;
	
}
public function getGradeRecords($academic_year_id,$department_id,$employee_id,$term_id,$course_id,$student_id){
	
	$select = $this->_db->select()
		->from($this->_name)
		->joinLeft(array("allocation_items"=>"grade_allocation_items"),"allocation_items.grade_allocation_id=$this->_name.grade_id",array("student_id","grade_value","component_id"))
		->where("$this->_name.status != 2")
		->where("$this->_name.academic_id=?",$academic_year_id)
		->where("$this->_name.department_id=?",$department_id)
		->where("$this->_name.employee_id=?",$employee_id)
		->where("$this->_name.term_id=?",$term_id)
               ->where("$this->_name.course_id=?",$course_id)
              //->where("allocation_items.component_id=?",$component_id)
               ->where("allocation_items.student_id=?",$student_id);
		//echo $select;die;
      $result = $this->getAdapter()
		->fetchRow($select);

 return $result;
	
}	
public function isGradeAllocated($batch_id, $faculty_id, $term_id, $course_id){
    $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_id=?", $batch_id)	
                      ->where("$this->_name.employee_id=?", $faculty_id)
                      ->where("$this->_name.term_id=?", $term_id)	
                      ->where("$this->_name.course_id=?", $course_id)
			->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        if(is_array($result) && !empty($result)){
            return TRUE;
        }
        else{
            return FALSE;
        }
}
}
?>