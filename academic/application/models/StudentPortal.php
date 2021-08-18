<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Author Divakar
 */
class Application_Model_StudentPortal extends Zend_Db_Table_Abstract {

    public $_name = 'erp_student_information';
    protected $_id = 'student_id';
    private $_flashMessenger = null;

    //get details by record for edit
    
  
    
    
    public function getStudenInfo($id){
        
        $select=$this->_db->select()
                ->from($this->_name)
              ->where("$this->_name.status !=?", 2)
              ->where("$this->_name.student_id =?",$id);
              $result=$this->getAdapter()
                      ->fetchRow($select);  
          //  echo "<pre>";  print_r($result);exit;
        return $result;
        
    }
    
    
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        
        $username = $this->getUserName($id);
        $result['participant_username'] = $username['participant_username'];
        $result['participant_Alumni'] = $username['participant_Alumni'];
        $result['secondary_mail'] = $username['participant_email'];
        
        $result['linked_in']=$username['linked_in'];
      //  print_r($result);exit;
        return $result;
    }
    
    public function getUserName($id){
         $select = $this->_db->select()
                ->from('participants_login',array('participant_username','participant_Alumni','linked_in','participant_email'))
                ->where("participants_login.$this->_id=?", $id)
                ->where("participants_login.participant_Active !=?", 2);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    public function getUserName1($id){
         $select = $this->_db->select()
                ->from('erp_student_information',array('filename'))
                ->where("erp_student_information.$this->_id=?", $id);
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['filename'];
    }
    
    
    
    
  public function getRecordsById($id)
      {
          
        $select=$this->_db->select()
                ->from($this->_name)
              ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id")
              ->where("$this->_name.status !=?", 2)
              ->where("$this->_name.student_id =?",$id);
              $result=$this->getAdapter()
                      ->fetchAll($select);        
        return $result;
        }
        
        
        public function checkRecords($id)
        {
              $select=$this->_db->select()
                ->from('participants_login')
                ->where("participants_login.student_id =?",$id); 
              $result=$this->getAdapter()
                      ->fetchAll($select); 
              return count($result);
                
        }
        
         public function checkAlumni($id)
        {
              $select=$this->_db->select()
                ->from('erp_alumni_table')
                ->where("erp_alumni_table.student_id =?",$id); 
              $result=$this->getAdapter()
                      ->fetchAll($select); 
              return count($result);
                
        }
        public function getAlumniDetail($student_id)
        {
              $select=$this->_db->select()
                ->from(array('alumni'=>'erp_alumni_table'))
                ->joinleft(array("participant" => "participants_login"), "participant.student_id=alumni.student_id", array("participant_pword"))
                ->where("alumni.student_id =?",$student_id); 
             
              $result=$this->getAdapter()
                      ->fetchRow($select); 
             
              return $result;
                
        }
        
    //Get all records
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_id", array("short_code AS academic_year"))
                ->joinleft(array("terms" => "term_master"), "terms.term_id=$this->_name.terms_id", array("term_name"))
                ->where("$this->_name.academic_id !=?", 0)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('student_id', 'stu_fname'))
                ->where("$this->_name.status!=?", 2)
                ->order('student_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();

        foreach ($result as $val) {

            $data[$val['student_id']] = $val['stu_fname'];
        }
        return $data;
    }

    public function getstudents($academic_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getStudentsSortByName($academic_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.stu_fname ASC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getstudentsyearwise($academic_id, $year_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.year=?", $year_id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

   /* public function getstudentsdetails($academic_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id', 'erp_student_information.stu_id'))
                ->where("$this->_name.academic_id=?", $academic_id)
                //->where("$this->_name.year=?", $year_id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
    }	public function getstudentsdetails($academic_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students','erp_student_information.student_id','erp_student_information.stu_id'))
					  ->where("$this->_name.academic_id=?", $academic_id)
					  //->where("$this->_name.year=?", $year_id)
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);       

	
*/	
	public function getstudentsdetails($academic_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('CONCAT(erp_student_information.stu_fname," ",erp_student_information.stu_lname) AS students','erp_student_information.student_id','erp_student_information.stu_id'))
					  ->where("$this->_name.academic_id=?", $academic_id)
					  //->where("$this->_name.year=?", $year_id)
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select); 
        return $result;
    }
    
    
    
      public function getstudentsdetailsForFirstTerm($academic_id) {
        $structID = $this->structID($academic_id);
        if($structID!=0){
            $select = $this->_db->select()
                     ->from("erp_fee_structure_items")
                             ->where('structure_id =?', $structID);
                     $due_dates = $this->getAdapter()
                ->fetchAll($select);
                 $t1_date = date("Y-m-d",strtotime($due_dates[0]['t1_date']));
                 $t2_date = date("Y-m-d",strtotime($due_dates[0]['t2_date']));
                 $t3_date = date("Y-m-d",strtotime($due_dates[0]['t3_date']));
                 $t4_date = date("Y-m-d",strtotime($due_dates[0]['t4_date']));
                 $t5_date = date("Y-m-d",strtotime($due_dates[0]['t5_date']));
          $term_id = $this->getTermId($structID['structure_id'],1);  
          $category = $this->getFeeCategory();
          $total_fee_in_that_term = 0;
          foreach($category as $key_category => $value ){
                     $total_fee_in_that_term += $this->getFee($structID['structure_id'],1,$value['category_id'])[0]['total'];
          }
           $service_fee = $this->getFee($structID['structure_id'],1,2);
           $otherAnnualCharges = $this->getFee($structID['structure_id'],1,3);
           $tuition_fee = abs($total_fee_in_that_term - ((int)$service_fee[0]['total'] + (int)$otherAnnualCharges[0]['total']));
           $result = array(
               'gpa'=>0.0,
               'fee' => $total_fee_in_that_term,
               'service_fee' => $service_fee[0]['total'],
               'other_annual_charges' => $otherAnnualCharges[0]['total'],
               'tuition_fee' => $tuition_fee,
                'fee_discount'=>0,
               'total_fee' => $total_fee_in_that_term,
               'batch_id'=> $academic_id,
               'term_id' => $term_id,
               't1_date' => $t1_date, 
               't2_date' => $t2_date,
               't3_date' => $t3_date,
               't4_date' => $t4_date,
               't5_date' => $t5_date
                
           );
        return $result;
        }
        else
            return 0;
      }
      
      
      public function getFeeCategory(){
          
            $select = $this->_db->select()
                ->from('erp_fee_category_master', array('category_id'))
                ->where("status !=?",2);
       // echo $select; exit;
         $result = $this->getAdapter()
                  ->fetchAll($select);
         return $result;
          
          
          
      }
      
      
      public function getTermId($structure_id, $terms){
           $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('terms_id as term_id'))
                ->where("structure_id =?", $structure_id)
                ->where("terms =?",(INT)$terms);
       // echo $select; exit;
         $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['term_id'];
          
      }
    
    
    
     public function getFee($structure_id,$term_id, $category_id){   
         $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('sum(fees) as total'))
                ->where("structure_id =?", $structure_id)
                 ->where("category_id =?", $category_id)
                ->where("terms =?",(INT)$term_id);
       // echo $select; exit;
         $result = $this->getAdapter()
                ->fetchAll($select);
        
        return $result;
    }
    
    
    
    

    public function getstudentsdetailsByTerm_id($academic_id, $term_id, array $pre_index_details) {
        $x = $this->myfunc($term_id);
        //print($academic_id." ". $term_id);exit;
         $terms_count = strlen($x['term_name']);
        $term_val = substr($x['term_name'],$terms_count-1);
       // echo "<pre>";print_r($term_id);exit;
        $structID = $this->structID($academic_id);
        if($structID!=0){
       
        $select = $this->_db->select()
                ->from(array("student" => $this->_name), array('sum(fees) as sum', 'CONCAT(stu_fname,' . " " . ' stu_lname) as students', 'stu_id as participants_id','student_id'))
                ->join(array("ref_master" => "course_grade_after_penalties_items"), "ref_master.student_id=student.student_id")
                ->join(array("structure" => "erp_fee_structure_master"), "structure.academic_id=student.academic_id")
                ->join(array("due_date" => "erp_fee_structure_term_items"), "due_date.structure_id=structure.structure_id")
                ->join(array("due_date_real" => "erp_fee_structure_items"), "due_date_real.structure_id=structure.structure_id")
                
               // ->join(array("scholarship" => "scholarship_management"),"scholarship.term_id = student.academic_id" )
                ->where("student.academic_id = ?", $academic_id)
                ->where("ref_master.term_id = ?", $term_id)
               // ->where('scholarship.term_id = ?', $term_id)
                ->where("due_date.structure_id =?", $structID['structure_id'])
                ->where("due_date.category_id =?", 1)
                ->where("due_date.terms_id =?", $term_id)
                ->group("student.stu_id")
                ->where("student.status !=?", 2);
      //echo $select ; exit;
           
        $result = $this->getAdapter()
                ->fetchAll($select);
      
        if(count($result) != 0){
        $i = 0;
           $total_fee_in_that_term = $this->getTotalFee($structID['structure_id'],$term_id);
           $service_fee = $this->getServiceFee($structID['structure_id'],$term_id);
           $x = array();
           $otherAnnualCharges = $this->getOtherAnnualCharges($structID['structure_id'],$term_id);
        foreach ($result as $key => $value) {
              
            $gpa_percent = $this->getPercentage($result[$i]['student_id'],$pre_index_details,$academic_id);
           
            $result[$i]['total_fee'] = $total_fee_in_that_term[0]['total'];
            $result[$i]['sum1'] = $service_fee;
            $result[$i]['sum2'] = $otherAnnualCharges;
            
            if (count($gpa_percent) > 0) {
              
                if($pre_index_details['c_type'] != 'el'){
                  //  print_r($gpa_percent);exit;
                    $result[$i]['scholarship_percent'] = $gpa_percent[0]['fee'];
                    $result[$i]['cgpa'] = $gpa_percent[0]['cgpa'];
                }
                else{  
                $result[$i]['scholarship_percent'] = $gpa_percent[0]['fee'];
                $result[$i]['cgpa'] = $gpa_percent[0]['cgpa'];
                } 
                $total_fee = $this->getCalculatedFee($result[$i]['sum'],$result[$i]['scholarship_percent'],$total_fee_in_that_term[0]['total']);
         
                 $result[$i]['calculated_fee'] = $total_fee;
            } 
            else {
             //  $x[$i] = "hello";
                $result[$i]['scholarship_percent'] = "0";
                $result[$i]['calculated_fee'] = $total_fee_in_that_term[0]['total'];
            }
            $i++;
        }}
        else
        {
           $result = 3; 
        }
        
            }
 else {
     $result = 0;
 }
// echo "<pre>";print_r($result);exit;
     return $result;
    }
    public function getTotalFee($structure_id,$term_id){   
         $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('sum(fees) as total'))
                ->where("structure_id =?", $structure_id)
                ->where("terms_id =?",(INT)$term_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
   

    public function getPercentage($final_grade,array $pre_index_details,$batch){
    
     if($pre_index_details['c_type'] !='el'){
            $select = $this->_db->select()
                ->from(array('gr'=>'course_grade_after_penalties_items'),array('gr.term_id','gr.student_id','gr.item_id','gr.cgpa','(SELECT sch.scholarship_fee_wavier FROM scholarship_management sch WHERE sch.status =0 AND batch_id ='. $batch.' AND gr.cgpa BETWEEN sch.gpa_from AND sch.gpa_to) as fee'))
                ->where('gr.student_id = ?',$final_grade) 
                ->where('gr.term_id = ?', $pre_index_details['id']);
           // echo $select;exit;
                $result = $this->getAdapter()
                ->fetchAll($select);
               //  print_r($result);exit;
            }
            else
            {
                //print_r($pre_index_details['id']);exit;
                 $select = $this->_db->select()
                ->from(array('gr'=>'experiential_grade_allocation_items'),array('gr.student_id','gr.grade_allocation_item_id','gr.cgpa','(SELECT sch.scholarship_fee_wavier FROM scholarship_management sch WHERE sch.status =0 AND batch_id ='. $batch.' AND gr.cgpa BETWEEN sch.gpa_from AND sch.gpa_to) as fee'))
                         ->join(array("ref_master" => "experiential_grade_allocation_master"), "ref_master.grade_id=gr.grade_allocation_id")
                         ->where('ref_master.course_id = ?', $pre_index_details['id'])
                ->where('student_id = ?',$final_grade); 
                //echo $select; exit;
                    $result = $this->getAdapter()
                ->fetchAll($select);
            }
              // echo "<pre>";print_r($result);echo "</pre>";exit;
            return $result;
    }
    
    public function getLastId(){
         $select = $this->_db->select()
                ->from('scholarship_management', array('max(id) as last_id'));
          $result = $this->getAdapter()
                ->fetchAll($select);
          return $result[0]['last_id'];
    }
    
    public function checkLastValue($last_id,$gpa_from){
        
    }
    
    
    public function getServiceFee($structure_id,$term_id){
              //print($term_id);exit;
         $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('sum(fees) as sum1'))
                ->where("structure_id =?", $structure_id)
                 ->where("category_id =?", 2)
                ->where("terms_id =?",(INT)$term_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
      return $result;
        
    }
    
    public function getOtherAnnualCharges($structure_id,$term_id){
         $select = $this->_db->select()
                ->from('erp_fee_structure_term_items', array('sum(fees) as sum2'))
                ->where("structure_id =?", $structure_id)
                 ->where("category_id =?", 3)
                ->where("terms_id =?",(INT)$term_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getCalculatedFee($sum, $relief_percent_in_fee,$total_fee)
    { 
        $other_term_fee = $total_fee - $sum;
        $scholarship_fee = $other_term_fee+($sum - ($sum/100)*$relief_percent_in_fee);
        return $scholarship_fee;
    }

    public function myfunc($term_id) {
              
        $select = $this->_db->select()
                ->from("term_master", 'term_name')
                ->where("term_id =?", $term_id);

        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result[0];
    }

    public function structID($id) {
      
        $select = $this->_db->select()
                ->from("erp_fee_structure_master", 'structure_id')
                ->where("academic_id =?", $id);

        $result = $this->getAdapter()
                ->fetchAll($select);
       //print_r($result);exit; 
      if(count($result)==0){
         return 0;
      }else
      {
          return $result[0];
      }
    }

    public function getStuIds() {
        $select = $this->_db->select()
                ->from($this->_name, 'student_id')
                ->where("$this->_name.status !=?", 2)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getStudentNames($academic_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $k => $val) {
            $data[$val['student_id']] = $val['students'];
        }
        return $data;
    }

    public function getStudentPCRecord($academic_id = '', $stu_id = '') {
        //print_r($branch_id); die;
        if (!empty($academic_id) || !empty($stu_id)) {
            $where = "";
            if (!empty($academic_id)) {
                $where .= " AND erp_student_information.academic_id = '$academic_id'";
            }
            if (!empty($stu_id)) {
                $where .= " AND erp_student_information.student_id = '$stu_id'";
            }

            $select = "SELECT `erp_student_information`.* FROM `erp_student_information` WHERE erp_student_information.status!=2 $where GROUP BY erp_student_information.student_id";
        }
        //echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result);die;
        return $result;
    }

    public function getStudentsAcademicWise($academic_year_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('student_id', 'stu_fname', 'stu_lname'))
                ->where("$this->_name.academic_id=?", $academic_year_id)
                ->where("$this->_name.status!=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {

            $data[$val['student_id']] = $val['stu_fname'] . '-' . $val['stu_lname'];
        }
        return $data;
    }
    public function fetchDiscontinuedStudentDetailById($stu_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id=?", $stu_id)
                ->where("$this->_name.stu_status = ?", 2)
                ->where("$this->_name.status != ?", 2)
                ->order("student_id DESC")
                ->limit(1,0);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    public function fetchDiscontinuedBatchesOfStudent($student_id){
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id IN (SELECT stu_id FROM $this->_name WHERE student_id = ?)", $student_id)  
                ->where("$this->_name.stu_status = ?", 2)
                ->where("$this->_name.status != ?", 2)
                ->order("$this->_name.student_id ASC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
   
    public function fetchAllBatchesOfStudent($student_id){
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.stu_id IN (SELECT stu_id FROM $this->_name WHERE student_id = ?)", $student_id) 
                ->where("$this->_name.status != ?", 2)
                ->order("$this->_name.student_id ASC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getDistincetStudentsByBatchId($academic_id) {
        $select = $this->_db->select()
                ->from($this->_name, array('DISTINCT(erp_student_information.stu_id) as stu_id','CONCAT(erp_student_information.stu_fname,erp_student_information.stu_lname) AS students', 'erp_student_information.student_id'))
                ->where("$this->_name.academic_id=?", $academic_id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
   

}

