<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ExperientialEvaluationComponentsItems extends Zend_Db_Table_Abstract
{
    public $_name = 'experiential_evaluation_components_items';
    protected $_id = 'eci_id';
  
    //get details by record for edit
	public function getRecord($ec_id)  //company_id is main Company Id
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.ec_id=?",$ec_id);
        $result=$this->getAdapter()
                      ->fetchRow($select);  
//print_r($result); die;					  
        return $result;
    }
	
	
	public function getRecords($ec_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.ec_id=?",$ec_id);
					 
        $result=$this->getAdapter()
                      ->fetchAll($select);   
		//print_r($result);die;	  
        return $result;
    }
	
	//View purpose
	
	
	public function trashItems($ec_id) {

        $this->_db->delete($this->_name, "ec_id=$ec_id");

    }
	/* public function  getWeightage($component_id){
		$select = $this->_db->select()
				->from($this->_name)
				->where("$this->_name.eci_id=?",$component_id);
		$result = $this->getAdapter()
					->fetchRow($select);
		return $result;			
		
	}	*/
	
public function  getWeightage($academic_year_id,$employee_id,$department_id,$course_id){
		$select = $this->_db->select()
				->from($this->_name)
				->joinleft(array("component"=>"evaluation_components_master"),"component.ec_id=$this->_name.ec_id",array("academic_year_id","employee_id","department_id"))
				->where("component.academic_year_id=?",$academic_year_id)
				->where("component.department_id=?",$department_id)
				->where("component.employee_id=?",$employee_id)				
				->where("$this->_name.course_id=?",$course_id);
				//echo $select;die;
		$result = $this->getAdapter()
					->fetchAll($select);
		return $result;			
		
	}
public function  getWeightages($academic_year_id,$term_id,$course_id,$component_id){
		$select = $this->_db->select()
				->from($this->_name)
				->joinleft(array("component"=>"evaluation_components_master"),"component.ec_id=$this->_name.ec_id",array("academic_year_id"))
				->where("component.academic_year_id=?",$academic_year_id)
			//->where("component.department_id=?",$department_id)
				//->where("component.employee_id=?",$employee_id)
				->where("$this->_name.term_id=?",$term_id)
				->where("$this->_name.course_id=?",$course_id)
				->where("$this->_name.eci_id=?",$component_id);
				//echo $select;die;
		$result = $this->getAdapter()
					->fetchRow($select);
		return $result;			
		
	}
	
	
	public function getAlltermsandcourses($ec_id){
		$select = $this->_db->select()
		           ->from($this->_name,array("GROUP_CONCAT(evaluation_components_items_master.course_id) AS courses","GROUP_CONCAT(evaluation_components_items_master.term_id) AS terms_ids"))
				   ->where("$this->_name.ec_id=?",$ec_id)
				   ->group("$this->_name.course_id")
				   ->order("$this->_name.term_id");
				   $result = $this->getAdapter()
				             ->fetchAll($select);
							 
							 
				return $result;			 
				   
		
	}
	
}
?>