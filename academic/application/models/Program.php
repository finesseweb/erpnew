<?php
/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Program extends Zend_Db_Table_Abstract
{
    public $_name = 'master_program';
    protected $_id = 'program_id';
  
    //get details by record for edit
	public function getRecord($program_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_id=?",$program_id);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    //get details for show
    public function getRecords()
    {
        $select=$this->_db->select()
					->from($this->_name) 
					//->joinLeft(array("branch" => "master_branch"), "branch.branch_id=$this->_name.branch_id", array("branch_name"))
					->where("$this->_name.status!=2")
					->order('program_id  DESC');
        $result=$this->getAdapter()
                      ->fetchAll($select);
        return $result;
    }
	public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('program_id', 'program_name'))				
				->where("$this->_name.status!=2")
                ->order('program_id', 'ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['program_id']] = $val['program_name'];
        }
        return $data;
    }
	/*
    public function getCompanyRecords()
    {
        $select=$this->_db->select()
                      ->from($this->_name,array('company_name','company_address1','caompany_vat_no','company_cst_no'));                        
        $result=$this->getAdapter()
                      ->fetchRow($select);
        return $result;
    } */
	
	/*
	public function getCourseRecord()
    {        
        $select=$this->_db->select()
                      ->from("course",array('course_id','course'))
                      ->order('course_id', 'ASC');
        $result=$this->getAdapter()
                      ->fetchAll($select);
       $data = array();
        foreach ($result as $val) {
            $data[$val['course_id']] = $val['course'];
        }
        return $data;

    } */
}