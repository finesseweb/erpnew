<?php

class Application_Model_CourseReport extends Zend_Db_Table_Abstract
{
    public $_name = 'course_report';
    protected $_id = 'id';
  
   public function getTotalNumberOfDays($term_id, $batch_id){
 
        $select = $this->_db->select()
                    ->from($this->_name)
                    //->joinLeft(array('batch' => 'batch_scheduler'),'batch.date = stu.date')
                    ->where("term_id =?",$term_id)
                    ->where("batch_id =?", $batch_id);
                $result = $this->getAdapter()
                ->fetchAll($select);
           //echo $select;exit;
              return $result;
    
   }
	
	
}
