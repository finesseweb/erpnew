<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_CoursewisepenaltiesItems extends Zend_Db_Table_Abstract
{
    public $_name = 'course_wise_penalties_items';
    protected $_id = 'item_id';
  
    //get details by record for edit
	public function getRecords($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.id=?", $id)	;		   
					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
	
	public function trashItems($id) {

        $this->_db->delete($this->_name, "id=$id");

    }
    public function getStudentTermPenalty($penalty_master_id, $term_id, $student_id){
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.id=?", $penalty_master_id)
                      ->where("$this->_name.term_id=?", $term_id)
                      ->where("$this->_name.student_id=?", $student_id);		   
					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
    public function getPenaltyItemByMasterIdTermId($penalty_master_id, $term_id) {
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.id=?", $penalty_master_id)
                      ->where("$this->_name.term_id=?", $term_id);		   
					  
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
}
?>