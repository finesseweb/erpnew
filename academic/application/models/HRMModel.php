<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_HRMModel extends Application_Model_GlobalDbModel
{
	    protected $_name = 'fa_kv_empl_info';
          
            
    function __construct() {
        parent::__construct('erp');
    }
	

    
   
	 public function getEmployeeIds() {
      $select = $this->_db->select()
				->from($this->_name,array('empl_id','empl_firstname','status'))	->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
				->where("empl_job.department=?",1)
				->where("$this->_name.status=?",1)
				->orwhere("$this->_name.status=?",2);
		$result = $this->getAdapter()
			->fetchAll($select);
			
		$data = array();
		
		foreach($result as $val){
			
			$data[$val['empl_id']] = $val['empl_firstname'];
		}
			return $data;
    }
    public function getOrgFacultyIds() {
      $select = $this->_db->select()
				->from($this->_name,array('empl_id','empl_firstname','status'))	->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
				->where("empl_job.department=?",1)
                                ->where("empl_job.empl_type !=?",5)
				->where("$this->_name.status=?",1)
                                ->where("$this->_name.empl_id like (?)",'EMP-F%')
				->orwhere("$this->_name.status=?",2);
		$result = $this->getAdapter()
			->fetchAll($select);
			
		$data = array();
		
		foreach($result as $val){
			
			$data[$val['empl_id']] = $val['empl_firstname'];
		}
			return $data;
    }
public function getVisitingEmployeeIds(){
		
		$select = $this->_db->select()
				->from($this->_name,array('empl_id','empl_firstname','status'))
				->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig","empl_type"))
				->where("empl_job.department=?",1)
				->where("empl_job.empl_type=?",5)
				->where("$this->_name.status=?",1)
				->orwhere("$this->_name.status=?",2);
				
				$result = $this->getAdapter()
			->fetchAll($select);
			
		$data = array();
		
		foreach($result as $val){
			
			$data[$val['empl_id']] = $val['empl_firstname'];
		}
			return $data;
	}
	
	 public function getDepartmentsDropdown() {

		$select = "SELECT id,description FROM fa_kv_departments";
		$result = $this->getAdapter()
					->fetchAll($select);
			
			$data = array();
		//	print_r($result); die;
		foreach($result as $val){
			$data[$val['id']] = $val['description'];
		}
		return $data;			
						
	}
	
	public function getEmployeeData($empl_id){
		if($empl_id){
		$select = $this->_db->select()
					->from($this->_name)
					->where("$this->_name.empl_id=?",$empl_id);
					
		$result = $this->getAdapter()
				->fetchRow($select);	
                }
 else {
     $result = array();
 }
		return $result;			
		
	}
        
        public function getAllEmployee($empl_id){
		
		$select = $this->_db->select()
					->from($this->_name,array("empl_id, concat(empl_firstname,' ',empl_lastname) as name"))
					->where('empl_id LIKE ?', $empl_id.'%');
				//echo $select; exit;
		$result = $this->getAdapter()
				->fetchAll($select);	

		return $result;			
		
	}
        
        public function getWeeklyOff(){
            $select = $this->_db->select()
					->from('fa_kv_empl_option');
		$result = $this->getAdapter()
				->fetchAll($select);	

		return $result;	
            
        }
        
        
        
          public function getAllEmployee1($empl_id){
		
		$select = $this->_db->select()
					->from($this->_name,array("empl_id, concat(empl_firstname,' ',empl_lastname) as name"))
					->where('empl_id =?', $empl_id);
				
		$result = $this->getAdapter()
				->fetchAll($select);	

		return $result;			
		
	}
        
        
	public function getDesiggroupDropDownList($department)
	{
		$select = "SELECT id,name FROM fa_kv_desig_group WHERE id = '".$department."'";
		$result = $this->getAdapter()
					->fetchAll($select);
			
			$data = array();
			
		foreach($result as $val){
			$data[$val['id']] = $val['name'];
		}
		return $data;			
						
	}	
	public function getDesignationDropDownList($desig_group){
		
		$select = "SELECT id,name FROM fa_designation_master WHERE  desig_group_id = '".$desig_group."'";
		$result = $this->getAdapter()
		          ->fetchAll($select);
		$data = array();
		foreach($result as $val){
			$data[$val['id']] = $val['name'];
			
		}
		
		return $data;
		
	}
    public function getDepartments()
	{
		$select = "SELECT id, description FROM fa_kv_departments  WHERE inactive = 0";
		
		$result = $this->getAdapter()
					->fetchAll($select);
		$data = array();
		
		foreach($result as $val){
			$data[$val['id']] = $val['description'];
			
		}	
		return $data;
		
	}
	

	
	public function getEmployees($department_id,$desig_group_id,$designation_id)
	{
		
		$select  = $this->_db->select()
					->from($this->_name,array("empl_id","empl_firstname"))
					->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
					->where("empl_job.department=?",$department_id)
					->where("empl_job.desig_group=?",$desig_group_id)
					->where("empl_job.desig=?",$designation_id);
					
		$result = $this->getAdapter()
					->fetchAll($select);
					
					
			$data = array();

			foreach($result as $val)
			{
				$data[$val['empl_id']] = $val['empl_firstname'];


            }	
      return $data;			
	}	
		
		
	public function getEmployee($department_id)
	{
		
		$select  = $this->_db->select()
					->from($this->_name,array("empl_id","empl_firstname"))
					->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
					->where("empl_job.department=?",$department_id);
					
					
		$result = $this->getAdapter()
					->fetchAll($select);
					
					
			$data = array();

			foreach($result as $val)
			{
				$data[$val['empl_id']] = $val['empl_firstname'];


            }
	
      return $data;			
	}	
	public function getUserDetail($username) {
            $select = $this->_db->select()
                    ->from('fa_users')
                    ->joinLeft(array("security"=>"fa_security_roles"),"security.id=fa_users.role_id")
                    ->where("fa_users.user_id=?", $username)
                    ->where("fa_users.inactive=0");

            $result = $this->getAdapter()
                    ->fetchRow($select);

            return $result;
        }
public function getUserDetail1($username) {
            $select = $this->_db->select()
                    ->from('fa_users')
                    ->where("fa_users.email=?", $username)
                    ->where("fa_users.inactive=0");

            $result = $this->getAdapter()
                    ->fetchRow($select);

            return $result;
        }
}
?>