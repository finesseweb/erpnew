<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ProgramMaster extends Zend_Db_Table_Abstract
{
    public $_name = 'program_master';
    protected $_id = 'pm_id';
  
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
						->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.short_id",array("from_date","to_date","short_code"))	//->joinleft(array("academic1"=>"academic_master"),"academic1.academic_year_id=$this->_name.short_id",array("short_code"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	

	//View purpose
	
	public function getProgram($academic_id) {

        $select = $this->_db->select()
                ->from($this->_name)	
				->where("$this->_name.academic_year_id =?", $academic_id)
				->where("$this->_name.status!=?", 2);
		
        $result = $this->getAdapter()
                ->fetchAll($select);
		
		return $result;
		
    }
	
	//same name 
	
	public function getProgramMaster($pd) {

        $select = $this->_db->select()
                ->from($this->_name,array("pm_name","pm_id"))	
				->where("$this->_name.pm_name =?", $pd)
				->where("$this->_name.status!=?", 2);
		
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		
    }
    
    
    
    

         public function getlastdateRecord($academic_id) {
        $select = "SELECT start_date, end_date FROM program_design_master  Where  academic_year_id='$academic_id' and status !=2  order by pd_id DESC LIMIT 1";
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;
        return $result;
    }
    
    
	
	
	//Drop Down For Program Name
	
	public function getDropDownListSessionName()
	{
        $select = $this->_db->select()
		    ->from($this->_name,array('pm_id', 'pm_name'))
			->where("$this->_name.status!=?",2)
			->order('pm_id ASC');
        $result = $this->getAdapter()->fetchAll($select);
		//echo'<pre>';print_r($result);die;
      $data = array();
        foreach($result as $k=>$vals) {
			
			$data[$vals['pm_id']] = $vals['pm_name'];
			
        }
		
        return $data; 
    }
	
	//Based on short code need to get Program Name 
	
	public function getProgramName($short_id)
	{
		
		$select  = $this->_db->select()
					->from($this->_name,array('pm_id','pm_name'))
					//->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
					->where("$this->_name.short_id=?",$short_id);
				//print_r($select);die;	
					
		$result = $this->getAdapter()
					->fetchAll($select);
					
			//echo '<pre>';print_r($result);die;		
			$data = array();

			foreach($result as $val)
			{
				$data[$val['pm_id']] = $val['pm_name'];


            }
	
      return $data;			
	}	
		
	
	public function getProgramNameDisplay($short_id)
	{
		
		$select  = $this->_db->select()
					->from($this->_name,array('pm_id','pm_name'))
					//->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
					->where("$this->_name.short_id=?",$short_id);
				//print_r($select);die;	
					
		$result = $this->getAdapter()
					->fetchAll($select);
					
			//echo '<pre>';print_r($result);die;		
			$data = array();

			foreach($result as $val)
			{
				$data[$val['pm_id']] = $val['pm_name'];


            }
	
      return $data;			
	}	
		
	
	
	
	
}
?>