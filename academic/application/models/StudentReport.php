<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_StudentReport extends Zend_Db_Table_Abstract
{
    public $_name = 'erp_student_information';
    protected $_id = 'student_id';
  

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
						->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array("CONCAT(academic.from_date,'-',academic.to_date) AS academic_year"))
						->joinleft(array("terms"=>"term_master"),"terms.term_id=$this->_name.terms_id",array("term_name"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	public function getDropDownList(){
        $select = $this->_db->select()
                ->from($this->_name, array('student_id','stu_fname'))				
				->where("$this->_name.status!=?",2)
                ->order('course_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['student_id']] = $val['stu_fname'];
        }
        return $data;
    }
	
	
	
	public function getstudents($academic_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students','erp_student_information.student_id'))
                      ->where("$this->_name.academic_id=?", $academic_id)
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	public function getstudentsyearwise($academic_id,$year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students','erp_student_information.student_id'))
					  ->where("$this->_name.academic_id=?", $academic_id)
					  ->where("$this->_name.year=?", $year_id)
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	
	public function getstudentsdetails($academic_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students','erp_student_information.student_id','erp_student_information.stu_id'))
					  ->where("$this->_name.academic_id=?", $academic_id)
					  //->where("$this->_name.year=?", $year_id)
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
}
?>