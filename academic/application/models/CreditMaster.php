<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_CreditMaster extends Zend_Db_Table_Abstract
{
    public $_name = 'credit_master';
    protected $_id = 'credit_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_id=?", $id)				   
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
				
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_id DESC")
					  ->group("$this->_id");
					//  ->group("$this->_id");
        $result=$this->getAdapter()
                      ->fetchAll($select); 
					//print_r($result) ;die; 
        return $result;
    }
	

	public function getValidateCreditValue($credit_id) {

    /*    $select = $this->_db->select()
                ->from($this->_name,array("credit_value","credit_id"))	
				->where("$this->_name.credit_value =?", $credit_id)
				->where("$this->_name.status!=?", 2);
		//echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		
*/
    }
	
	
			
public function getDropDownList()
	{
        $select = $this->_db->select()
		    ->from($this->_name,array('credit_id', 'credit_value'))
			->where("$this->_name.status!=?",2)
			->order('credit_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
		//echo'<pre>';print_r($result);die;
      $data = array();
        foreach($result as $k=>$vals) {
			
			$data[$vals['credit_id']] = $vals['credit_value'];
			
        }
		
        return $data; 
    }
	
	
	public function getCourseCreditDropDownList()
	{
		$select = $this->_db->select()
				->from($this->_name,array('credit_id','credit_value'))
				->where("$this->_name.status !=?",2)
				->where("$this->_name.credit_type=?",1)
				->order('credit_id ASC');
		$result = $this->getAdapter()->fetchAll($select);
         $data = array();
		foreach($result as $k => $val){
			
			$data[$val['credit_id']] = $val['credit_value'];
		}
		return $data;		
		
	}
	public function getCourseCreditById($id)
	{
		$select = $this->_db->select()
				->from($this->_name,array('credit_id','credit_value'))
				->where("$this->_name.status !=?",2)
				->where("$this->_name.credit_id =?",$id)
				->where("$this->_name.credit_type=?",2)
				->order('credit_id ASC');
		$result = $this->getAdapter()->fetchAll($select);
                return $result;
		
	}
	public function getExperientialCreditDropDownList()
	{
		$select = $this->_db->select()
				->from($this->_name,array('credit_id','credit_value'))
				->where("$this->_name.status !=?",2)
				->where("$this->_name.credit_type=?",2)
				->order('credit_id ASC');
		$result = $this->getAdapter()->fetchAll($select);
         $data = array();
		foreach($result as $k => $val){
			
			$data[$val['credit_id']] = $val['credit_value'];
		}
		return $data;		
		
	}
        
        
	public function getExperientialCreditNameDropDownList()
	{
		$select = $this->_db->select()
				->from($this->_name,array('credit_id','credit_name'))
				->where("$this->_name.status !=?",2)
				->where("$this->_name.credit_type=?",2)
				->order('credit_name DESC');
		$result = $this->getAdapter()->fetchAll($select);
          $data = array();
		foreach($result as $k => $val){
			
			$data[$val['credit_id']] = $val['credit_name'];
		}
		return $data;	
		
	}
	
	
}
?>