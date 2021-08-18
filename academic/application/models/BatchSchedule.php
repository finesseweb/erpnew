<?php

/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class Application_Model_BatchSchedule extends Zend_Db_Table_Abstract {

    public $_name = 'batch_scheduler';
    protected $_id = 'batch_schedule_id';

    //get details by record for edit


    
    public function getAllversion(){
                
      $select = $this->_db->select()
                //->from($this->_name,array('max(publish) as version'))
                ->from($this->_name, array('max(publish) as max_version','term_id','batch','section'))
                ->where("status!=?", 2)
                ->group(array('batch','term_id','section'));
     // echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getAllversionWithBatchTerm($batch='',$term=''){
                
      $select = $this->_db->select()
                //->from($this->_name,array('max(publish) as version'))
                ->from($this->_name, array('max(publish) as max_version','term_id','batch','section'))
                ->where('batch =?',$batch)
                ->where('term_id =?',$term)
                ->where("status!=?", 2)
                ->group(array('batch','term_id','section'));
     // echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getCourseRoomWise($room,$max_room,$version){
        $sql[] = 'distinct(day) as day';
       
        for($i =1 ; $i<= $max_room; $i++){
        $sql[] =  'class_'.$i;
        $sql[] =  'room_'.$i;
        }
        
      $select = $this->_db->select();
                 $select->from($this->_name,$sql);
                 $select->where("publish =?", (float)$version);
                 $sql = '';
                 for($i =1 ; $i<= $max_room; $i++){
                     if($i == 1)
                     $sql .= ' room_'.$i.' ='.$room;
                     else
                     $sql .= ' or room_'.$i.' ='.$room;  
                 }
                 $select->where($sql);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getCourseDayWise($max_room,$version){
        $sql[] = 'distinct(day) as day';
       
        for($i =1 ; $i<= $max_room; $i++){
        $sql[] =  'class_'.$i;
        $sql[] =  'room_'.$i;
        }
        
      $select = $this->_db->select();
                 $select->from($this->_name,$sql);
                 $select->where("publish =?", (float)$version);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    
    public function getCourseByoneClass($class,$day,$version,$section){
              $select = $this->_db->select();
                 $select->from($this->_name, array($class));
                 $select->where("day =?", $day);
                 $select->where("$class is NOT NULL");
                 
                 $select->where("publish =?", (float)$version);
                 $select->where("section =?", $section);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
        
        
    }
    
    
    
    
    public function getFacultybyCourseId($course_id){

        
      $select = $this->_db->select();
                 $select->from('employee_allocation_items_master',array('employee_id','faculty_id','visiting_faculty_id'));
                $select->where('course_id = ?',$course_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    

    
    public function create_column($no_of_column,$predefined_column_name){
     $result = $this->fetchColumnName();
     $field_arr = $this->fields($result,'Field');
        foreach($predefined_column_name as $key => $value){
            for($i = 1; $i<=$no_of_column; $i++){
                $field_name = $value."_".$i;
                if(!in_array($field_name,$field_arr)){
                    $select = "Alter table $this->_name add  $field_name varchar(50)";
                     $result = $this->getAdapter()
                ->query($select);
                }
                    
            }
       } 
    }
    
    
    public function fields($result,$column){
        foreach($result as $key => $value){
            $data[] = $value[$column];
        }
        return $data;
    }
    
    public function fetchColumnName(){
        
        $select = "show fields from $this->_name";
       
         $result = $this->getAdapter()
                ->fetchAll($select);
        return $result ;
        
    }
    
    


    public function getDoB($batch,$term,$month){
        if(strlen($month)==1)
              $month = '0'.$month; 
          $select = $this->_db->select()
                 // ->distinct()
                ->from(array('info'=>'erp_student_information'))
                ->join(array("student" => "student_attendance"), "student.student_id=info.student_id")
                  
                ->where("student.term_id=?", $term)
                ->where("student.batch_id =?", $batch)
                ->where('info.stu_dob like ?', "_%/".$month."/%_");
         //echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function checkValue($batch, $term,$section) {
        $select = $this->_db->select()
                //->from($this->_name,array('max(publish) as version'))
                ->from('course_report')
                ->where("term_id=?", $term)
                ->where("section=?", $section)
                ->where("batch_id =?", $batch);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function getAllBatch($date, $term_id) {
        $select = $this->_db->select()
                //->from($this->_name,array('max(publish) as version'))
                ->from($this->_name, array('batch'))
                ->where("term_id=?", $term_id)
                ->where("date =?", $date)
                ->where("status!=?", 2)
                ->group('batch');
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function allDetails($batch_id, $term_id) {
        $select = $this->_db->select()
                //->from($this->_name,array('max(publish) as version'))
                ->from($this->_name)
                ->where("batch=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("publish =?", (float) 0.0)
                ->where("status!=?", 2);
        //echo $select;exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result; //$this->getAll($batch_id,$term_id,$result['version']);
    }

    public function allDetail($batch_id, $term_id,$section) {
        //print_r($batch_id.$term_id);exit;
        $select = $this->_db->select()
                ->from($this->_name, array('max(publish) as version'))
                ->where("batch=?", $batch_id)
                ->where("term_id =?", $term_id)
              ->where("section =?", $section)
                // ->where("publish =?",(float)0.0)
                ->where("status!=?", 2);
      
        $result = $this->getAdapter()
                ->fetchAll($select);


        return $this->getAll($batch_id, $term_id,$section, $result[0.1]['version']);
    }

    public function getALL($batch_id, $term_id,$section, $version) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("batch = ?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("section =?", $section)
                ->where("status!=?", 2)
                ->where("publish =? ", (FLOAT) $version);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function publish_version($batch_id, $term_id,$section, $date, $class_1) {
        // print($batch_id."=".$term_id."=".$date);exit;
        $select = $this->_db->select()
                ->from($this->_name, array('max(publish) as version'))
                ->where("batch=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("section =?",$section)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['version'];
    }

    public function minVersion($batch_id, $term_id,$section , $date, $class_1) {
        // print($batch_id."=".$term_id."=".$date);exit;
        $select = $this->_db->select()
                ->from($this->_name, array('min(publish) as version'))
                ->where("batch=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("section =?",$section)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['version'];
    }

    public function checkIfAnyChange($batch_id, $term_id,$section , $version , $date, $class,$no) {
      //  echo "<pre>";print_r($class);exit;
        $where = '';
       // $day = date("l", strtotime($date));
        //echo $day;exit;
        $where = $this->str_lreplace('AND', ' ',$where);
        $select = $this->_db->select();
               $select ->from($this->_name);
                $select->where("batch=?", $batch_id);
                $select->where("term_id =?", $term_id);
               $select ->where("section =?",$section);
               $select ->where("date =?", $date);
               foreach($class as $key => $value){
                      $select ->where("$key=?",$value);
                 }
               
                $select->where("publish =?", (float) $version);
               
                
        //->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }
    
    
   public function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}
    
    

    public function saveAtFirstTime($batch_id, $term_id, $version,$section) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("batch=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("section =?",$section)
                ->where("publish =?", (float) $version);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function checkDetails($batch_id, $term_id, $date,$section) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("batch=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("section =?",$section)
                ->where("date =?", $date)
                ->where("publish = ?", 0.0);
        //->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function getTermDetails($batch_id, $term_id) {

        $select = $this->_db->select()
                ->from("term_master")
                ->where("academic_year_id=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getVersion($batch_id, $term_id,$section) {
        //echo $batch_id, $term_id;
        $select = $this->_db->select()
                ->from($this->_name, array("distinct(publish) as version"))
                ->where("batch=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("section =?", $section)
                ->where("publish !=?", (float) 0.0)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getCourseDetails($batch_id, $term_id) {

        $select = $this->_db->select()
                // ->distinct("master.ccl_id")
                ->from("core_course_master")
                ->joinLeft(array("Allotment" => "employee_allocation_items_master"), "Allotment.cc_id=core_course_master.cc_id")
                ->joinLeft(array("master" => "course_master"), "master.course_id=core_course_master.course_id")
                ->where("core_course_master.term_id = ?", $term_id)
                ->where("Allotment.term_id = ?", $term_id)
                ->where("core_course_master.academic_year_id =?", $batch_id)
                ->where("core_course_master.status !=?", 2)
                ->group("master.course_code");
        //echo $select; exit; 
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getRecord($id, $version,$section= '') {
        $select = $this->_db->select()
                ->from($this->_name, array('batch', 'term_id'))
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.section=?", $section)
                ->where("$this->_name.publish=?", (FLOAT) $version)
                ->where("$this->_name.status !=?", 2);
        //echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($resut);exit;
        return $this->getAllRecords($result[0], $version);
    }

        public function getDropDownList(){ 
        $select = $this->_db->select()
        ->from($this->_name, array("DISTINCT(batch)"))  
          ->joinLeft(array("master" => "academic_master"), "master.academic_year_id=$this->_name.batch",array("academic_year_id","short_code")) ;
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        $st_year ='';
        $end_year='';
        foreach ($result as $val) {
            
            $data[$val['academic_year_id']] = $val['short_code'];
            
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
            //print_r($data);die;
        }
        return $data;
    }

    public function getDropDownListTerm($academic_year_id){ 

        $select = $this->_db->select()
        ->from($this->_name, array("distinct($this->_name.term_id) as routine_term_id",'term.term_name','term.term_id')) 
        ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id")
         ->where("$this->_name.batch=?", $academic_year_id);
         $result = $this->getAdapter()->fetchAll($select); 

        return $result;

    }
    

    public function gebatchIdTermId($id) {
        $select = $this->_db->select()
                ->from($this->_name, array('batch', 'term_id'))
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result[0];
    }

    public function getAllRecords($batch_details, $version) {
        //print_r($batch_details['batch']);exit;
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.batch=?", $batch_details['batch'])
                ->where("$this->_name.term_id=?", $batch_details['term_id'])
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.publish=?", (FLOAT) $version);

        $result = $this->getAdapter()
                ->fetchAll($select);
        $term_name = $this->getTerms($batch_details['term_id']);

        $terms_count = strlen($term_name[0]['term_name']);
        $term_val = substr($term_name[0]['term_name'], $terms_count - 1);
        $result['course_result'] = $this->getCourseDetails($batch_details['batch'], $batch_details['term_id']);

        return $result;
    }

    public function getAllRecordsByVersionId($batch_id, $term_id, $version) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.batch=?", $batch_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.publish=?", (FLOAT) $version);
        $result = $this->getAdapter()
                ->fetchAll($select);
        $term_name = $this->getTerms($term_id);
        $terms_count = strlen($term_name[0]['term_name']);
        $term_val = substr($term_name[0]['term_name'], $terms_count - 1);
        $result['course_result'] = $this->getCourseDetails($batch_id, $term_id);
        // print_r($result);exit;
        return $result;
    }

    public function getTerms($term_id) {

        $select = $this->_db->select()
                ->from("term_master")
                ->where("term_id=?", $term_id)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    //Get all records
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("master" => "academic_master"), "master.academic_year_id=$this->_name.batch")
                ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id")
                //->joinleft(array("term"=>"term_master"),"term.term_id=components_items.term_id",array("term_name"))
                //->joinleft(array("course"=>"course_master"),"FIND_IN_SET(course.course_id,components_items.course_id)",array("GROUP_CONCAT(course_name) AS course_name"))
                //->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
                //->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
                //->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date","short_code"))
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.publish !=?", 0)
                ->group(array("$this->_name.publish", "$this->_name.term_id","$this->_name.section"))
                ->order(array("master.academic_year_id DESC", "term.term_id DESC", "$this->_name.publish DESC"));

        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
//print_r($result);die;						  
        return $result;
    }

    public function getNotPublishedRecord() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("master" => "academic_master"), "master.academic_year_id=$this->_name.batch")
                ->joinLeft(array("term" => "term_master"), "term.term_id=$this->_name.term_id")
                //->joinleft(array("term"=>"term_master"),"term.term_id=components_items.term_id",array("term_name"))
                //->joinleft(array("course"=>"course_master"),"FIND_IN_SET(course.course_id,components_items.course_id)",array("GROUP_CONCAT(course_name) AS course_name"))
                //->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
                //->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
                //->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date","short_code"))
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.publish =?", 0)
                ->group(array("$this->_name.publish", "$this->_name.term_id"));
        //  ->order("$this->_name.$this->_id DESC");
        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
//print_r($result);die;						  
        return $result;
    }

    public function getRecordsByFacultyId($faculty_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("components_items" => "evaluation_components_items_master"), "components_items.ec_id=$this->_name.ec_id", array("term_id", "GROUP_CONCAT(components_items.course_id) as courses", "course_id"))
                //->joinleft(array("term"=>"term_master"),"term.term_id=components_items.term_id",array("term_name"))
                //->joinleft(array("course"=>"course_master"),"FIND_IN_SET(course.course_id,components_items.course_id)",array("GROUP_CONCAT(course_name) AS course_name"))
                //->joinleft(array("cc"=>"course_category_master"),"cc.cc_id=$this->_name.cc_id",array("cc_name"))
                //->joinleft(array("credit"=>"credit_master"),"credit.credit_id=$this->_name.credit_id",array("credit_value"))
                ->joinleft(array("academic" => "academic_master"), "academic.academic_year_id=$this->_name.academic_year_id", array("from_date", "to_date", "short_code"))
                ->where("$this->_name.status !=?", 2)
                ->where("$this->_name.employee_id =?", $faculty_id)
                ->group("$this->_name.ec_id")
                //->group("components_items.course_id")
                ->order("$this->_name.$this->_id DESC");

        //echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
//print_r($result);die;						  
        return $result;
    }

    //View purpose

    public function getcorecourselearning($corecourselearn) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinleft(array("term" => "term_master"), "term.term_id=$this->_name.term_id", array("term_name"))
                ->joinleft(array("course" => "course_master"), "course.course_id=$this->_name.course_id", array("course_name"))
                ->joinleft(array("cc" => "course_category_master"), "cc.cc_id=$this->_name.cc_id", array("cc_name"))
                ->joinleft(array("credit" => "credit_master"), "credit.credit_id=$this->_name.credit_id", array("credit_value"))
                ->where("$this->_name.academic_year_id =?", $corecourselearn)
                ->where("$this->_name.status!=?", 2);

        $result = $this->getAdapter()
                ->fetchAll($select);



        return $result;
    }

    public function getItemRecords($id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id)
                ->where("$this->_name.status !=?", 2)
                ->group("batch");
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getExperientialEvaluationComponentsItemRecords($eval_component_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("component_items" => "experiential_evaluation_components_items"), "component_items.ec_id=$this->_name.ec_id", array("course_id", "component_name", "weightage", "remaining_weightage"))
                ->joinLeft(array("course" => "experiential_learning_components_master"), "course.elc_id=component_items.course_id", array("elc_id as course", "elc_name"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.ec_id=?", $eval_component_id);


        $result = $this->getAdapter()
                ->fetchAll($select);


        return $result;
    }

    public function getComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("component_items" => "evaluation_components_items_master"), "component_items.ec_id=$this->_name.ec_id", array("eci_id", "term_id", "course_id", "component_name", "weightage"))
                //->joinLeft(array("com_grades"=>"component_grades"),"component_items.eci_id=com_grades.component_id",array("component_id"))
                ->where("$this->_name.status != 2")
                //->where("com_grades.component_id IS NULL")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("component_items.term_id=?", $term_id)
                ->where("component_items.course_id=?", $course_id);

        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {

            $data[$val['eci_id']] = $val['component_name'];
        }
        return $data;
    }

    public function getAddComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("component_items" => "evaluation_components_items_master"), "component_items.ec_id=$this->_name.ec_id", array("eci_id", "term_id", "course_id", "component_name", "weightage"))
                ->joinLeft(array("com_grades" => "component_grades"), "component_items.eci_id=com_grades.component_id", array("component_id"))
                ->where("$this->_name.status != 2")
                ->where("com_grades.component_id IS NULL")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("component_items.term_id=?", $term_id)
                ->where("component_items.course_id=?", $course_id);

        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {

            $data[$val['eci_id']] = $val['component_name'];
        }
        return $data;
    }

    public function getGradeAddComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("component_items" => "evaluation_components_items_master"), "component_items.ec_id=$this->_name.ec_id", array("eci_id", "term_id", "course_id", "component_name", "weightage"))
                ->joinLeft(array("grade_allocation" => "grade_allocation_master"), "component_items.eci_id=grade_allocation.component_id", array("component_id"))
                ->where("$this->_name.status != 2")
                ->where("grade_allocation.component_id IS NULL")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("component_items.term_id=?", $term_id)
                ->where("component_items.course_id=?", $course_id);

        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {

            $data[$val['eci_id']] = $val['component_name'];
        }
        return $data;
    }

    public function getAllComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("component_items" => "evaluation_components_items_master"), "component_items.ec_id=$this->_name.ec_id", array("eci_id", "term_id", "course_id", "component_name", "weightage"))
                ->where("$this->_name.status != 2")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("component_items.term_id=?", $term_id)
                ->where("component_items.course_id=?", $course_id);

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getEvlComponentCount($academic_year_id, $department_id, $employee_id) {

        $select = $this->_db->select()
                ->from($this->_name)

                //->where("$this->_name.employee_id IS NULL")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("$this->_name.status != 2");
        //echo $select;die;		
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;		
        return $result;
    }

    //rajesh code for weightage
    public function getweightages($academic_year_id, $department_id, $employee_id, $course_id, $component_id, $term_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array("component_items" => "evaluation_components_items_master"), "component_items.ec_id=$this->_name.ec_id", array("eci_id", "term_id", "course_id", "component_name", "weightage"))
                //->where("$this->_name.employee_id IS NULL")
                ->where("$this->_name.academic_year_id=?", $academic_year_id)
                ->where("$this->_name.department_id=?", $department_id)
                ->where("$this->_name.employee_id=?", $employee_id)
                ->where("component_items.course_id=?", $course_id)
                ->where("component_items.term_id=?", $term_id)
                ->where("component_items.eci_id=?", $component_id)
                ->where("$this->_name.status != 2");
        //echo $select;die;		
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;		
        return $result;
    }

    public function getComponentsView($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->joinLeft(array('components_items' => 'evaluation_components_items_master'), "components_items.ec_id=$this->_name.ec_id", array("term_id", "course_id", "GROUP_CONCAT(component_name) as cmp_name", "GROUP_CONCAT(weightage) as weighs", "GROUP_CONCAT(remaining_weightage) as remain_weighs", "eci_id"))
                ->joinLeft(array('term' => 'term_master'), "term.term_id=components_items.term_id", array("term_name"))
                ->joinLeft(array('course' => 'course_master'), "course.course_id=components_items.course_id", array("course_name"))
                ->where("$this->_name.ec_id=?", $id)
                ->group("components_items.term_id")
                ->group("components_items.course_id")
                ->where("$this->_name.status != 2");

        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getCourseId($batch_id, $term_id, $date, $max,$no_of_classes,$section) {
        $join_arr = array();
        for($i=1; $i<=$no_of_classes;$i++){
            $join_arr[] = 'class_'.$i;
        }
       // echo "<pre>"; print_r($join_arr);exit;
        $select = $this->_db->select()
                ->from($this->_name, $join_arr)
                ->where("$this->_name.batch=?", $batch_id)
                ->where("$this->_name.term_id=?", $term_id)
                ->where("$this->_name.section=?", $section)
                ->where("$this->_name.publish=?", (float) $max)
                ->where("$this->_name.date=?", date('d-m-Y', strtotime($date)));
        //echo $select; exit;
        $result = $this->getAdapter()
                ->fetchRow($select);
        //print_r($result);die;		
        return $result;
    }

    public function getMaxVersionOnDate($start_date, $batch,$section) {
        $select = $this->_db->select()
                ->from('batch_scheduler', array(('max(publish) as maxId')))
                ->where("date=?", date('d-m-Y', strtotime($start_date)))
                ->where("batch =?", $batch)
                ->where("section =?", $section);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['maxId'];
    }

    public function getCourseName($course_id) {

        $select = $this->_db->select()
                ->from('course_master', array('course_name', 'course_code', 'course_id'))
                ->where('course_id IN(?)', $course_id)
                ->where("status !=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getFaculty($course_id, $term_id) {
        $select = $this->_db->select()
                ->from('employee_allocation_items_master')
                ->where('term_id =?', $term_id)
                ->where('course_id IN(?)', $course_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result); die;
        return $result;
    }
      
}

?>