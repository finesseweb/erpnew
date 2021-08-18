<?php
class Application_Model_ExprCourseGrade extends Zend_Db_Table_Abstract
{
    protected $_name = 'experiential_course_wise_grades';
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

	public function getExpCourseGradeRecord($academic_year_id,$year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("grade_items"=>"experiential_course_wise_grade_items"),"grade_items.id=$this->_name.id",array("courses_id","student_id","academic_credits","credit_value"))
                      ->where("$this->_name.academic_id=?", $academic_year_id)
					  ->where("$this->_name.year_id=?", $year_id);	   
        $result=$this->getAdapter()
                      ->fetchAll($select);
    //print_r($result);die;					  
        return $result;
    }
	
	public function getExpCourseGradeRecordItems($academic_year_id,$year_id,$course_id,$student_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("grade_items"=>"experiential_course_wise_grade_items"),"grade_items.id=$this->_name.id",array("courses_id","student_id","academic_credits","credit_value"))
                      ->where("$this->_name.academic_id=?", $academic_year_id)
					  ->where("$this->_name.year_id=?", $year_id)
					  ->where("grade_items.courses_id=?", $course_id)
					  ->where("grade_items.student_id=?", $student_id);
		//print_r($select);die;			  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
   // print_r($result);die;					  
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
	
	
	public function getExpCourseCreditValRecord($elc_id,$student_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("grade_items"=>"experiential_course_wise_grade_items"),"grade_items.id=$this->_name.id",array("courses_id","student_id","academic_credits","credit_value"))
                      ->where("grade_items.courses_id=?", $elc_id)
					  ->where("grade_items.student_id=?", $student_id);	   
        $result=$this->getAdapter()
                      ->fetchRow($select);
    //print_r($result);die;					  
        return $result;
    }
	
}

