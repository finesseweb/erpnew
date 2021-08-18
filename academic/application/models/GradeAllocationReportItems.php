<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_GradeAllocationReportItems extends Zend_Db_Table_Abstract
{
    public $_name = 'grade_allocation_report_items';
    protected $_id = 'grade_allocation_report_item_id';
  
    //get details by record for edit
	public function getRecords($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.grade_allocation_report_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);    
    //print_r($result);die;					  
        return $result;
    }
	
	public function trashItems($grade_allocation_report_id) {

        $this->_db->delete($this->_name, "grade_allocation_report_id=$grade_allocation_report_id");

    }
    /**
     * Check if grade of a subject is published by dean
     * @return Boolean Returns TRUE if grade finally published else returns FALSE
     */
    public function isGradeReportPublished($batch_id, $term_id, $course_id) {
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->joinInner("grade_allocation_report","grade_allocation_report.grade_report_id=$this->_name.report_id")
                      ->where("grade_allocation_report.academic_id=?", $batch_id)
                      ->where("$this->_name.term_id=?", $term_id)
                      ->where("$this->_name.course_id=?", $course_id)
					  ->where("grade_allocation_report.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);  		  
        if(is_array($result) && !empty($result)){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
	
	
	
	

}
?>