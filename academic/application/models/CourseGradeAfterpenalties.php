<?php
class Application_Model_CourseGradeAfterpenalties extends Zend_Db_Table_Abstract
{

    protected $_name = 'course_grade_after_penalties';

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
					->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array("from_date","to_date","short_code"))
                ->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }

    public function getGradeSheetRecord($academic_id,$year_id,$stu_id)
     {
        $select=$this->_db->select()
				->from($this->_name)
				->joinleft(array("items"=>"course_grade_after_penalties_items"),"items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_credits","academic_grades","final_grade","academic_electives","elective_ids","academic_electives"))
				->joinleft(array("terms"=>"term_master"),"terms.term_id=items.term_id",array("term_name","start_date","end_date"))
				//->joinleft(array("elective_course"=>"course_master"),"FIND_IN_SET(elective_course.course_id,items.elective_ids)",array("GROUP_CONCAT(elective_course.course_name) as elective_coursename"))
				->joinleft(array("course"=>"course_master"),"FIND_IN_SET(course.course_id,items.academic_courses)",array("GROUP_CONCAT(course.course_name) AS coursename"))
				->where("$this->_name.academic_id=?",$academic_id)
				->where("$this->_name.year_id=?",$year_id)
				->where("items.student_id=?",$stu_id)
				->group("items.academic_courses")
				->order("items.item_id ASC");
				//->group("items.elective_ids");
		    //echo $select;die;
			$result=$this->getAdapter()
			->fetchAll($select);
        return $result;
    }

	 public function getCourseGradeRecord($academic_year_id,$term_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_id =?", $academic_year_id)
                        ->where("$this->_name.term_id =?", $term_id)
                        ->where("$this->_name.status !=?", 2);
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
    public function getAllGradesAfterPenalties($batch_list,$student_list)
    {
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->joinleft(array("items"=>"course_grade_after_penalties_items"),"items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_credits","academic_grades","final_grade","academic_electives","elective_ids","academic_electives"))
                      ->where("$this->_name.academic_id IN (?)", $batch_list)
                      ->where("items.student_id IN (?)", $student_list)
                      ->where("items.final_grade > 0")
                      ->where("$this->_name.status !=?", 2);
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
    public function getAllGradesByBatch($batch_id)
    {
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->joinleft(array("items"=>"course_grade_after_penalties_items"),"items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_credits","academic_grades","final_grade","academic_electives","elective_ids","academic_electives","cgpa"))
                      ->where("$this->_name.academic_id = ?", $batch_id)
                      ->where("$this->_name.status !=?", 2);
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
    public function calculateCGPA($student_id, $current_credits, $current_gpa){
        //Fetching all batch id of students
        $student_model = new Application_Model_StudentPortal();
        $stu_pre_details1 = $student_model->fetchAllBatchesOfStudent($student_id);
        $batch_arr = array();
        $student_ids = array();
        foreach ($stu_pre_details1 as $row) {
               $batch_arr[] = $row['academic_id'];
               $student_ids[] = $row['student_id'];
        }
        $batch_arr[] = $row['academic_id'];

        $CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
        $grade_result = $CourseGradeAfterpenalties_model->getAllGradesAfterPenalties($batch_arr,$student_ids);
        $expGradeAllocation_model = new Application_Model_ExperientialGradeAllocation();
        $exp_grade_result = $expGradeAllocation_model->getExpGradesByBatches($batch_arr, $student_ids);
        //Calculating CGPA
        $total_credit_all_term = 0;
        $total_grade_sum = 0;
        foreach($grade_result as $row_grade1){
            $total_credit1 = array_sum(explode(',', $row_grade1['academic_credits']));
            $product1 = $row_grade1['final_grade'] * $total_credit1;

            $total_credit_all_term += $total_credit1;
            $total_grade_sum += $product1;
        }


        foreach($exp_grade_result as $row_grade1){
            $total_credit1 = array_sum(explode(',', $row_grade1['credit']));
            $product1 = $row_grade1['final_grade_point'] * $total_credit1;

            $total_credit_all_term += $total_credit1;
            $total_grade_sum += $product1;
        }

        //For current term
        $total_credit1 = array_sum(explode(',', $current_credits));  
        $product1 = $current_gpa * $total_credit1;
        $total_credit_all_term += $total_credit1;
        $total_grade_sum += $product1;
        //calculating CGP
        $cgpa = round($total_grade_sum/$total_credit_all_term, 2);
        return $cgpa;
    }

	public function getGradeSheetElectivesRecord($academic_id,$year_id,$stu_id)
     {
        $select=$this->_db->select()
				->from($this->_name)
				->joinleft(array("items"=>"course_grade_after_penalties_items"),"items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_credits","academic_grades","final_grade","academic_electives","elective_ids","academic_electives"))
				->joinleft(array("terms"=>"term_master"),"terms.term_id=items.term_id",array("term_name","start_date","end_date"))
				->joinleft(array("elective_course"=>"course_master"),"FIND_IN_SET(elective_course.course_id,items.elective_ids)",array("GROUP_CONCAT(elective_course.course_name) as elective_coursename"))		
				->where("$this->_name.academic_id=?",$academic_id)
				->where("$this->_name.year_id=?",$year_id)
				->where("items.student_id=?",$stu_id)
				->group("items.elective_ids");
		   // echo $select;die;
			$result=$this->getAdapter()
			->fetchAll($select);
        return $result;
    }
	
	public function getTermRecordsCreditgrades($academic_year_id,$year_id,$stu_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("items"=>"course_grade_after_penalties_items"),"items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_credits","academic_grades","final_grade","academic_electives","elective_ids","academic_electives"))//->joinleft(array("credits"=>"credit_master"),"credits.credit_id=items.academic_credits",array("credit_value"))
                      ->where("$this->_name.academic_id =?", $academic_year_id)
					  ->where("$this->_name.year_id =?", $year_id)
					  ->where("items.student_id =?", $stu_id);
       // echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
       //print_r($result);die;					  
        return $result;
    }
	public function getAllGrades($academic_year_id,$year_id,$term_id,$stu_id){
			$select = $this->_db->select()
						->from($this->_name)
						->joinLeft(array("courses_items"=>"course_grade_after_penalties_items"),"courses_items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_grades","final_grade","elective_ids","academic_electives","academic_credits"))
						->where("$this->_name.academic_id=?",$academic_year_id)
						->where("$this->_name.term_id=?",$term_id)
						->where("courses_items.term_id=?",$term_id)
						->where("courses_items.student_id=?",$stu_id);
						//echo $select; die;
				$result = $this->getAdapter()
						->fetchRow($select);
						
				return $result;		
	}
        public function getDirectFinalGrades($academic_year_id,$term_id,$stu_id){
			$select = $this->_db->select()
						->from($this->_name)
						->joinLeft(array("courses_items"=>"course_grade_after_penalties_items"),"courses_items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_grades","final_grade","elective_ids","academic_electives","academic_credits"))
						->where("$this->_name.academic_id=?",$academic_year_id)
						->where("$this->_name.term_id=?",$term_id)
						->where("courses_items.term_id=?",$term_id)
						->where("courses_items.student_id=?",$stu_id);
						//echo $select; die;
				$result = $this->getAdapter()
						->fetchRow($select);
						
				return $result;		
	}
	public function getFirstYearGrades($academic_year_id,$year_id,$stu_id){
			$select = $this->_db->select()
						->from($this->_name)
						->joinLeft(array("courses_items"=>"course_grade_after_penalties_items"),"courses_items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_grades","final_grade","elective_ids","academic_electives","academic_credits"))
						->where("$this->_name.academic_id=?",$academic_year_id)
						->where("$this->_name.term_id IN(SELECT term_id FROM term_master WHERE academic_year_id = $academic_year_id AND year_id = $year_id)")
						//->where("courses_items.term_id=?",$term_id)
						->where("courses_items.student_id=?",$stu_id);
						//echo $select; die;
				$result = $this->getAdapter()
						->fetchAll($select);
						
				return $result;		
	}
        public function getStudentGradesByBatches($batch_list, $term_list, $student_list){
            
			$select = $this->_db->select()
						->from($this->_name)
						->joinLeft(array("courses_items"=>"course_grade_after_penalties_items"),"courses_items.id=$this->_name.id",array("student_id","term_id","academic_courses","academic_grades","final_grade","elective_ids","academic_electives","academic_credits"))
						->where("$this->_name.academic_id IN (?)", $batch_list)
                                                ->where("$this->_name.term_id IN (?)", $term_list)
                                                ->where("courses_items.student_id IN (?)", $student_list)
                                                ->where("courses_items.final_grade > 0")
						->where("$this->_name.status != ?",2);
						//echo $select; die;
				$result = $this->getAdapter()
						->fetchAll($select);
						
				return $result;		
	}
        
        
         
        
}

