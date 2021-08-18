<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ReferenceGradeMasterItems extends Zend_Db_Table_Abstract
{
    public $_name = 'reference_grade_master_items';
    protected $_id = 'reference_item_id';
  
    //get details by record for edit
	public function getRecords($reference_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.reference_id=?", $reference_id);				   
					 
        $result=$this->getAdapter()
                      ->fetchAll($select);  
					  
        return $result;
    }
    public function getRecordsByAcademicId($academic_year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                     ->joinLeft(array("ref_master"=>"reference_grade_master"),"ref_master.reference_id=$this->_name.reference_id",array("academic_year_id"))
                      ->where("ref_master.academic_year_id=?", $academic_year_id)
                      ->where("ref_master.status !=?", 2);;				   
					 
        $result=$this->getAdapter()
                      ->fetchAll($select);  
					  
        return $result;
    }
	
	
	
		public function trashItems($reference_id) {

        $this->_db->delete($this->_name, "reference_id=$reference_id");

		}
	
	
	
}
?>