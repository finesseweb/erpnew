<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExperientialComponents
 *
 * @author w10
 */
class Application_Model_ExperientialEvaluationComponents extends Zend_Db_Table_Abstract {
    //put your code here
    public $_name = 'experiential_evaluation_components_items';
    protected $_id = 'elc_id';
    public function trashItems($elc_id){
        $this->_db->delete($this->_name, "ec_id=$elc_id");
    }
    public function getAlltermsandcourses($ec_id){
        //echo $ec_id;exit;
        if($ec_id){
		$select = $this->_db->select()
		           ->from($this->_name,array('GROUP_CONCAT(course_id) as courses'))
                        ->joinLeft(array("course"=>"experiential_learning_components_master"),"course.elc_id=$this->_name.course_id",array("elc_id as course","elc_name"))	
				   ->where("$this->_name.ec_id=?",$ec_id)
				   ->group("$this->_name.course_id");
                
				   $result = $this->getAdapter()
        ->fetchAll($select);}
        else{
            $result = array();
        }
            
                                    return $result;			 
				   
    }
    
    
    
       
      
    
    
    
    public function getComponentWeightageByAcademicYearCourseEmployee($academic_year_id, $employee_id, $course_id){
        $select = $this->_db->select()
		           ->from($this->_name)
                           ->joinLeft(array("ev_master"=>"evaluation_components_master"),"ev_master.ec_id=$this->_name.ec_id",array("academic_year_id","employee_id"))	
				   ->where("ev_master.academic_year_id=?",$academic_year_id)
                                   ->where("ev_master.employee_id=?",$employee_id)
                                   ->where("$this->_name.course_id=?",$course_id)
                                    ->where("ev_master.status !=?",2);
                //echo $select;
				   $result = $this->getAdapter()
				             ->fetchAll($select);
							 
							 
				return $result;			 
    }
}
