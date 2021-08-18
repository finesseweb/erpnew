<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_EmployeeAllocationItems extends Zend_Db_Table_Abstract
{
    public $_name = 'employee_allocation_items_master';
    protected $_id = 'ead_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id);				   
					  //->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name) 
						//->joinleft(array("academic"=>"course_type_master"),"academic.ct_id=$this->_name.ct_id",array("ct_name"))
						//->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date"))
					  //->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	public function getDropDownList(){
        $select = $this->_db->select()
     ->from($this->_name, array('elc_id','elc_name'))				
				->where("$this->_name.status!=?",2)
                ->order('elc_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['elc_id']] = $val['elc_name'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
	
	
	
	public function getItemsRecords($ea_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
					->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
					->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
                      ->where("$this->_name.ea_id=?", $ea_id);				   
					  //->where("$this->_name.status !=?", 2);
        //echo $select;exit;
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	
	//same name validation
	
	
	public function getcomponenetname($componentname) {

        $select = $this->_db->select()
                ->from($this->_name,array("elc_name","elc_id"))	
				->where("$this->_name.elc_name =?", $componentname)
				->where("$this->_name.status!=?", 2);
		
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		

    }
	
	
	public function getprogram($ccl_id) {

        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
				 ->joinleft(array("course"=>"course_master"),"course.course_id=$this->_name.course_id",array("course_name"))
				 ->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
				 ->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
				->where("$this->_name.academic_year_id =?", $ccl_id)
				->where("$this->_name.status!=?", 2);
		
        $result = $this->getAdapter()
                ->fetchAll($select);
		
		
				
		return $result;
		

    }
	
	public function trashItems($ea_id) {

        $this->_db->delete($this->_name, "ea_id=$ea_id");

    }
    public function trashItemsByEmpAllotmentId($ead_id) {

        $this->_db->delete($this->_name, "ead_id=$ead_id");

    }
	
	
}
?>