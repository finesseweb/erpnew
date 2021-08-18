<?php
class Application_Model_ExprCourseGradeAftrPenalties extends Zend_Db_Table_Abstract
{
    protected $_name = 'experiential_course_grade_aftr_penalties';
    protected $_id = 'id';

    /**
     * Set Primary Key Id as a Parameter 
     * @return single dimention array
     */
	//get record (edit) 
    public function getRecord($id)
    {
        $select=$this->_db->select()
				->from($this->_name)
				->where("$this->_id=?",$id);
			$result=$this->getAdapter()
			->fetchRow($select);
        return $result;
    }
	
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array("from_date","to_date"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	public function getValidRecord($academic_year_id,$year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_id =?", $academic_year_id)
					  ->where("$this->_name.year_id =?", $year_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }

	
	public function getGradeSheetRecords($academic_year_id,$year_id,$stu_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("gradeaftritems"=>"experiential_course_grade_aftr_penalty_items"),"gradeaftritems.id=$this->_name.id",array("courses_id","student_id","academic_credits","grade_point_avg"))
					  ->joinleft(array("expr"=>"experiential_learning_master"),"expr.elc_id=gradeaftritems.courses_id",array("elc_id","credit_id","start_date","end_date"))
					  ->joinleft(array("exprcourse"=>"experiential_learning_components_master"),"exprcourse.elc_id=expr.elc_id",array("elc_name as course_name"))
					  ->joinleft(array("credits"=>"credit_master"),"credits.credit_id=gradeaftritems.academic_credits",array("credit_value"))
                      ->where("$this->_name.academic_id =?", $academic_year_id)
					  ->where("$this->_name.year_id =?", $year_id)
					  ->where("gradeaftritems.student_id =?", $stu_id)
					  ->group("exprcourse.elc_id");	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
	
	public function getExperRecordCredits($term_id,$academic_year_id,$year_id,$stu_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("gradeaftritems"=>"experiential_course_grade_aftr_penalty_items"),"gradeaftritems.id=$this->_name.id",array("courses_id","student_id","academic_credits","grade_point_avg","grade_aftr_penalty","grade_point_avg"))
					  ->joinleft(array("expr"=>"experiential_learning_master"),"expr.elc_id=gradeaftritems.courses_id",array("elc_id","credit_id","start_date","end_date"))
					  ->joinleft(array("exprcourse"=>"experiential_learning_components_master"),"exprcourse.elc_id=expr.elc_id",array("elc_name as course_name"))
					  ->joinleft(array("credits"=>"credit_master"),"credits.credit_id=gradeaftritems.academic_credits",array("credit_value"))
                      ->where("expr.terms_id =?", $term_id)
					  ->where("expr.academic_year_id =?", $academic_year_id)
					  ->where("expr.year_id =?", $year_id)
					  ->where("gradeaftritems.student_id =?", $stu_id)
					  ->group("$this->_name.id");	
       //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
       //print_r($result);					  
        return $result;
    }
	
	
	public function getTermsExprRecords($academic_year_id,$year_id,$stu_id)
    {       
       // $st_day = substr($start_date,0,2);
		//$st_month = substr($start_date,3,2);
		//$st_year = substr($start_date,6,4);
		//$st_date = $st_day.'-'.$st_month.'-'.$st_year;
		//print_r($st_date);die;
		
		$select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("gradeaftritems"=>"experiential_course_grade_aftr_penalty_items"),"gradeaftritems.id=$this->_name.id",array("courses_id","student_id","academic_credits","grade_point_avg","grade_aftr_penalty","grade_point_avg"))
					  ->joinleft(array("expr"=>"experiential_learning_master"),"expr.elc_id=gradeaftritems.courses_id",array("elc_id","credit_id","start_date","end_date"))
					 ->joinleft(array("credits"=>"credit_master"),"credits.credit_id=gradeaftritems.academic_credits",array("credit_value"))
                     //->where("expr.terms_id =?", $terms_id)
					 ->where("expr.academic_year_id =?", $academic_year_id)
					 ->where("expr.year_id =?", $year_id)
					 ->where("gradeaftritems.student_id =?", $stu_id)
					 ->group("$this->_name.id");		
		//echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
       //print_r($result);die;					  
        return $result;
    }
	public function getGrades($academic_id,$year_id,$course_id,$student_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("expr_items"=>"experiential_course_grade_aftr_penalty_items"),"expr_items.id=$this->_name.id",array("grade_point_avg","courses_id","student_id"))
					->where("$this->_name.academic_id=?",$academic_id)
					->where("$this->_name.year_id=?",$year_id)
					->where("expr_items.courses_id=?",$course_id)
					->where("expr_items.student_id=?",$student_id);
					//echo $select; die;
			$result = $this->getAdapter()
						->fetchRow($select);
			return $result;			
		
		
		
	}
	public function getGradePoint($academic_id,$year_id,$student_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("expr_items"=>"experiential_course_grade_aftr_penalty_items"),"expr_items.id=$this->_name.id",array("student_id","academic_credits","grade_point_avg"))
					->where("$this->_name.academic_id=?",$academic_id)
                    ->where("$this->_name.year_id=?",$year_id)
					->where("expr_items.student_id=?",$student_id);
					
			$result = $this->getAdapter()
					->fetchAll($select);
					
			return $result;		
		
		
		
	}
	
}

