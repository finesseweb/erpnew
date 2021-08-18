<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Course extends Zend_Db_Table_Abstract
{
    public $_name = 'course_master';
    protected $_id = 'course_id';
  
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
						->joinleft(array("academic"=>"course_type_master"),"academic.ct_id=$this->_name.ct_id",array("ct_name"))
						->joinLeft(array("cr_cat"=>"course_category_master"),"cr_cat.cc_id=$this->_name.course_category_id",array("cc_name"))
					  ->where("$this->_name.status !=?", 2);
					 // ->order("$this->_name.$this->_id ASC");
        $result=$this->getAdapter()
                      ->fetchAll($select); 
		//echo '<pre>'; print_r($result);die;				
        return $result;
    }
	
	public function getDropDownList(){
        $select = $this->_db->select()
     ->from($this->_name, array('course_id','course_name'))				
				->where("$this->_name.status!=?",2)
                ->order('course_name  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['course_id']] = $val['course_name'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
	
	
		public function getElectiveDropDownList(){
        $select = $this->_db->select()
     ->from($this->_name, array('course_id','course_name'))				
				->where("$this->_name.status!=?",2)
				->where("$this->_name.ct_id=?",2)
                ->order('course_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['course_id']] = $val['course_name'];
			
          
        }
        return $data;
    }
	
	
	public function getCoreDropDownList(){
        $select = $this->_db->select()
     ->from($this->_name, array('course_id','course_name'))				
				->where("$this->_name.status!=?",2)
				->where("$this->_name.ct_id=?",1)
                ->order('course_name  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['course_id']] = $val['course_name'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
	
	
	
	// same name query
	
	
	public function getcoursename($coursename) {

        $select = $this->_db->select()
                ->from($this->_name,array("course_name","course_id"))	
				->where("$this->_name.course_name =?", $coursename)
				->where("$this->_name.status!=?", 2)
                                ->order('course_name  ASC');
		
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		

    }
    public function getCourseCode($course_code) {

        $select = $this->_db->select()
                ->from($this->_name,array("course_name","course_id"))	
				->where("$this->_name.course_code =?", $course_code)
				->where("$this->_name.status!=?", 2)
                                 ->order('course_name  ASC');
        $result = $this->getAdapter()
                ->fetchRow($select);
       // echo $select; exit;
		return $result;
		

    }
    
    
     public function getCourseCodeById($course_id) {

        $select = $this->_db->select()
                ->from($this->_name,array("course_name","course_id","course_code"))	
				->where("$this->_name.$this->_id =?", $course_id)
				->where("$this->_name.status!=?", 2);
				                              
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		

    }
	
	
	public function getDropDownList11() {
				$select = $this->_db->select()
					->from("term_master", array('term_id', 'term_name'))
					->where("status!=?", 2)
					//->where("parent_id =?", 0)
					->order('term_id', 'ASC');
					
				$result = $this->getAdapter()->fetchAll($select);
				
				//print_r($result); die;
				$data1 = array();
				 foreach ($result as $val) {
				$data2[$val['term_id']] = $val['term_name'];
				$select1 = $this->_db->select()
					->from("core_course_master as courses_master", array('course_id'))
					->joinLeft(array("cou_mstr"=>"course_master"),"cou_mstr.course_id=courses_master.course_id",array("course_id as course","course_name"))
					->where("courses_master.status!=?", 2)
					->where("courses_master.term_id =?",$val['term_id']);
					//->order('modules.module_id', 'ASC');
					$result1 = $this->getAdapter()->fetchAll($select1);
					//print_r($result1); die;
					$data2 = array();
					foreach ($result1 as $pval) {
						$data2[$pval['course']] = $pval['course_name'];
						
						
					}
			$data1[$val['term_name']] = $data2;
        } 
		//echo '<pre>'; print_r($data1); die;
        return $data1;		
		
    }
	
	
	
	
	public function getDropDownList1($academic_id) {
		//print_r($ea_id); die;
				$select = $this->_db->select()
					->from("term_master", array('term_id', 'term_name','academic_year_id'))
					->where("status!=?", 2)
					->where("term_master.academic_year_id=?",$academic_id)
					//->where("parent_id =?", 0)
					->order('term_id', 'ASC');
					
				$result = $this->getAdapter()->fetchAll($select);
				
				//print_r($result); die;
				$data1 = array();
				 foreach ($result as $val) {
				$data2[$val['term_id']] = $val['term_name'];
				$select1 = $this->_db->select()
					->from("core_course_master as courses_master", array('course_id','academic_year_id'))
					->joinLeft(array("cou_mstr"=>"course_master"),"cou_mstr.course_id=courses_master.course_id",array("course_id as course","course_name"))
					->where("courses_master.status!=?", 2)
					->where("courses_master.academic_year_id =?",$academic_id);
					
					//->order('modules.module_id', 'ASC');
					$result1 = $this->getAdapter()->fetchAll($select1);
					//print_r($result1); die;
					$data2 = array();
					foreach ($result1 as $pval) {
						$data2[$pval['course']] = $pval['course_name'];
						
						
					}
			$data1[$val['term_name']] = $data2;
        } 
		//echo '<pre>'; print_r($data1); die;
        return $data1;		
		
    }
	
	 public function getElectiveNamesDropDown(){
        $select = $this->_db->select()
                ->from($this->_name, array('course_id','course_name'))				
				 ->where("$this->_name.status!=?",2)
				 ->where("$this->_name.ct_id=?",2)
                 ->order('course_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['course_id']] = $val['course_name'];
        }
        return $data;
    }
	 public function getCoursesDropDownList($category_id){
             if($category_id == 5){//If it elective category, select course type = elective course
                 $ct_type = 2;
             }
             else{
                 $ct_type = 1;
             }
		$select = $this->_db->select()
					->from($this->_name,array('course_id as id','course_name'))
					->joinLeft(array("core_course"=>"core_course_master"),"core_course.course_id=$this->_name.course_id",array("academic_year_id","course_id"))
					//->where("$this->_name.ct_id =?",$ct_type)
					->where("$this->_name.status!=?",2)
					->where("$this->_name.course_category_id=?",$category_id)
					->order('course_name ASC');
				//echo $select; die;
		$result = $this->getAdapter()->fetchAll($select);
		$data = array();
		foreach($result as $val){
			
			$data[$val['id']] = $val['course_name'];
		}
		return $data;
		
	} 
	public function getElectiveCoursesDropDownList($category_id){
		$select = $this->_db->select()
					->from($this->_name,array('course_id','course_name','ct_id'))
					->where("$this->_name.status != ?",2)
		             ->where("$this->_name.course_category_id=?",$category_id)
					 ->where("$this->_name.ct_id=?",2)
					 ->order('course_id ASC');
		$result = $this->getAdapter()->fetchAll($select);
       $data = array();
	   foreach($result as $val){
		  $data[$val['course_id']] = $val['course_name']; 
	   } 
	   return $data;
	}
	

     public function getEvlCoursesNames($courses_id){
		$select = $this->_db->select()
					->from($this->_name,array('course_id as id','GROUP_CONCAT(course_code) as course_code'))
					//->joinLeft(array("core_course"=>"core_course_master"),"core_course.course_id=$this->_name.course_id",array("academic_year_id","course_id"))
					->where("$this->_name.status!=?",2)
					->where("FIND_IN_SET($this->_id,(?))", "$courses_id");
		$result = $this->getAdapter()->fetchRow($select);
		return $result;	
	}
}
?>