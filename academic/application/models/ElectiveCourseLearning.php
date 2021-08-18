<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ElectiveCourseLearning extends Zend_Db_Table_Abstract
{
    public $_name = 'elective_course_learning_master';
    protected $_id = 'ecrl_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_id=?", $id)				   
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
					->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("short_code","from_date","to_date"))
					 ->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name")) 
					 ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name")) 
					  ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
					//  ->where("course.ct_id !=?", 1)
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_id DESC")
					  ->group("$this->_id");
					//  ->group("$this->_id");
        $result=$this->getAdapter()
                      ->fetchAll($select); 
					//print_r($result) ;die; 
        return $result;
    }
	
	 public function getDropDownList(){
        $select = $this->_db->select()
                ->from($this->_name, array('ecrl_id','elective_name'))				
				->where("$this->_name.status!=?",2);
              //  ->order('course_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['ecrl_id']] = $val['elective_name'];
        }
        return $data;
    }
 
 
	public function getTerms($academic_year_id) {

        $select = $this->_db->select()
                ->from($this->_name,array('term_id'))
->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))				
				->where("$this->_name.academic_year_id =?", $academic_year_id)
				
				->where("$this->_name.status!=?", 2)
				->group ("$this->_name.term_id");
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
		//print_r($result);die;
		return $result;
		
    }
	
	public function getTermCount($academic_year_id,$term_id) {

        $select = $this->_db->select()
                ->from($this->_name,array('term_id'))	
				->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
				->where("$this->_name.academic_year_id =?", $academic_year_id)
				->where("$this->_name.term_id =?", $term_id)
				->where("$this->_name.status!=?", 2);
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
		//print_r($result);die;
		return $result;
		
    }
	public function getCount($academic_year_id,$term_id){
		$select = $this->_db->select()
					->from($this->_name,array('count("term_id") as term_count'))
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.term_id=?",$term_id)
					->where("$this->_name.status != ?",2);
		$result = $this->getAdapter()
					->fetchRow($select);
					
			return $result;		
		
	}
	public function GetCourseCount($academic_id,$course_id){
		$select = $this->_db->select()
				  ->from($this->_name,array('count("course_id") as course_count'))
             	  ->where("$this->_name.academic_year_id=?",$academic_id)
                  ->where("$this->_name.course_id=?",$course_id)
                   ->where("$this->_name.status != ?", 2);
      $result = $this->getAdapter()
					->fetchRow($select);
	  return $result;				
		
	}
	public function getTermWiseCourses($academic_id,$term_id)
	{
		 $select = $this->_db->select()
		           ->from($this->_name)
				   ->joinLeft(array('course'=>'course_master'),"course.course_id=$this->_name.course_id",array("course_id as id","course_name","status"))
				   ->joinLeft(array('credit'=>'credit_master'),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				   ->where("$this->_name.academic_year_id=?",$academic_id)
				   ->where("$this->_name.term_id=?",$term_id)
				   ->where("$this->_name.status !=?",2)
				   ->where("course.status != ?",2);
	   $result = $this->getAdapter()
                  ->fetchAll($select);
       $data = array();
       foreach($result as $val){
         $data[$val['id']] = $val['course_name'].'('.$val['credit_value'].')';

	   }
       return $data;	   
		
	}	
	public function getAllElectiveCourses($academic_year_id,$term_id){
		
		 $select = $this->_db->select()
		           ->from($this->_name)
				   ->joinLeft(array('course'=>'course_master'),"course.course_id=$this->_name.course_id",array("course_id","course_name","status"))
				   ->joinLeft(array('credit'=>'credit_master'),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				   ->where("$this->_name.academic_year_id=?",$academic_year_id)
				   ->where("$this->_name.term_id=?",$term_id)
				   ->where("$this->_name.status !=?",2)
				   ->where("course.status != ?",2);
	   $result = $this->getAdapter()
                  ->fetchAll($select);
	 return $result;	
	}
	
	public function getElectiveCreditsRecord($academic_year_id,$term_id,$elective_id){
		
		 $select = $this->_db->select()
		           ->from($this->_name)
				   ->joinLeft(array('credit'=>'credit_master'),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				   ->where("$this->_name.academic_year_id=?",$academic_year_id)
				   ->where("$this->_name.term_id=?",$term_id)
				   ->where("$this->_name.course_id=?",$elective_id)
				   ->where("$this->_name.status !=?",2);
				 //echo $select;die;
	   $result = $this->getAdapter()
                  ->fetchRow($select);
	 return $result;	
	}
}
?>