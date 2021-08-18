<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_CoursesEvaluationComponentsItems extends Zend_Db_Table_Abstract
{
    public $_name = 'courses_evaluation_components_items';
    protected $_id = 'cr_eci_id';
  
    //get details by record for edit
	public function getRecord($el_ec_id)  //company_id is main Company Id
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.el_ec_id=?",$el_ec_id);
        $result=$this->getAdapter()
                      ->fetchRow($select);  
//print_r($result); die;					  
        return $result;
    }
	
	
	public function getRecords($el_ec_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.el_ec_id=?",$el_ec_id);
					 
        $result=$this->getAdapter()
                      ->fetchAll($select);   
		//print_r($result);die;	  
        return $result;
    }
	
	//View purpose
	
	
	public function trashItems($el_ec_id) {

        $this->_db->delete($this->_name, "el_ec_id=$el_ec_id");

    }
	public function  getWeightage($component_id){
		$select = $this->_db->select()
				->from($this->_name)
				->where("$this->_name.cr_eci_id=?",$component_id);
		$result = $this->getAdapter()
					->fetchRow($select);
		return $result;			
		
	}	
	
	
	
}
?>