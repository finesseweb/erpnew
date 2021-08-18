<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ElectivesFacultyAllotment extends Zend_Db_Table_Abstract
{
    public $_name = 'electives_faculty_allotment';
    protected $_id = 'faculty_allotment_id';
  
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
						->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	public function getElectiveEmployeeTerms($academic_year_id,$department_id,$employee_id){
		
		$select =  $this->_db->select()
					->from($this->_name)
					->joinLeft(array("course_allotment_items"=>"courses_faculty_allotment_items"),"course_allotment_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","cc_id","course_id","credit_value","department_id","employee_id"))
					->joinleft(array("term"=>"term_master"),"term.term_id=course_allotment_items.term_id",array("term_name"))
					   ->joinleft(array("course"=>"course_master"),"course.course_id=course_allotment_items.course_id",array("course_name"))
					   ->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=course_allotment_items.cc_id",array("cc_name"))
					->where("$this->_name.status != 2")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("course_allotment_items.department_id=?",$department_id)
					->where("course_allotment_items.employee_id=?",$employee_id);
					
					$result = $this->getAdapter()
							->fetchAll($select);
			return $result;				
	}
	public function getElectiveEmployeeElectives($academic_year_id,$department_id,$employee_id){
		
		$select =  $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_allotment_items"=>"electives_faculty_allotment_items"),"electives_allotment_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","elective_id","credit_value","department_id","employee_id"))
					->joinleft(array("term"=>"term_master"),"term.term_id=electives_allotment_items.term_id",array("term_name"))
					->joinleft(array("course"=>"course_master"),"course.course_id=electives_allotment_items.elective_id",array("course_name as elective_name"))
					->where("$this->_name.status != 2")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("electives_allotment_items.department_id=?",$department_id)
					->where("electives_allotment_items.employee_id=?",$employee_id);
					
					$result = $this->getAdapter()
							->fetchAll($select);
			return $result;				
	}
	public function getTermsCoursewise($academic_year_id,$department_id,$employee_id,$course_type){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("courses_items"=>"courses_faculty_allotment_items"),"courses_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","course_id","department_id","employee_id"))
					->joinLeft(array("term"=>"term_master"),"term.term_id=courses_items.term_id",array("term_id as term","term_name"))
					->where("$this->_name.status != 2")
		            ->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("courses_items.department_id=?",$department_id)
					->where("courses_items.employee_id=?",$employee_id)
					->where("courses_items.course_type=?",$course_type);
					
					
				$result = $this->getAdapter()
					     ->fetchAll($select);
						 
						 
				$data = array();
              foreach($result as $k => $val){

			$data[$val["term"]] = $val["term_name"];

			  }			  
		return $data;
		
	}
	public function getTermsElectivesWise($academic_year_id,$department_id,$employee_id,$course_type)
	{
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_allotment_items"=>"electives_faculty_allotment_items"),"electives_allotment_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("course_type","term_id","elective_id","department_id","employee_id"))
					->joinLeft(array("term"=>"term_master"),"term.term_id=electives_allotment_items.term_id",array("term_id as term","term_name"))
					->where("$this->_name.status != 2")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("electives_allotment_items.department_id=?",$department_id)
					->where("electives_allotment_items.employee_id=?",$employee_id)
					->where("electives_allotment_items.course_type=?",$course_type);
				
					
				$result = $this->getAdapter()	
				          ->fetchAll($select);
						  
				$data  = array();

              foreach($result as $k => $val){
 
					$data[$val['term']] = $val['term_name'];
 			  }	
		
		return $data;
		
	}
	public function getCourses($academic_year_id,$department_id,$employee_id,$course_type,$term_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("courses_items"=>"courses_faculty_allotment_items"),"courses_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","course_id","department_id","employee_id"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=courses_items.course_id",array("course_id as course","course_name"))
					->where("$this->_name.status != 2")
		            ->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("courses_items.department_id=?",$department_id)
					->where("courses_items.employee_id=?",$employee_id)
					->where("courses_items.course_type=?",$course_type)
					->where("courses_items.term_id=?",$term_id);
					
					
				$result = $this->getAdapter()
					     ->fetchAll($select);
						 
						 
				$data = array();
              foreach($result as $k => $val){

			$data[$val["course"]] = $val["course_name"];

			  }			  
		return $data;
		
	}
	public function getComponentCourses($academic_year_id,$department_id,$employee_id,$course_type,$term_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("courses_items"=>"courses_faculty_allotment_items"),"courses_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","course_id","department_id","employee_id"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=courses_items.course_id",array("course_id as course","course_name"))
					->joinLeft(array("electives_grade_allocation"=>"electives_grade_allocation"),"electives_grade_allocation.employee_id=courses_items.employee_id",array("course_id"))
					->where("electives_grade_allocation.course_id IS NULL")
					->where("$this->_name.status != 2")
		            ->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("courses_items.department_id=?",$department_id)
					->where("courses_items.employee_id=?",$employee_id)
					->where("courses_items.course_type=?",$course_type)
					->where("courses_items.term_id=?",$term_id);
					
					
				$result = $this->getAdapter()
					     ->fetchAll($select);
						 
						 
				$data = array();
              foreach($result as $k => $val){

			$data[$val["course"]] = $val["course_name"];

			  }			  
		return $data;
		
	}
	public function getEditComponentCourses($academic_year_id,$department_id,$employee_id,$course_type,$term_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("courses_items"=>"courses_faculty_allotment_items"),"courses_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","course_id","department_id","employee_id"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=courses_items.course_id",array("course_id as course","course_name"))
					
					->where("$this->_name.status != 2")
		            ->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("courses_items.department_id=?",$department_id)
					->where("courses_items.employee_id=?",$employee_id)
					->where("courses_items.course_type=?",$course_type)
					->where("courses_items.term_id=?",$term_id);
					
					
				$result = $this->getAdapter()
					     ->fetchAll($select);
						 
						 
				$data = array();
              foreach($result as $k => $val){

			$data[$val["course"]] = $val["course_name"];

			  }			  
		return $data;
		
	}
	public function getElectives($academic_year_id,$department_id,$employee_id,$course_type,$term_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_items"=>"electives_faculty_allotment_items"),"electives_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","elective_id","department_id","employee_id"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=electives_items.elective_id",array("course_id as course","course_name"))
					->where("$this->_name.status != 2")
		            ->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("electives_items.department_id=?",$department_id)
					->where("electives_items.employee_id=?",$employee_id)
					->where("electives_items.course_type=?",$course_type)
					->where("electives_items.term_id=?",$term_id);
					
					
				$result = $this->getAdapter()
					     ->fetchAll($select);
						 
						 
				$data = array();
              foreach($result as $k => $val){

			$data[$val["course"]] = $val["course_name"];

			  }			  
		return $data;
		
	}
	public function getElectivesCourses($academic_year_id,$department_id,$employee_id,$course_type,$term_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_items"=>"electives_faculty_allotment_items"),"electives_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","elective_id","department_id","employee_id"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=electives_items.elective_id",array("course_id as course","course_name"))
					->joinLeft(array("electives_grade_allocation"=>"electives_grade_allocation"),"electives_grade_allocation.course_id=electives_items.elective_id",array("course_id"))
					->where("electives_grade_allocation.course_id IS NULL")
					->where("$this->_name.status != 2")
		            ->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("electives_items.department_id=?",$department_id)
					->where("electives_items.employee_id=?",$employee_id)
					->where("electives_items.course_type=?",$course_type)
					->where("electives_items.term_id=?",$term_id);
					
					
				$result = $this->getAdapter()
					     ->fetchAll($select);
						 
						 
				$data = array();
              foreach($result as $k => $val){

			$data[$val["course"]] = $val["course_name"];

			  }			  
		return $data;
		
	}
	public function getElectivesCoursesEdit($academic_year_id,$department_id,$employee_id,$course_type,$term_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_items"=>"electives_faculty_allotment_items"),"electives_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","elective_id","department_id","employee_id"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=electives_items.elective_id",array("course_id as course","course_name"))
					
					->where("$this->_name.status != 2")
		            ->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("electives_items.department_id=?",$department_id)
					->where("electives_items.employee_id=?",$employee_id)
					->where("electives_items.course_type=?",$course_type)
					->where("electives_items.term_id=?",$term_id);
					
					
				$result = $this->getAdapter()
					     ->fetchAll($select);
						 
						 
				$data = array();
              foreach($result as $k => $val){

			$data[$val["course"]] = $val["course_name"];

			  }			  
		return $data;
		
	}
	/* public function getComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("courses_items"=>"courses_faculty_allotment_items"),"courses_items.faculty_allotment_id=$this->_name.faculty_allotment_id",array("term_id","course_id","department_id","employee_id"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=courses_items.course_id",array("course_id as course","course_name"))
					->where("$this->_name.status != 2")
		            ->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("courses_items.department_id=?",$department_id)
					->where("courses_items.employee_id=?",$employee_id)
					->where("courses_items.course_type=?",$course_type)
					->where("courses_items.term_id=?",$term_id);
					
					
				$result = $this->getAdapter()
					     ->fetchAll($select);
						 
						 
				$data = array();
              foreach($result as $k => $val){

			$data[$val["course"]] = $val["course_name"];

			  }			  
		return $data;
		
	} */
	
	
	
	public function getValidFacultyDataRecord($academic_year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id =?", $academic_year_id);
					 // ->where("$this->_name.year_id =?", $year_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
}
?>