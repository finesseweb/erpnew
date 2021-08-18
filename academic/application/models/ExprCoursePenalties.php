<?php
class Application_Model_ExprCoursePenalties extends Zend_Db_Table_Abstract
{
    protected $_name = 'experiential_course_wise_penalties';
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
	
	public function getExpCoursePenaltyRecord($academic_year_id,$year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("penalty_items"=>"experiential_course_wise_penalities_items"),"penalty_items.id=$this->_name.id",array("courses_id","student_id","academic_credits","penalties"))
                      ->where("$this->_name.academic_id=?", $academic_year_id)
					  ->where("$this->_name.year_id=?", $year_id);		   
        $result=$this->getAdapter()
                      ->fetchAll($select);    
        //print_r($result);die;					  
        return $result;
    }
	public function getExpCoursePenaltyRecordItems($academic_year_id,$year_id,$course_id,$student_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("penalty_items"=>"experiential_course_wise_penalities_items"),"penalty_items.id=$this->_name.id",array("courses_id","student_id","academic_credits","penalties"))
                      ->where("$this->_name.academic_id=?", $academic_year_id)
					  ->where("$this->_name.year_id=?", $year_id)
					  ->where("penalty_items.courses_id=?", $course_id)
					->where("penalty_items.student_id=?", $student_id);					  
					$result=$this->getAdapter()
                      ->fetchRow($select);    
        //print_r($result);die;					  
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

	public function getExpCourseWiseCreditValRecord($elc_id,$student_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("coursewise_items"=>"experiential_course_wise_penalities_items"),"coursewise_items.id=$this->_name.id",array("courses_id","student_id","academic_credits","penalties"))
                      ->where("coursewise_items.courses_id=?", $elc_id)
					  ->where("coursewise_items.student_id=?", $student_id);	   
        $result=$this->getAdapter()
                      ->fetchRow($select);
    //print_r($result);die;					  
        return $result;
    }
}

