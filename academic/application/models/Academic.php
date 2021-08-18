<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Academic extends Zend_Db_Table_Abstract
{
    public $_name = 'academic_master';
    protected $_id = 'academic_year_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
       // echo $select; exit;
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
    
  
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)                      				   
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
    
    public function getRecordsByDepartment($department){
        $select=$this->_db->select('academic_year_id')
                      ->from($this->_name)                      				   
		      ->where("$this->_name.status !=?", 2)
		      ->where("$this->_name.department =?", $department);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
        
    }
    
    
    
    	public function getBatchDetails($id)
         {       
            
        $select=$this->_db->select()
                      ->from($this->_name,array('academic_year_id','from_date','to_date','short_code'))  
                      ->where("$this->_name.status !=?", 2)
                      ->where("$this->_name.$this->_id=?", $id);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
		public function getDropDownList(){
        $select = $this->_db->select()
		->from($this->_name, array('academic_year_id','from_date','to_date','short_code'))				
				->where("$this->_name.status!=?",2)
                ->order('academic_year_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[$val['academic_year_id']] = $val['short_code'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
    
    
    public function getDropDownList1(){
        $select = $this->_db->select()
		->from($this->_name, array('academic_year_id','short_code'))				
				->where("$this->_name.status!=?",2)
                ->order('academic_year_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[$val['academic_year_id']] = $val['short_code'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
    
     public function getDropDownList2($batch_id){
        $select = $this->_db->select()
		->from($this->_name, array('academic_year_id','short_code'))				
				->where("$this->_name.status!=?",2)
                                ->where("academic_year_id =?", $batch_id)
                ->order('academic_year_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[$val['academic_year_id']] = $val['short_code'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
    
    
    
    
	public function getValidateAcademic($academic_year_id) {

        $select = $this->_db->select()
                ->from($this->_name,array('academic_year_id','from_date','to_date'))	
				//->where("$this->_name.term_name =?", $term_id)
				->where("$this->_name.status!=?", 2)
				->order('academic_year_id  ASC');
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		
    }
	
	
	public function getIncrementID(){
	   $select = "SELECT `academic_master`.`academic_year_id` FROM `academic_master` WHERE (status !=2) ORDER BY `academic_year_id` DESC";
	   
		$result = $this->getAdapter()->fetchRow($select);
		
		//if( !empty($result['waybill_no']) )
		//{
			$data = $result['academic_year_id']+1;
		//	print_r($data);die;
			 if (strlen($data) == 1) {
                $data = "0" .  $data . @(SHORTCODE_PREFIX);
            } else
            if (strlen($data) == 2) {
                $data = "0" .  $data . @(SHORTCODE_PREFIX);
            } else
            if (strlen($data) == 3) {
                $data = "0" .  $data . @(SHORTCODE_PREFIX);
                $data = "" . $data . @(SHORTCODE_PREFIX);
            }
			
		return $data;
	}
	
	//Drop Down For Short Code
	
	public function getDropDownListShortCode()
	{
        $select = $this->_db->select()
		    ->from($this->_name,array('academic_year_id', 'short_code'))
			->where("$this->_name.status!=?",2)
			->order('academic_year_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
		//echo'<pre>';print_r($result);die;
      $data = array();
        foreach($result as $k=>$vals) {
			
			$data[$vals['academic_year_id']] = $vals['short_code'];
			
        }
		
        return $data; 
    }
	
/*	public function getAcademic($short_id)
	{
		
		$select  = $this->_db->select()
					->from($this->_name,array('academic_year_id','from_date','to_date'))
					//->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
					->where("$this->_name.academic_year_id=?",$short_id);
				//print_r($select);die;	
					
		$result = $this->getAdapter()
					->fetchAll($select);
					
			//echo '<pre>';print_r($result);die;		
			$data = array();

			foreach($result as $val)
			{
				$data[$val['academic_year_id']] = $val['from_date'].' '.'-'.' '.$val['to_date'];


            }
	
      return $data;			
	}	
		
	public function getBatchCodeRecord($academic_year_id) {

        $select = $this->_db->select()
                ->from($this->_name,array('short_code'))	
				->where("$this->_name.academic_year_id =?", $academic_year_id)
				->where("$this->_name.status!=?", 2);
				//->order('academic_year_id  ASC');
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		
    }	  */
    
    
    
    public function getAcademicOnDept($dept_id){
        $select  = $this->_db->select()
					->from($this->_name,array('academic_year_id','from_date','to_date','short_code as batch_code'))
					//->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
					->where("$this->_name.department=?",$dept_id);
				//echo $select;die;	
					
		$result = $this->getAdapter()
					->fetchAll($select);
      return $result;
        
    }
    

	public function getAcademic($short_id)
	{
		
		$select  = $this->_db->select()
					->from($this->_name,array('academic_year_id','from_date','to_date','batch_code'))
					//->joinLeft(array("empl_job"=>"fa_kv_empl_job"),"empl_job.empl_id=$this->_name.empl_id",array("department","desig_group","desig"))
					->where("$this->_name.academic_year_id=?",$short_id);
				//echo $select;die;	
					
		$result = $this->getAdapter()
					->fetchRow($select);
      return $result;			
	}
        //==========================================================================26-04-19 satyam =====================
          public function getlastdateRecord($academic_id) {
        $select = "SELECT start_date, end_date FROM program_design_master  Where  academic_year_id='$academic_id' and status !=2  order by pd_id DESC LIMIT 1";
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;
        return $result;
    }
        //===============================================================================================================
		
	public function getBatchCodeRecord($academic_year_id) {
            if($academic_year_id){
        $select = $this->_db->select()
                ->from($this->_name,array('short_code','batch_code'))	
				->where("$this->_name.academic_year_id =?", $academic_year_id)
				->where("$this->_name.status!=?", 2);
				//->order('academic_year_id  ASC');
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
            }
 else {
     $result = array();
 }
		return $result;
		
    }
// ramesh for enabled between dates in year 
public function getyearRecord($academic_id)
{
	$select = $this->_db->select()
                ->from($this->_name,array('from_date','to_date'))	
				->where("$this->_name.academic_year_id =?", $academic_id)
				->where("$this->_name.status!=?", 2);
		$result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
	
}	
    public function getAcademicDesignOrderByDate($academic_year_id){
        $select = "SELECT id, c_type, term_name, tot_no_of_credits FROM ( SELECT term_id as id, 'term' as c_type, term_name,	tot_no_of_credits, start_date_type, end_date_type FROM term_master t WHERE status != 2 AND academic_year_id = $academic_year_id UNION SELECT ec.elc_id as id, 'el' as c_type, ec.elc_name, cr.credit_value as tot_no_of_credits, start_date_type, end_date_type FROM experiential_learning_master e JOIN experiential_learning_components_master ec ON ec.elc_id = e.elc_id JOIN credit_master cr ON cr.credit_id = e.credit_id WHERE e.status != 2 AND e.academic_year_id = $academic_year_id AND ec.status != 2 ) AS p ORDER BY p.start_date_type;";
	   
		$result = $this->getAdapter()->fetchAll($select);
                return $result;
    }

}
?>