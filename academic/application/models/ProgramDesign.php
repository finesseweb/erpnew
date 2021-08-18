<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ProgramDesign extends Zend_Db_Table_Abstract
{
    public $_name = 'program_design_master';
    protected $_id = 'pd_id';
  
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
						->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date","short_code"))
						->joinleft(array("program"=>"program_master"),"program.pm_id=$this->_name.pd_name",array("pm_name"))
						//->joinleft(array("academic1"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("short_code"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	

	//View purpose
	
	public function getProgram($short_id,$pd_id) {

        $select = $this->_db->select()
                ->from($this->_name)	
				->where("$this->_name.short_code =?", $short_id)
				->where("$this->_name.pd_name =?", $pd_id)
				->where("$this->_name.status!=?", 2);
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
		
		return $result;
		
    }
	
	//same name 
	
	public function getProgramDesign($pd) {

        $select = $this->_db->select()
                ->from($this->_name,array("pd_name","pd_id"))	
				->where("$this->_name.pd_name =?", $pd)
				->where("$this->_name.status!=?", 2);
		
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		
    }
	
}
?>