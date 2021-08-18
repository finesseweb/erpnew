<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Coursefee extends Zend_Db_Table_Abstract
{
    public $_name = 'course_fee';
    protected $_id = 'id';
  
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
					 // ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    
    
    
    
    
    
    
    
	public function isRecordExists($batch, $term, $paper)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)                      				   
					  ->where("$this->_name.status !=?", 2)
					  ->where("$this->_name.batch =?", $batch)
					  ->where("$this->_name.term =?", $term)
					  ->where("$this->_name.paper =?", $paper);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return count($result);
    }
    
	public function getFee($term, $batch, $paper)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('fee'))                      				   
					  ->where("$this->_name.status !=?", 2)
					  ->where("$this->_name.batch =?", $batch)
					  ->where("$this->_name.term =?", $term)
					  ->where("$this->_name.paper =?", $paper);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result['fee'];
    }
    
    
    
    
}