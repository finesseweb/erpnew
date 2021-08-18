<?php


class Application_Model_Classalotment extends Zend_Db_Table_Abstract {

    public $_name = 'class_allotment';
    protected $_id = 'id';
    
    public function getRecords(){
            $select = $this->_db->select()
                ->from($this->_name);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    public  function getRecordById($id){
              $select = $this->_db->select()
                ->from($this->_name)
              ->where('id=?',$id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    public function check($course_id, $date,$class,$batch, $term)
    {
        $select = $this->_db->select()
                ->from($this->_name)
              ->where('course_id=?',$course_id)
                ->where('class_no=?',$class)
                ->where('batch_id=?',$batch)
                ->where('term_id=?',$term)
        ->where('date=?', date('Y-m-d',strtotime($date)));
     
           // echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    
}