<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_jobAnnouncement extends Zend_Db_Table_Abstract
{
    
    public $_name = 'job_announcement'; 
    protected $_id = 'id';
  
	//Get all records
      public function getRecord($id)
    {                                   
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)          
                      ->where("$this->_name.status !=?", Inactive);
        $result=$this->getAdapter()
                      ->fetchRow($select);  
//print_r($result); die;            
        return $result;
    }
 
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->order("$this->_name.$this->_id DESC")                				   
					            ->where("$this->_name.status !=?", Inactive);
					 
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
 

public function getRecords_selection_process()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->order("$this->_name.$this->_id DESC");                          
                       
           
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    public function getRecords_selection_process_user_and_faculty()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->order("$this->_name.$this->_id DESC")                          
                       ->where("$this->-_name.status !=?",Inactive);
           
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }



       public function getValidateRegistrationNo($id) {
        $select = $this->_db->select()
                ->from($this->_name,array("job_announcement_id")) 
        ->where("$this->_name.id =?", $id)
        ->where("$this->_name.status!=?", Inactive);
  $result = $this->getAdapter()
                ->fetchRow($select);
    return $result;
 } 
   public function getRecords_to_jobannouncement_id()
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('job_announcement_id'));  
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
  public function getMaxRegistration(){

       $select=$this->_db->select()
                      ->from($this->_name,array('max(job_announcement_id)as max_id'));
        $result=$this->getAdapter()
                      ->fetchRow($select);  
/*echo $select; die;            
*/        return $result;

  }

 } 