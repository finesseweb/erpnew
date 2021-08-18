<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ElectivesEvaluationComponents extends Zend_Db_Table_Abstract
{
    public $_name = 'electives_evaluation_components_master';
    protected $_id = 'el_ec_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);    
//print_r($result);die;					  
        return $result;
    }
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					   	->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("CONCAT(academic.from_date,'-',academic.to_date) AS academic_year"))
					  //->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					  // ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
					   //->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
					   //->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
					  // ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	//View purpose
	
	public function getcorecourselearning($corecourselearn) {

        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
				 ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
				 ->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
				 ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				->where("$this->_name.academic_year_id =?", $corecourselearn)
				->where("$this->_name.status!=?", 2);
		
        $result = $this->getAdapter()
                ->fetchAll($select);
		
		
				
		return $result;
		

    }
	public function getComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id){
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("courses_eval_items"=>"courses_evaluation_components_items"),"courses_eval_items.el_ec_id=$this->_name.el_ec_id",array("term_id","course_id","course_type","component_name","cr_eci_id"))
					->where("$this->_name.status != 2")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("courses_eval_items.course_type=?",$course_type)
					->where("courses_eval_items.term_id=?",$term_id)
                    ->where("courses_eval_items.course_id=?",$course_id);
				
         $result = $this->getAdapter()
					->fetchAll($select);
					
				$data = array();
					foreach($result as $k => $val)
					{

						$data[$val['cr_eci_id']] = $val['component_name'];
					}	
				
					return $data;
							
	}
	public function getComponentsForGradeAllocation($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id){
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("courses_eval_items"=>"courses_evaluation_components_items"),"courses_eval_items.el_ec_id=$this->_name.el_ec_id",array("term_id","course_id","course_type","component_name","cr_eci_id"))
					->joinLeft(array("grade_allocation"=>"electives_grade_allocation"),"grade_allocation.component_id=courses_eval_items.cr_eci_id",array("component_id"))
					->where("$this->_name.status != 2")
					->where("grade_allocation.component_id IS NULL")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("courses_eval_items.course_type=?",$course_type)
					->where("courses_eval_items.term_id=?",$term_id)
                    ->where("courses_eval_items.course_id=?",$course_id);
				
         $result = $this->getAdapter()
					->fetchAll($select);
					
				$data = array();
					foreach($result as $k => $val)
					{

						$data[$val['cr_eci_id']] = $val['component_name'];
					}	
				
					return $data;
							
	}
	public function getAddComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id){
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("courses_eval_items"=>"courses_evaluation_components_items"),"courses_eval_items.el_ec_id=$this->_name.el_ec_id",array("term_id","course_id","course_type","component_name","cr_eci_id"))
					->joinLeft(array("electives_comp_grade"=>"electives_evaluation_components_grade_master"),"courses_eval_items.cr_eci_id=electives_comp_grade.component_id",array("component_id"))
					->where("$this->_name.status != 2")
					->where("electives_comp_grade.component_id IS NULL")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("courses_eval_items.course_type=?",$course_type)
					->where("courses_eval_items.term_id=?",$term_id)
                    ->where("courses_eval_items.course_id=?",$course_id);
				
         $result = $this->getAdapter()
					->fetchAll($select);
					
				$data = array();
					foreach($result as $k => $val)
					{

						$data[$val['cr_eci_id']] = $val['component_name'];
					}	
				
					return $data;
							
	}
	public function getElectivesComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id){
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_eval_items"=>"electives_evaluation_components_items"),"electives_eval_items.el_ec_id=$this->_name.el_ec_id",array("term_id","elective_id","course_type","component_name","el_eci_id"))
					->where("$this->_name.status != 2")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("electives_eval_items.course_type=?",$course_type)
					->where("electives_eval_items.term_id=?",$term_id)
                    ->where("electives_eval_items.elective_id
					=?",$course_id);
				
         $result = $this->getAdapter()
					->fetchAll($select);
					
				$data = array();
					foreach($result as $k => $val)
					{

						$data[$val['el_eci_id']] = $val['component_name'];
					}	
				
					return $data;
							
	}
	public function getElectivesComponentsForGradeAllocation($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id){
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_eval_items"=>"electives_evaluation_components_items"),"electives_eval_items.el_ec_id=$this->_name.el_ec_id",array("term_id","elective_id","course_type","component_name","el_eci_id"))
					->joinLeft(array("ele_gr_alloc"=>"electives_grade_allocation"),"ele_gr_alloc.elective_component_id=electives_eval_items.el_eci_id",array("elective_component_id"))
					->where("$this->_name.status != 2")
					->where("ele_gr_alloc.elective_component_id IS NULL")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("electives_eval_items.course_type=?",$course_type)
					->where("electives_eval_items.term_id=?",$term_id)
                    ->where("electives_eval_items.elective_id
					=?",$course_id);
				
         $result = $this->getAdapter()
					->fetchAll($select);
					
				$data = array();
					foreach($result as $k => $val)
					{

						$data[$val['el_eci_id']] = $val['component_name'];
					}	
				
					return $data;
							
	}
	public function getElectivesAddComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id){
		
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("electives_eval_items"=>"electives_evaluation_components_items"),"electives_eval_items.el_ec_id=$this->_name.el_ec_id",array("term_id","elective_id","course_type","component_name","el_eci_id"))
					->joinLeft(array("electives_comp_grade"=>"electives_evaluation_components_grade_master"),"electives_eval_items.el_eci_id=electives_comp_grade.elective_component_id",array("elective_component_id"))
					->where("$this->_name.status != 2")
					->where("electives_comp_grade.elective_component_id IS NULL")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("electives_eval_items.course_type=?",$course_type)
					->where("electives_eval_items.term_id=?",$term_id)
                    ->where("electives_eval_items.elective_id
					=?",$course_id);
					//echo $select; die;
				
         $result = $this->getAdapter()
					->fetchAll($select);
					
				$data = array();
					foreach($result as $k => $val)
					{

						$data[$val['el_eci_id']] = $val['component_name'];
					}	
				
					return $data;
							
	}
	public function getAllCoursesComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id){
		$select = $this->_db->select()
			 ->from($this->_name)
			 ->joinLeft(array("cr_component_items"=>"courses_evaluation_components_items"),"cr_component_items.el_ec_id=$this->_name.el_ec_id",array("cr_eci_id","term_id","course_id","course_type","component_name","weightage","remaining_weightage"))
			 ->where("$this->_name.status != 2")
			 ->where("$this->_name.academic_year_id=?",$academic_year_id)
			 ->where("$this->_name.department_id=?",$department_id)
			 ->where("$this->_name.employee_id=?",$employee_id)
			 ->where("cr_component_items.term_id=?",$term_id)
			 ->where("cr_component_items.course_id=?",$course_id);
			
			 $result = $this->getAdapter()
					->fetchAll($select);
					
			return $result;		
		
	}
	
	public function getAllElectivesComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id){
		$select = $this->_db->select()
			 ->from($this->_name)
			 ->joinLeft(array("el_component_items"=>"electives_evaluation_components_items"),"el_component_items.el_ec_id=$this->_name.el_ec_id",array("el_eci_id","term_id","elective_id","course_type","component_name","weightage","remaining_weightage"))
			 ->where("$this->_name.status != 2")
			 ->where("$this->_name.academic_year_id=?",$academic_year_id)
			 ->where("$this->_name.department_id=?",$department_id)
			 ->where("$this->_name.employee_id=?",$employee_id)
			 ->where("el_component_items.term_id=?",$term_id)
			 ->where("el_component_items.elective_id=?",$course_id);
			
			 $result = $this->getAdapter()
					->fetchAll($select);
					
			return $result;		
		
	}	
	
	public function getEEComponentCount($academic_year_id,$department_id,$employee_id){
			
			$select = $this->_db->select()
					->from($this->_name)
					//->where("$this->_name.employee_id IS NULL")
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("$this->_name.status != 2");
			//echo $select;die;		
			$result = $this->getAdapter()
					       ->fetchRow($select);
			//print_r($result);die;		
		    return $result;
			
	}
	public function getCourseComponentWeightage($academic_year_id,$department_id,$employee_id,$term_id,$course_id,$component_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array('courses_evaluation_items'=>'courses_evaluation_components_items'),"courses_evaluation_items.el_ec_id=$this->_name.el_ec_id",array("cr_eci_id","term_id","course_id","weightage"))
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("courses_evaluation_items.term_id=?",$term_id)
					->where("courses_evaluation_items.course_id=?",$course_id)
					->where("courses_evaluation_items.cr_eci_id=?",$component_id)
					->where("$this->_name.status != 2");
				$result = $this->getAdapter()
                             ->fetchRow($select);  
			return $result;				 
							 
					
		
	}
	public function getElectiveComponentWeightage($academic_year_id,$department_id,$employee_id,$term_id,$course_id,$component_id){
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array('electives_evaluation_items'=>'electives_evaluation_components_items'),"electives_evaluation_items.el_ec_id=$this->_name.el_ec_id",array("el_eci_id","term_id","elective_id","weightage"))
					->where("$this->_name.academic_year_id=?",$academic_year_id)
					->where("$this->_name.department_id=?",$department_id)
					->where("$this->_name.employee_id=?",$employee_id)
					->where("electives_evaluation_items.term_id=?",$term_id)
					->where("electives_evaluation_items.elective_id=?",$course_id)
					->where("electives_evaluation_items.el_eci_id=?",$component_id)
					->where("$this->_name.status != 2");
				$result = $this->getAdapter()
                             ->fetchRow($select);  
			return $result;				 
							 
					
		
	}
	public function getCoursesComponentsView($id)
	{
		$select = $this->_db->select()
					->from($this->_name)
					->joinLeft(array("course_items"=>"courses_evaluation_components_items"),"course_items.el_ec_id=$this->_name.el_ec_id",array("term_id","course_id","GROUP_CONCAT(component_name) as name","GROUP_CONCAT(weightage) as weighs","GROUP_CONCAT(remaining_weightage) as remain_weighs"))
					->joinLeft(array("term"=>"term_master"),"term.term_id=course_items.term_id",array("term_name"))
					->joinLeft(array("course"=>"course_master"),"course.course_id=course_items.course_id",array("course_name"))
					->where("$this->_name.el_ec_id=?",$id)
					->group("course_items.term_id")
					->group("course_items.course_id")
					->where("$this->_name.status != 2");
					
			$result = $this->getAdapter()
			          ->fetchAll($select);
					  
					  
			return $result;		  
			
					
		
		
	}
		
	public function getElectivesComponentsView($id)
    {
			$select = $this->_db->select()
			          ->from($this->_name)
					  ->joinLeft(array("elective_items"=>"electives_evaluation_components_items"),"elective_items.el_ec_id=$this->_name.el_ec_id",array("term_id","elective_id","GROUP_CONCAT(component_name) as ele_name","GROUP_CONCAT(weightage) as ele_weighs","GROUP_CONCAT(remaining_weightage) as ele_remain_weighs"))
					  ->joinLeft(array("term"=>"term_master"),"term.term_id=elective_items.term_id",array("term_name"))
					  ->joinLeft(array("course"=>"course_master"),"course.course_id=elective_items.elective_id",array("course_name"))
					  ->where("$this->_name.el_ec_id=?",$id)
					  ->group("elective_items.term_id")
					  ->group("elective_items.elective_id")
				->where("$this->_name.status != 2");
				
			$result = $this->getAdapter()
			          ->fetchAll($select);
					  
					  
			return $result;	

    }	
	
}
?>