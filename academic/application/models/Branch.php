<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Branch extends Zend_Db_Table_Abstract
{
    public $_name = 'master_branch';
    protected $_id = 'branch_id';
	protected $_auth_id = '';
	protected $_role_id = '';
	
	//Get the common data
	/*public function init() {      
        $this->_act = new Application_Model_Adminactions();	
		$this->_auth_id = $this->_act->auth_id();
		$this->_role_id = $this->_act->role_id();		
    }
	*/
    //get details by record for edit
	public function getRecord($branch_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_id=?",$branch_id);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    //get details for show
    public function getRecords()
    {
		//Get all records
		if($this->_role_id ==''){
			$select=$this->_db->select()
						->from($this->_name) 
						->joinLeft(array("main_branch" => "master_main_branch"), "main_branch.main_branch_id=$this->_name.main_branch_id", array("main_branch_name"))
						->where("$this->_name.status!=2")
						->order('branch_id  DESC');
		}else{
			$select=$this->_db->select()
						->from($this->_name) 
						->joinLeft(array("main_branch" => "master_main_branch"), "main_branch.main_branch_id=$this->_name.main_branch_id", array("main_branch_name"))
						->where("$this->_name.status!=2")
						->where("$this->_name.branch_id=?", $this->_auth_id)
						->order('branch_id  DESC');
		}
		
        $result=$this->getAdapter()
                      ->fetchAll($select);
        return $result;
    }
	public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_name'))				
				->where("$this->_name.status!=2")
                ->order('branch_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
        }
        return $data;
    }
	//dump data from branch
	public function getDropDownListDumpBranch() {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_name'))
				->joinLeft(array("fees" => "master_fees"), "fees.branch=$this->_name.branch_id", array("branch"))
				->where("fees.branch IS NOT NULL")
				->where("$this->_name.status!=2")
                ->order("$this->_name.branch_id", 'ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
        }
        return $data;
    }
	
	public function getDropDownListFee() {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_name'))
				->joinLeft(array("fees" => "master_fees"), "fees.branch=$this->_name.branch_id", array("branch"))
				->where("fees.branch IS NULL")
				->where("$this->_name.status!=2")
                ->order("$this->_name.branch_id", 'ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
        }
        return $data;
    }
	//fees edit
	public function feesEditDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_name'));
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
        }
        return $data;
    }
	
	public function getDropDownCode() {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_code'))				
				->where("$this->_name.status!=2")
                ->order('branch_id', 'ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_code'];
        }
        return $data;
    }
	//get branch code for admission
	public function getBranchCode($branch_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array("branch_code"))
                      ->where("$this->_id=?",$branch_id);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	
	//get Location for food calender
	public function getLocation($branch_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array("location_id"))
                      ->where("$this->_id=?",$branch_id);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	public function getLocations($branch)
    {       //echo $branch; die;
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinLeft(array("location" => "master_location"), "location.location_id=$this->_name.location_id", array("location_name"))
                      ->where("$this->_id=?",$branch);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	public function getBranchDropDownList($id="") {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_name'))				
				->where("$this->_name.status!=2")
				->where("$this->_name.branch_id =?", $id)
				
                ->order('branch_id', 'ASC');
				//echo $select;die;
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
			//print_r($data);die;
        }
        return $data;
    }
	//For Event Master and customer care tracker
		public function getDropDownListById($location_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_name'))	
->where("$this->_name.status !=2")
			
				->where("$this->_name.location_id =?", $location_id);
        $result = $this->getAdapter()->fetchAll($select);
		//print_r($result); die;
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
        }
        return $data;
    }
	public function DropDownForEvents() {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_name'))	
				->joinLeft(array("event" => "master_event"), "event.branch_id=$this->_name.branch_id",array("branch_id as branchID","event_name" ))
				->where("event.branch_id IS  NOT NULL")
				//->where("$this->_name.location_id =?", $location_id)
				//->where("foodtimetable.status !=0")	
				->where("$this->_name.status!=2");
               //echo  $select; die;
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'].' ('.$val['event_name'].')';
        }
        return $data;
    }
	public function getDropDownListByType($type) {
        $select = $this->_db->select()
                ->from($this->_name, array('branch_id', 'branch_name'))				
				->where("$this->_name.company =?", $type)
->where("$this->_name.status != 2");

        $result = $this->getAdapter()->fetchAll($select);
		//print_r($result); die;
        $data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
        }
        return $data;
    }
	public function getBranchDropDown($location_id)
    {
	//echo $subprogram_id; die;
        $select=$this->_db->select()
					->from($this->_name,array('branch_id', 'branch_name')) 
					->joinLeft(array("location" => "master_location"), "location.location_id = $this->_name.location_id", array("location_id","location_name"))
					->where("$this->_name.status!=2")					
					->where('FIND_IN_SET(location.location_id,(?))', "$location_id")
					->order("$this->_id DESC");
				//echo $select; die;
        $result=$this->getAdapter()
                      ->fetchAll($select);
		
		$data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
        }
        return $data;
    }
	public function getIntimationBranchDropDown($city)
    {
	//echo $subprogram_id; die;
        $select=$this->_db->select()
					->from($this->_name,array('branch_id', 'branch_name')) 
					->joinLeft(array("city" => "erp_cities"), "city.city_id = $this->_name.city_id", array("city_id","city_name"))
					->where("$this->_name.status!=2")					
					->where('FIND_IN_SET(city.city_id,(?))', "$city")
					->order("$this->_id DESC");
				//echo $select; die;
        $result=$this->getAdapter()
                      ->fetchAll($select);
		
		$data = array();
        foreach ($result as $val) {
            $data[$val['branch_id']] = $val['branch_name'];
        }
        return $data;
    }
}
  
