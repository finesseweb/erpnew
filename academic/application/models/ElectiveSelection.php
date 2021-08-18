<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ElectiveSelection extends Zend_Db_Table_Abstract
{
    public $_name = 'erp_elective_selection';
    protected $_id = 'elective_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.elective_id=?", $id)				   
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
			       	->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("short_code AS academic_year"))
					->joinLeft(array("student"=>"erp_student_information"),"student.student_id=$this->_name.student_id",array("CONCAT(student.stu_fname,'-',student.stu_lname) AS stu_name"))
					->joinLeft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
					 
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }

	public function getacademicRecords($academic)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id=?", $academic)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	public function getElectives($academic_year_id)
	{
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array("GROUP_CONCAT(terms) as term_id","GROUP_CONCAT(elective_name) as elev_names","GROUP_CONCAT(students_id) as stu_ids","GROUP_CONCAT(electives) as tot_electives","terms","electives"))
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->group("elective_items.terms")
					->group("elective_items.electives")
					->where("$this->_name.status != 2");
					
					
				$result = $this->getAdapter()
						->fetchAll($select);
						
			return $result;			
					
					
		
		
	}
	
	//DashBoard
	
	
	public function getElectivesDashboard()
	{
		$select = $this->_db->select()
					->from($this->_name)
					//->joinLeft(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array("GROUP_CONCAT(terms) as term_id","GROUP_CONCAT(elective_name) as elev_names","GROUP_CONCAT(students_id) as stu_ids","GROUP_CONCAT(electives) as tot_electives","terms","electives"))
					->joinLeft(array("electives_items"=>"erp_elective_selection_items"),"electives_items.elective_id=$this->_name.elective_id",array("electives"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=electives_items.electives",array("course_name"))	
					->group("electives_items.electives")
					->where("$this->_name.status != 2");
				$result = $this->getAdapter()
						->fetchAll($select);
						
				//print_r($result);die;		
							
			return $result;			
					
					
		
		
	}
	public function getCountForElective($elective_id){
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_items"=>"erp_elective_selection_items"),"electives_items.elective_id=$this->_name.elective_id",array("count(electives) as elective_count","electives"))
					->where("electives_items.electives=?",$elective_id);
					//echo $select; die;
				$result = $this->getAdapter()
						->fetchRow($select);
						
				return $result;		
	}
	//DashBoard Ending
	
	public function getStudentsForElective($academic_year_id,$elective_id,$term_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("elective_selection_items"=>"erp_elective_selection_items"),"elective_selection_items.elective_id=$this->_name.elective_id",array("students_id as student_id","electives","terms"))
					->joinLeft(array("student_information"=>"erp_student_information"),"student_information.student_id=elective_selection_items.students_id",array("CONCAT(stu_fname,stu_lname) as students","stu_id"))
		           ->where("$this->_name.status != 2")
				   ->where("$this->_name.academic_year_id=?",$academic_year_id)
				   ->where("elective_selection_items.electives=?",$elective_id)
				   ->where("elective_selection_items.terms=?",$term_id);
				  
				 $result = $this->getAdapter()
								->fetchAll($select);
								
		return $result;						
		
		
	}
	
	
	public function getValidAcademicRecord($academic_year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id =?", $academic_year_id);
					  //->where("$this->_name.year_id =?", $year_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
	public function getStudentSelectedElectives($academic_year_id,$term_id,$student_id){
		
		$select = $this->_db->select()
				->from($this->_name)
				->joinLeft(array('selection_items'=>'erp_elective_selection_items'),"selection_items.elective_id=$this->_name.elective_id",array('electives'))
				->where("$this->_name.academic_year_id=?",$academic_year_id)
				->where("$this->_name.term_id=?",$term_id)
				->where("$this->_name.student_id=?",$student_id)
				->where("$this->_name.status != ?",2);
	$result = $this->getAdapter()
             ->fetchAll($select);
     return $result;			 
		
	}
	
	public function getValidStudentsRecord($academic_year_id,$student_id,$term_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id =?", $academic_year_id)
					  ->where("$this->_name.student_id =?", $student_id)
					  ->where("$this->_name.term_id =?", $term_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
    public function getSelectedElectives($elective_increment_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array('selection_items'=>'erp_elective_selection_items'),"selection_items.elective_id=$this->_name.elective_id",array("electives"))
					->where("$this->_name.elective_id=?",$elective_increment_id)
					->where("$this->_name.status!=2");
		$result = $this->getAdapter()
				->fetchAll($select);
				
				
		return $result;			
		
		
	}
        
        
        	public function getElectivesByTerm($academic_year_id,$term_id)
	{
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("elective_items"=>"erp_elective_selection_items"),"elective_items.elective_id=$this->_name.elective_id",array("GROUP_CONCAT(terms) as term_id","GROUP_CONCAT(elective_name) as elev_names","GROUP_CONCAT(students_id) as stu_ids","GROUP_CONCAT(electives) as tot_electives","terms","electives","credit_value"))
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.term_id=?",$term_id)
                        		->group("elective_items.terms")
					->group("elective_items.electives")
					->where("$this->_name.status != 2");		
				//echo $select ; exit;	
				$result = $this->getAdapter()
						->fetchAll($select);
						
			return $result;			
					
					
		
		
	}
        
        
        
}
?>