<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ElectivesGradeAllocationReport extends Zend_Db_Table_Abstract
{
    public $_name = 'electives_grade_allocation_report';
    protected $_id = 'elective_grade_report_id';
  
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
		      ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array("CONCAT(academic.from_date,'-',academic.to_date) AS academic_year"))

					  ->where("$this->_name.status !=?", 2)
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
	
public function getGradePointValue($academic_year_id,$term_id,$course_id,$student_id){
		$select = $this->_db->select()
                  ->from($this->_name)
					->joinLeft(array("courses_report_items"=>"courses_grade_allocation_report_items"),"courses_report_items.elective_grade_report_id=$this->_name.elective_grade_report_id",array("term_id","course_id","student_id","grade_point"))
                   ->where("$this->_name.academic_id=?",$academic_year_id)
					->where("courses_report_items.term_id=?",$term_id)
                    ->where("courses_report_items.course_id=?",$course_id)
                    ->where("courses_report_items.student_id=?",$student_id);

        $result = $this->getAdapter()
				->fetchRow($select);


      return $result;

   }
   public function getElectives($academic_year_id,$term_id,$student_id){
	   $select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_report_items"=>"electives_grade_allocation_report_items"),"electives_report_items.elective_grade_report_id=$this->_name.elective_grade_report_id",array("term_id","student_id","elective_id","grade_point"))
					->where("$this->_name.academic_id=?",$academic_year_id)
					->where("electives_report_items.term_id=?",$term_id)
					->where("electives_report_items.student_id=?",$student_id);
					//echo $select; die;
					
		$result = $this->getAdapter()
				->fetchAll($select);
				
				
				return $result;
	   
   }

public function getEGARCount($academic_year_id,$department_id,$employee_id){
			
			$select = $this->_db->select()
					->from($this->_name)
					//->where("$this->_name.employee_id IS NULL")
					->where("$this->_name.academic_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("$this->_name.status != 2");
			//echo $select;die;		
			$result = $this->getAdapter()
					       ->fetchRow($select);
			//print_r($result);die;		
		    return $result;
			
	}   
	

}
?>