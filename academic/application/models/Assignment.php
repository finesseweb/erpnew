<?php 
class Application_Model_Assignment extends Zend_Db_Table_Abstract
{
    public $_name = 'faculty_assignment';
    protected $_id = 'assignment_id';
    
  
            	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)	;			   
					  //->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
    
   public function getRecords()
    {       
       if($_SESSION['admin_login']['admin_login']->empl_id){
        $select=$this->_db->select()
                      ->from($this->_name)		   
		  ->where("$this->_name.empl_id =?", $_SESSION['admin_login']['admin_login']->empl_id);
   //   echo $select; die;
        $result=$this->getAdapter()
                      ->fetchAll($select);    

        return $result;
       }
 else {
            $select=$this->_db->select()
                      ->from($this->_name);			   
		  //->where("$this->_name.status !=?", 2);
   //   echo $select; die;
        $result=$this->getAdapter()
                      ->fetchAll($select);    

        return $result;
       }
    }
    
    
    public function getRecordsByCurrentBatchAndTerm($batch_id, $term_id)
    {       
        
        
          $select=$this->_db->select()
                      ->from($this->_name)	
                ->joinLeft(array("assignment" => "assignment_submitted"), "assignment.assignment_id=$this->_name.$this->_id")
                ->where("$this->_name.academic_year_id= ?",$batch_id)
                ->where("$this->_name.term_id = ?", $term_id)
                ->where("assignment.student_id = ?",$_SESSION['admin_login']['admin_login']->student_id)
                // ->where("assignment.notification_status !=?", 1)
               // ->where("assignment.assignment_status !=?", 1)
		  ->where("$this->_name.status !=?", 2);
   // echo $select; exit;
        $result=$this->getAdapter()
                      ->fetchAll($select);    

        return $result;
        
        
        
      /*  $select=$this->_db->select()
                      ->from($this->_name)	
                ->where('academic_year_id = ?',$batch_id)
                ->where('term_id = ?', $term_id)
		  ->where("$this->_name.status !=?", 2);
 
        $result=$this->getAdapter()
                      ->fetchAll($select);    

        return $result;*/
    }
    

    
      
    
    
       public function getRecordsByCurrentBatchAndTerm1($batch_id, $term_id)
    {       
           //echo "<pre>";print_r($_SESSION);exit;
           
        $select=$this->_db->select()
                      ->from($this->_name)	
                ->joinLeft(array("assignment" => "assignment_submitted"), "assignment.assignment_id=$this->_name.$this->_id")
                ->where("$this->_name.academic_year_id= ?",$batch_id)
                ->where("$this->_name.term_id = ?", $term_id)
                ->where("assignment.student_id = ?",$_SESSION['admin_login']['admin_login']->student_id)
                 ->where("assignment.notification_status !=?", 1)
                ->where("assignment.assignment_status !=?", 1)
		  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);    

        return $result;
    }
    
     public function getCourseName($course_id)
    {       
        $select=$this->_db->select()
                      ->from('course_master',array('course_code','course_name'))	
                ->where('course_id= ?',$course_id)
		  ->where("status !=?", 2);
        //echo $select; die;
        $result=$this->getAdapter()
                      ->fetchRow($select);    

        return $result;
    }
    
    }