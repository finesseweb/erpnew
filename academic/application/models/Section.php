<?php

class Application_Model_Section extends Zend_Db_Table_Abstract {

    public $_name = 'section_master';
    protected $_id = 'id';
    
    public function getRecordByTermIndex($term_index){
        
            $select = $this->_db->select()
                ->from($this->_name)
                ->where("term_id=?", $term_index)
          
                ->where("$this->_name.status !=?", 1)
                      ->order(array('name'));

        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
        
    }
    public function getRecordById($id){
        
            $select = $this->_db->select()
                ->from($this->_name,array('name'))
                ->where("id=?", $id)
            ->order(array('name'));
                //->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['name'];
        
    }
    public function getRecord($id){
        
            $select = $this->_db->select()
                ->from($this->_name)
                ->where("id=?", $id);
           
                //->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
        
    }
    public function getRecords(){
        
            $select = $this->_db->select()
                ->from($this->_name)
                    ->joinLeft(array('term'=>'term_master'),"term.term_id = $this->_name.term_id")
                    ->joinLeft(array('batch'=>'academic_master'),"batch.academic_year_id = $this->_name.academic_year_id")
                    ->order(array("$this->_name.term_id"));
            
                //->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
        
    }
    
    public function getSectionId($term_id){
        
           $select = $this->_db->select()
                ->from($this->_name,array('id'))
                ->where("term_id=?", $term_id)
            
                ->where("$this->_name.status !=?", 1)
                   ->order(array('id'));

        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
        
        
    }
    public function getSectionIdWithConcat($term_id){
        
           $select = $this->_db->select()
                ->from($this->_name,array('GROUP_CONCAT(id) as id'))
                ->where("term_id=?", $term_id)
            ->order(array('id'));
                //->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['id'];
        
        
    }
    
    
    
}

