<?php

/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class Application_Model_GradeAllocationReport extends Zend_Db_Table_Abstract {

    public $_name = 'grade_allocation_report';
    protected $_id = 'grade_report_id';

    //get details by record for edit
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;					  
        return $result;
    }

    //Get all records
    public function getRecords() {

        if ($_SESSION['admin_login']['admin_login']->empl_id) {
            $select = $this->_db->select()
                    ->from($this->_name)
                    ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("short_code AS academic_year"))
                    ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id", array("term_id as term", "term_name"))
                    ->where("$this->_name.employee_id =?", $_SESSION['admin_login']['admin_login']->empl_id)
                    ->where("$this->_name.status !=?", 2)
                    ->order("$this->_name.$this->_id DESC");
            $result = $this->getAdapter()
                    ->fetchAll($select);
        } else {
            $select = $this->_db->select()
                    ->from($this->_name)
                    ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("short_code AS academic_year"))
                    ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id", array("term_id as term", "term_name"))
                    ->where("$this->_name.status !=?", 2)
                    ->order("$this->_name.$this->_id DESC");
            $result = $this->getAdapter()
                    ->fetchAll($select);
        }
        return $result;
    }

    public function getRecordsByFacultyId($faculty_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("CONCAT(academic.from_date,'-',academic.to_date) AS academic_year"))
                ->where("$this->_name.employee_id =?", $faculty_id)
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.employee_id =?", $faculty_id)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    //View purpose

    public function getCourseInfo($id) {

        $select = $this->_db->select()
                ->from('course_master', array('course_code', 'course_name'))
                ->where("course_id =?", $id)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getStudentRecords($grade_allocation_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("allocation_items" => "grade_allocation_items"), "allocation_items.grade_allocation_id=$this->_name.grade_id", array("student_id", "grade_value"))
                ->joinLeft(array("student" => "erp_student_information"), "student.student_id=allocation_items.student_id", array("CONCAT(student.stu_fname,student.stu_lname) AS student_name", "student.student_id", "student.stu_id"))
                ->where("$this->_name.status!=2")
                ->where("$this->_name.grade_id=?", $grade_allocation_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getGradePointValue($academic_year_id, $term_id, $course_id, $student_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("report_items" => "grade_allocation_report_items"), "report_items.report_id=$this->_name.grade_report_id", array("term_id", "course_id", "student_id", "grade_point"))
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("report_items.term_id=?", $term_id)
                ->where("report_items.course_id=?", $course_id)
                ->where("report_items.student_id=?", $student_id);

        $result = $this->getAdapter()
                ->fetchRow($select);


        return $result;
    }

    public function getGradeAllocateCount($academic_year_id, $department_id, $employee_id, $term_id, $course_id) {
        if($academic_year_id == 2){
            
            $select = $this->_db->select()
                ->from($this->_name, array("count(employee_id) as employee_count"))
                ->where("$this->_name.academic_id =?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.status != 2");
      //  echo $select;die;		
        $result = $this->getAdapter()
                ->fetchRow($select);
            
            
        }
 else {
     
        
        $select = $this->_db->select()
                ->from($this->_name, array("count(employee_id) as employee_count"))
                ->where("$this->_name.academic_id =?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.course_id=?", $course_id)
                ->where("$this->_name.status != 2");
      //  echo $select;die;		
        $result = $this->getAdapter()
                ->fetchRow($select);
 }
        //print_r($result);die;		
        return $result;
    }

}

?>