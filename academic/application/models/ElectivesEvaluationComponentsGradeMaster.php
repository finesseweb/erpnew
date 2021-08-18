<?php
/**
 * Application_Model_ErpItemsMaster
 *
 * @Framework Zend Framework
 * @Powered By TIS
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2014 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 */
class Application_Model_ElectivesEvaluationComponentsGradeMaster extends Zend_Db_Table_Abstract {

    protected $_name = 'electives_evaluation_components_grade_master';
    protected $_id = 'elective_component_grade_id';
    /**
     * Set Primary Key Id as a Parameter 
     *
     * @param string $item_id
     * @return single dimention array
     */	
   public function getRecord($elective_component_grade_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_id=?", $elective_component_grade_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    /**
     * Retrieve all Records
     *
     * @return Array
     */
    public function getRecords() {      
        $select = $this->_db->select()
                       ->from($this->_name)
					   ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array("from_date","to_date"))
					    ->where("$this->_name.status !=?", 2);
						
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getGradeValue($academic_year_id,$department_id,$employee_id,$term_id,$course_id,$component_id,$letter_grade){
	   $select = $this->_db->select()
	             ->from($this->_name)
		     ->joinLeft(array("el_eval_comp_grade_master_items"=>"electives_evaluation_components_grade_master_items"),"el_eval_comp_grade_master_items.elective_component_grade_id=$this->_name.elective_component_grade_id",array("letter_grade","number_grade"))
		     ->where("$this->_name.status!=2")
		     ->where("$this->_name.academic_id=?",$academic_year_id)
		     ->where("$this->_name.department_id=?",$department_id)
		     ->where("$this->_name.employee_id=?",$employee_id)
		     ->where("$this->_name.term_id=?",$term_id)
		     ->where("$this->_name.course_id=?",$course_id)
		     ->where("$this->_name.component_id=?",$component_id)
		     ->where("el_eval_comp_grade_master_items.letter_grade=?",$letter_grade);
		     
       $result = $this->getAdapter()
		->fetchRow($select);
		
	return $result;	
		
		     
   } 
  public function getElectivesGradeValue($academic_year_id,$department_id,$employee_id,$term_id,$course_id,$component_id,$letter_grade){
	   $select = $this->_db->select()
	             ->from($this->_name)
		     ->joinLeft(array("el_eval_comp_grade_master_items"=>"electives_evaluation_components_grade_master_items"),"el_eval_comp_grade_master_items.elective_component_grade_id=$this->_name.elective_component_grade_id",array("letter_grade","number_grade"))
		     ->where("$this->_name.status!=2")
		     ->where("$this->_name.academic_id=?",$academic_year_id)
		     ->where("$this->_name.department_id=?",$department_id)
		     ->where("$this->_name.employee_id=?",$employee_id)
		     ->where("$this->_name.term_id=?",$term_id)
		     ->where("$this->_name.course_id=?",$course_id)
		     ->where("$this->_name.elective_component_id=?",$component_id)
		     ->where("el_eval_comp_grade_master_items.letter_grade=?",$letter_grade);
		     //echo $select; die;
       $result = $this->getAdapter()
		->fetchRow($select);
		
	return $result;	
		
		     
   } 
  
     
    
}