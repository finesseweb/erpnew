<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ElectivesGradeAllocationReportItems extends Zend_Db_Table_Abstract
{
    public $_name = 'electives_grade_allocation_report_items';
    protected $_id = 'elective_grade_report_item_id';
  
    //get details by record for edit
	public function getRecords($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.elective_grade_report_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
	
	public function trashItems($elective_grade_report_id) {

        $this->_db->delete($this->_name, "elective_grade_report_id=$elective_grade_report_id");

    }
	
	
	
	

}
?>