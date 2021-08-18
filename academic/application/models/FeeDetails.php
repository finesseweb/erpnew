<?php

class Application_Model_FeeDetails extends Zend_Db_Table_Abstract {

    public $_name = 'student_fee_details';
    protected $_id = 'fee_details_id';

    public function getUserId($batch_id, $term_id, $pdm_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('fee_details_id', 'total_fee', 'gpa', 'fee_discount', 'fee', 'tuition_fee', 'service_fee', 'other_annual_charges'))
                ->where("term_id =?", $term_id)
                ->where("batch_id =?", $batch_id)
                ->where("participants_id=?", $pdm_id);
        $result = $this->getAdapter()
        
                ->fetchRow($select);
        return $result;
    }
    
    
      public function getStuTransReal($id){
		if($id){
		$select = $this->_db->select()
					->from($this->_name, array('total_fee'))
					->where("$this->_name.fee_details_id=?",$id);
		$result = $this->getAdapter()
				->fetchAll($select);	

		return $result;
                }
                else
                    return false;
        
    }
    
    
     public function getUserIdByBatch($batch_id, $pdm_id,$term_id) {

        $select = $this->_db->select()
                ->from($this->_name, array('fee_details_id','term_id', 'total_fee', 'gpa', 'fee_discount', 'fee', 'tuition_fee', 'service_fee', 'other_annual_charges'))
               ->where("term_id =?", $term_id)
                ->where("batch_id =?", $batch_id)
                ->where("participants_id=?", $pdm_id);
     //echo $select; exit;
        $result = $this->getAdapter()
        
                ->fetchAll($select);
        
       // print_r($result);exit;
        
        return $result;
    }
    
    

    public function getShortCode($batch_id) {
        $select = $this->_db->select()
                ->from('academic_master', array('short_code'))
                ->where("academic_year_id =?", $batch_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        $id_prefix = explode('-', $result['short_code']);
        return $id_prefix;
    }
    
      public function getMaxExistingBatch($like){
          
        $select = $this->_db->select()
                ->from('erp_student_information', array('max(stu_id) as id'))
                    ->where('stu_id LIKE ?', "$like%");
        $result = $this->getAdapter()
                ->fetchRow($select);
        
        return $result['id'];
    }
    
    
    public function getMaxId($batch_id){
         $select = $this->_db->select()
                ->from('erp_student_information', array('MAX(stu_id) as max_id'))
                ->where("academic_id =?", $batch_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
      return $result['max_id'];
      
    }
    
    
    public function checkDetails($id, $term_id, $batch_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("term_id =?", $term_id)
                ->where("batch_id =?", $batch_id)
                ->where("student_id =?", $id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function checkDetails1($term_id, $batch_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("term_id =?", $term_id)
                ->where("batch_id =?", $batch_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function getAllSum($term_id, $batch_id) {


        $select = $this->_db->select()
                ->from($this->_name, array("sum(total_fee) as total"))
                ->where("term_id =?", $term_id)
                ->where("batch_id =?", $batch_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['total'];
    }

    public function checkWithPdmId($pdm_id, $batch_id, $term_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("term_id =?", $term_id)
                ->where("batch_id =?", $batch_id)
                ->where("participants_id =?", $pdm_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function getRecords() {

        $select = $this->_db->select()
                ->from($this->_name)
                ->group(array('term_id', 'batch_id'));
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function termInfo($id) {
        $select = $this->_db->select()
                ->from('term_master', array('term_name'))
                ->where('term_id =?', $id)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function batchInfo($id) {

        $select = $this->_db->select()
                ->from('academic_master', array('short_code'))
                ->where('academic_year_id =?', $id)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getRecordsByBatchTerm($term_id, $batch_id) {
        $select = $this->_db->select()
->from(array('main'=>'student_attendance'),array("distinct('student_id')"))
->join(array("student" => "erp_student_information"), "student.student_id=main.student_id",array("concat(stu_fname,' ',stu_lname) as participants_name","student_id"))
->where("main.term_id =?", $term_id)
->where("main.batch_id =?", $batch_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRecordsByBatchTerm1($term_id, $batch_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("term_id =?", $term_id)
               // ->where("promoted !=?", 1)
                ->where("batch_id =?", $batch_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

      public function getResultValue($gpa) {
        $select = $this->_db->select()
                ->from('result_management')
                ->where("gpa_from <= ?", (float)$gpa)
                ->where("gpa_to >= ?", (float)$gpa);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

     public function getgpa($gpat, $gpaf) {
        echo $select = $this->_db->select()
                ->from($this->_name)
                //->where("gpa >= ?", $gpat)
                ->where ('gpa between '.$gpat.' and '.$gpaf);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

}
