<?php

class Application_Model_SubmitAssignment extends Zend_Db_Table_Abstract {

    public $_name = 'assignment_submitted';
    protected $_id = 'submitted_id';

    
      public function getstudents($batch,$term){
       
          $select = $this->_db->select()
               // ->distinct()
                ->from(array('info'=>'erp_student_information'))
                ->join(array("student" => "student_attendance"), "student.student_id=info.student_id")
                  
                ->where("student.term_id=?", $term)
                ->where("student.batch_id =?", $batch)
                  ->group('student.student_id');
       //  echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    
      public function getstudentsbyEmpl($batch,$term,$course_id){
       
        $select=$this->_db->select()
                      ->from($this->_name)	
                ->joinLeft(array("assignment" => "faculty_assignment"), "assignment.assignment_id=$this->_name.assignment_id")
                ->where("assignment.academic_year_id= ?",$batch)
                ->where("assignment.term_id = ?", $term)
             //   ->where("assignment.student_id = ?",$_SESSION['admin_login']['admin_login']->student_id)
                ->where("assignment.empl_id =?", $_SESSION['admin_login']['admin_login']->empl_id)
                ->where("$this->_name.course_id =?",$course_id)
                // ->where("assignment.notification_status !=?", 1)
               // ->where("assignment.assignment_status !=?", 1)
		  ->where("assignment.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);    

        return $result;
    }
    
    
      public function getstudentsWithoutEmpl($batch,$term,$course_id){
       
        $select=$this->_db->select()
                      ->from($this->_name)	
                ->joinLeft(array("assignment" => "faculty_assignment"), "assignment.assignment_id=$this->_name.assignment_id")
                ->where("assignment.academic_year_id= ?",$batch)
                ->where("assignment.term_id = ?", $term)
                 ->where("$this->_name.course_id =?",$course_id)
               // ->where("assignment.student_id = ?",$_SESSION['admin_login']['admin_login']->student_id)
              //  ->where("assignment.empl_id =?", $_SESSION['admin_login']['admin_login']->empl_id)
                // ->where("assignment.notification_status !=?", 1)
               // ->where("assignment.assignment_status !=?", 1)
		  ->where("assignment.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);    

        return $result;
    }
    
    
    
    
}