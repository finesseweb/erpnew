<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_DirectFinalGrade extends Zend_Db_Table_Abstract
{
    public $_name = 'direct_final_grade_dmi';
    protected $_id = 'id';
  
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
					  ->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					   ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
					   ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date","short_code"))
                                           ->joinleft(array("student"=>"erp_student_information"),"student.student_id=$this->_name.student_id",array("stu_fname", "stu_lname"))
					  ->where("$this->_name.deleted !=?", 1)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
    public function getRecordDetail($id)
    {  
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					   ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
                                           ->joinleft(array("student"=>"erp_student_information"),"student.student_id=$this->_name.student_id",array("stu_fname", "stu_lname"))
					   ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date"))
					  ->where("$this->_name.id =?", $id)
                                          ->where("$this->_name.deleted !=?", 1)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	
//------------------
	public function getcredit()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					   ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);  
//print_r($result); die;					  
        return $result;
    }
    public function isGradeExist($academic_year_id, $term_id, $course_id, $student_id){
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id=?", $academic_year_id)
                      ->where("$this->_name.term_id=?", $term_id)
                      ->where("$this->_name.course_id=?", $course_id)
                      ->where("$this->_name.student_id=?", $student_id)
					  ->where("$this->_name.deleted =?", 0);
        $result=$this->getAdapter()
                      ->fetchRow($select);  
        if(is_array($result) && !empty($result)){
            return 1;
        }
        else{
            return 0;
        }
    }
    public function fetchCourseGrades($academic_year_id, $term_id, $student_id){
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id=?", $academic_year_id)
                      ->where("$this->_name.term_id=?", $term_id)
                      ->where("$this->_name.student_id=?", $student_id)
					  ->where("$this->_name.deleted =?", 0);
        $result=$this->getAdapter()
                      ->fetchAll($select);  
//print_r($result); die;					  
        return $result;
        
    }
    public function fetchTermSumCourseGrades($academic_year_id, $term_id, $student_id){
        $select=$this->_db->select()
                      ->from($this->_name, array('sum(grade_credit_multiplied) as sum_grade_credit, sum(credit_value) as sum_credit'))
                      ->where("$this->_name.academic_year_id=?", $academic_year_id)
                      ->where("$this->_name.term_id=?", $term_id)
                      ->where("$this->_name.student_id=?", $student_id)
					  ->where("$this->_name.deleted =?", 0);
        $result=$this->getAdapter()
                      ->fetchRow($select);  
//print_r($result); die;					  
        return $result;
        
    }
	
	
	//View purpose
	
	public function getcorecourselearning($academic_year_id='',$term_id='') {
		//if( !empty( $academic_year_id ) || !empty( $term_id )) {
		if(!empty($academic_year_id) && (!empty($term_id))){
        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
				 ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
				 ->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
				 ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				->where("$this->_name.academic_year_id =?", $academic_year_id)
				->where("$this->_name.term_id =?", $term_id)
				->where("$this->_name.status!=?", 2);
				
		}
		else{
			
			$select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
				 ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
				 ->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
				 ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				->where("$this->_name.academic_year_id =?", $academic_year_id)
				->where("$this->_name.status!=?", 2);
				
			
		}
		//}
        $result = $this->getAdapter()
                ->fetchAll($select);
		
				
		return $result;
		

    }
	
	
	
	
	
	
	
	public function getprogram($ccl_id) {

        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name","year_id"))
				 ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
				 ->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
				 ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				->where("$this->_name.academic_year_id =?", $ccl_id)
				->where("term.year_id=?",1)
				->where("$this->_name.status!=?", 2);
		$result = $this->getAdapter()
                ->fetchAll($select);
		
		
				
		return $result;
		

    }
    public function getprogramByYearTerm($acadecic_year_id, $term_id) {

        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name","year_id"))
				 ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
				 ->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
				 ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				->where("$this->_name.academic_year_id =?", $acadecic_year_id)
                                ->where("$this->_name.term_id =?", $term_id)				
				->where("$this->_name.status!=?", 2);
		$result = $this->getAdapter()
                ->fetchAll($select);
		
		
				
		return $result;
		

    }
	public function getSecondyearCourses($academic_year_id) {

        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name","year_id"))
				 ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
				 ->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
				 ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				->where("$this->_name.academic_year_id =?", $academic_year_id)
				->where("term.year_id=?",2)
				->where("$this->_name.status!=?", 2);
				
		$result = $this->getAdapter()
                ->fetchAll($select);
		
		return $result;
		

    }
	
 public function getCoreCouseDetailByTermAcademicCourse($academic_year_id,$term_id,$course_id){
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->joinLeft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
                      ->where("$this->_name.academic_year_id=?", $academic_year_id)
                        ->where("$this->_name.term_id=?", $term_id)
                        ->where("$this->_name.course_id=?", $course_id)
                        ->where("$this->_name.status !=?", 2)
                        ->where("credit.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
						

		

	}
         public function getcorecourses($academic_year_id,$term_id){
        $select =  $this->_db->select()
					->from($this->_name)
                    ->joinLeft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
                     ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
                     ->where("$this->_name.status != 2")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
	                 ->where("$this->_name.term_id=?",$term_id);

         $result = $this->getAdapter()
					->fetchAll($select);

     return $result;
						

		

	}
	
	
	
	public function getTerm($academic_year_id)
	{
		
		$select  = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					->where("$this->_name.academic_year_id=?",$academic_year_id);
					
					
		$result = $this->getAdapter()
					->fetchAll($select);
					
					
			$data = array();

			foreach($result as $val)
			{
				$data[$val['term_id']] = $val['term_name'];


            }
	
      return $data;			
	}
	
	public function getCourseRecord($academic_year_id,$category_id,$course_id){
		$select = $this->_db->select()
					->from($this->_name,array("count(course_id) as course_count"))
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.cc_id=?",$category_id)
					->where("$this->_name.course_id=?",$course_id);
	$result = $this->getAdapter()
				->fetchRow($select);
				
	return $result;			
					
		
		
	}
			
	
}
?>