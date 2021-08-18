<?php

class Application_Model_Application extends Zend_Db_Table_Abstract {

    public $_name = 'application';
    protected $_id = 'application_id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
        //->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getRecords() {

        $select = $this->_db->select()
                ->from($this->_name)
                ->group( array ("batch_id","term_id") );
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

        public function getRecordsPdm($id,$term_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('term_id=?',$term_id)
                ->where('batch_id=?',$id);
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    
    
    
    
    public function getAcademic(){
              $select = $this->_db->select()
                ->from('academic_master',array('short_code'));
            
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['short_code'];
    }
    public function getAcademic1($batch){
              $select = $this->_db->select()
                ->from('academic_master',array('short_code'))
            ->where('academic_year_id =?',$batch);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['short_code'];
    }
    
    
    public function getTerm($id){
          $select = $this->_db->select()
                ->from('term_master',array('term_name'))                  
                  ->where('term_id=?',$id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['term_name'];
    }
    
    public function getTermb($id){
          $select = $this->_db->select()
                ->from('term_master',array('term_name'))                  
                  ->where('term_id_b=?',$id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['term_name'];
    }
    
    
     public function getTotal($id,$term_id,$term_id_b) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('term_id =?',$term_id)
                ->where('batch_id =?',$id);
        $result = $this->getAdapter()
                ->fetchAll($select);

        return count($result);
    }
    
    
    

    public function getRecordsByPdmId($id) {
        $select = $this->_db->select()
                ->from('erp_student_information', array('concat(stu_fname," ",stu_lname) as name', 'stu_dob','academic_id'))
                ->where('stu_id=?', $id);
       // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchRow($select);
       
        return $result;
    }

    public function getRecordsByBatch($stu_id,$batch, $term, $term_id_b) {
       
        $select = $this->_db->select()
                ->from($this->_name, array('course_id as current_papers'))
                ->where('stu_id=?',$stu_id)
                ->where('batch_id=?', $batch)
              
                ->where('term_id = ?', $term);
        $result = $this->getAdapter()
                ->fetchAll($select);
        $result2 = $this->getBackDetails($stu_id,$batch, $term, $term_id_b);
        $res = $res2 = 0;
        if (count($result) > 0)
            $res = 1;
        if (count($result2) > 0)
            $res2 = 1;
//echo $res.'==='.$res2;die;
        $arr = array('res' => $res,
            'res2' => $res2,
            'result' => $result,
            'result2' => $result2
        );

//print_r($arr);exit;
        return $arr;
    }

    public function getBackDetails($stu_id,$batch,$term, $term_id_b) {
		if(!empty($term_id_b)){
        $select = $this->_db->select()
                ->from($this->_name, array('course_id_b as back_papers'))
                 ->where('stu_id=?',$stu_id)
                ->where('batch_id=?', $batch)
                ->where('term_id_b = ?', $term_id_b);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;}
		return array();
    }
    
    
    public function getStudentInBatch(array $batch){
        
               $select = $this->_db->select()
		->from($this->_name, array('application_id','stu_id','stu_name'))
                       ->where("$this->_name.batch_id in (?)",$batch)
                ->order('stu_name  ASC');
             // echo $select; die;
        $result = $this->getAdapter()->fetchAll($select); 
        return $result;
        
    }
    public function getStudentInBatchAndTerm($batch,$term,$paper){
        
               $select = $this->_db->select();
		$select->from($this->_name, array('application_id','stu_id','stu_name'));
                      $select ->where("$this->_name.batch_id = ?",$batch);
                      if($paper == 'curr'){
                      $select ->where("$this->_name.term_id = ?",$term);
                      }
                       elseif($paper == 'back'){
                      $select ->where("$this->_name.term_id_b = ?",$term);
                       }
                  else {
                      $select ->where("$this->_name.term_id = ?",$term);
                      $select ->orwhere("$this->_name.term_id_b = ?",$term);
                  }
                $select->order('stu_name  ASC');
           // echo $select; die;
        $result = $this->getAdapter()->fetchAll($select); 
        return $result;
        
    }
    
        	public function getDropDownList(){
        $select = $this->_db->select()
		->from($this->_name, array('application_id','stu_id','stu_name'))				
                ->order('stu_name  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[$val['application_id']] = $val['stu_id'].' - '.$val['stu_name'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
    

    
    
    

}
