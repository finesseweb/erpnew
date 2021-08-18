<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ExperientialLearning extends Zend_Db_Table_Abstract
{
    public $_name = 'experiential_learning_master';
    protected $_id = 'el_id';
  
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
					  ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("short_code","from_date","to_date"))
					  ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
					  ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=$this->_name.elc_id",array("elc_name"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	public function getDropDownList(){
        $select = $this->_db->select()
     ->from($this->_name, array('course_id','course_name'))				
				->where("$this->_name.status!=?",2)
                ->order('course_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['course_id']] = $val['course_name'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
	
	
	
	public function getExperientialRecords($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
			    ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=$this->_name.elc_id",array("elc_name"))
				->where("$this->_name.academic_year_id =?", $academic_id)
				->where("$this->_name.status!=?", 2);
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
		
//echo "<pre>";		print_r($result); die;
				
		return $result;
		

    }
	
	public function getProgram($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
			    ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=$this->_name.elc_id",array("elc_name"))
				->where("$this->_name.academic_year_id =?", $academic_id)
				->where("$this->_name.status!=?", 2);
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
		
		//print_r($result); die;
				
		return $result;
		

    }
	
	
	public function getExpCourseRecords($academic_year_id,$year_id)
	{
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
					->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=  $this->_name.elc_id",array("elc_name"))
					->where("$this->_name.status != 2")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.year_id=?",$year_id);
			$result = $this->getAdapter()
					->fetchAll($select);
					
		return $result;			
		
	}
	
	public function getExperRecords($academic_year_id,$year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_year_id =?", $academic_year_id)
					  ->where("$this->_name.year_id =?", $year_id)
					  ->order("$this->_name.terms_id ASC");
					 // ->group("exprcourse.elc_id");	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
    public function getExperRecordsByBatches($batch_list,$year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=  $this->_name.elc_id",array("elc_name"))
                      ->joinLeft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
                      ->where("$this->_name.academic_year_id IN (?)", $batch_list)
					  ->where("$this->_name.year_id =?", $year_id)
                                          ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.terms_id ASC");
					 // ->group("exprcourse.elc_id");	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
    
     
    
    
    public function getExpCourseDetailByAcademicCourseId($academic_year_id,$course_id)
    {        
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->joinLeft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
                      ->where("$this->_name.academic_year_id =?", $academic_year_id)
					  ->where("$this->_name.elc_id =?", $course_id)
                                          ->where("$this->_name.status != 2");
					  
					 // ->group("exprcourse.elc_id");	
        //echo $select;die;					  
        $result = $this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
    //=============================Edited by satyam 29-04-2019 for experiential learning (first and last date record)=============================================================
       public function getfirstlastdateRecord($academic_year_id,$terms_id) {
        $select = "SELECT start_date, end_date FROM term_master  Where  academic_year_id='$academic_year_id' and term_id='$terms_id' ";
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;
        return $result;
    }
    //========================================================================================
    
	
}
?>