<?php

class Application_Model_RatingMaster extends Zend_Db_Table_Abstract {

    public $_name = 'rating_master';
    protected $_id = 'id';
    public $_roll = 'R';
    
    public function Auto_num() {
        $select = $this->_db->select()
                ->from($this->_name, array('max(id) as id'));
        $result = $this->getAdapter()
                ->fetchRow($select);
        if ($result['id']){ 
            return $this->setNum($result['id']+1);
        }
            return $this->setNum(1);
    }

    
    public function setNum($id) {
        $len = strlen((string) $id);
        if ($len == 1)
            return  $this->_roll. '00' . $id;
        else if ($len == 2)
            return $this->_roll. '0' . $id;
        return $this->_roll.$id;
    }
    
    
    public function getRecordById($id){
          $select = $this->_db->select()
                ->from($this->_name)
                  ->where('id = ?',$id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
    public function getRecords(){
          $select = $this->_db->select()
                ->from($this->_name);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
     public function getRecords1(){
          $select = $this->_db->select()
                ->from($this->_name)
                  ->where('status != ?', 2);
         // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getRecordsByRatings($rating_value){
          $select = $this->_db->select()
                ->from($this->_name, array('text_filed as name'))
                  ->where('rating_value =?', $rating_value)
                  ->where('status != ?', 2);
     // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['name'];
    }
    
    public function hasSaved($rating_value){
         $select = $this->_db->select()
                ->from($this->_name)
                 ->where('rating_value =?',$rating_value)
                  ->where('status != ?', 2);
        // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r(count($result));exit;
        return count($result);
        
    }
    

}

