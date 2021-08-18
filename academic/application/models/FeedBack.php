<?php

class Application_Model_FeedBack extends Zend_Db_Table_Abstract {

    public $_name = 'feed_master';
    protected $_id = 'id';
    public $_roll = 'T';
    
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
                ->from($this->_name)
                ->join(array("cat"=>"academic_master"),"cat.academic_year_id=$this->_name.academic_year_id")
           ->join(array("term"=>"term_master"),"term.term_id=$this->_name.term_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
        public function getRatings(){
          $select = $this->_db->select()
                ->from($this->_name)
                ->join(array("cat"=>"rating_master"),"cat.academic_year_id=$this->_name.academic_year_id")
           ->join(array("term"=>"term_master"),"term.term_id=$this->_name.term_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;

}
}


