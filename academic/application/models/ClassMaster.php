<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ClassMaster extends Zend_Db_Table_Abstract
{
    public $_name = 'class_master';
    protected $_id = 'class_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)	;		   
					 // ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select); 
        
        return $result;
    }
	public function getRecordByTermID($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.term_id=?", $id)	;			   
					 // ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	public function getGroupedRecord(){
        $select=$this->_db->select()
                      ->from($this->_name,array('count(class_id) as total','term_id' ))
                     // ->where("$this->_name.'term_id'=?", $term_id)				   
					  ->where("$this->_name.status !=?", 2)
                                        ->group("$this->_name.term_id");
        $result=$this->getAdapter()
                      ->fetchAll($select);     
      
        return $result;
    }
    
    public function getRecordByTermIdAndBatch($term_id, $batch_id){
                $select=$this->_db->select()
                      ->from($this->_name,array('count(class_id) as total'))
                      ->where("$this->_name.term_id=?", $term_id)				   
                      ->where("$this->_name.academic_year_id=?", $batch_id)				   
                      ->where("$this->_name.status !=?", 2);
                     $result=$this->getAdapter()
                     ->fetchRow($select);     
      
        return $result['total'];
        
    }
    public function getClassHours($term_id, $batch_id){
                $select=$this->_db->select()
                      ->from($this->_name,array('hours'))
                      ->where("$this->_name.term_id=?", $term_id)				   
                      ->where("$this->_name.academic_year_id=?", $batch_id)				   
                      ->where("$this->_name.status !=?", 2);
                     $result=$this->getAdapter()
                     ->fetchAll($select);     
      
        return $result;
        
    }
    public function getClassTime($term_id, $batch_id){
                $select=$this->_db->select()
                      ->from($this->_name,array('time'))
                      ->where("$this->_name.term_id=?", $term_id)				   
                      ->where("$this->_name.academic_year_id=?", $batch_id)				   
                      ->where("$this->_name.status !=?", 2);
                     $result=$this->getAdapter()
                     ->fetchAll($select);     
      
        return $result;
        
    }
    
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					  ->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					   ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("short_code","from_date","to_date"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
    
    public function getMaxClass(){
      $select=$this->_db->select()
                      ->from($this->_name,array("count('term_id') as term_id"))			   
                      ->where("$this->_name.status !=?", 2)
                      ->group('term_id');
    
        $result=$this->getAdapter()
                      ->fetchAll($select);   
        return max($result)['term_id'];
        
    }
    
}
?>