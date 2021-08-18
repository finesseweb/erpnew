<?php

class Application_Model_Attendance extends Zend_Db_Table_Abstract {

    public $_name = 'student_attendance';
    protected $_id = 'attendance_id';
    
    
    
    
    
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
    
    
    
    public function getCourseDetailsIn($term_res,$batch_res){
       
          $select = $this->_db->select()
                ->from('application', array("GROUP_CONCAT(course_id) as course"))
                ->where('batch_id in (?)', explode(',',$batch_res))
                ->where('term_id in (?)', $term_res);
        //->where("course_master.status != ?", 2);
 //echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        $row['paper']  = $result[0]['course'];
       $row['back_paper'] = $this->getCourseDetailsInBack($term_res, $batch_res);
        return $row;
    }
    
       public function getCourseDetailsInBack($term_res,$batch_res){
       
          $select = $this->_db->select()
                ->from('application', array("GROUP_CONCAT(course_id_b) as course"))
                ->where('batch_id in (?)', explode(',',$batch_res))
                ->where('term_id_b in (?)', $term_res);
        //->where("course_master.status != ?", 2);
     // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
       
        return $result[0]['course'];
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

    public function getCourseCoordinator($course_id, $batch_id, $term_id) {

        $select = $this->_db->select()
                ->from('employee_allocation_items_master', array('employee_id', 'course_id'))
                ->where('term_id = ?', $term_id)
                ->where('course_id = ?', $course_id);
        //->where("course_master.status != ?", 2);
        // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result[0];
    }

    public function getClass($field, $course_id) {
        $select = $this->_db->select()
                ->from($this->_name, array($field))
                ->where("$field =?", $course_id)
                ->where("$field !=?", 0)
                ->group($field);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }
    
    
    
    public function getPresent($batch, $term, $participant_id,$no_of_classes,$section){
        $absent = 'Absent';
         $select = $this->_db->select();
                $select->from($this->_name,array('date'));
                $select->where('student_id=?', $participant_id);
                $select->where("batch_id=?", $batch);
                $select->where("section=?", $section);
               // ->where("term_id=?", $term)
                for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
                 $select->where("class_$dcl NOT LIKE ?", "Absent%");
                 $select->where("class_$dcl NOT LIKE ?", "Leave%");
                }
       // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
     public function getAbsent($batch, $term, $participant_id,$no_of_classes,$section){
        $absent = 'Absent';
         $select = $this->_db->select();
                $select->from($this->_name, array('date'));
                $select->where('student_id=?', $participant_id);
                $select->where("batch_id=?", $batch);
                $select->where("section=?", $section);
               // ->where("term_id=?", $term)
                 for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
                  $select->where("class_1  LIKE ?", "Absent%");
                 $select->orWhere("class_1  LIKE ?", "Leave%");
                 }
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    

    public function getFacultyId($field, $like, $class, $notEqual = '', $course_id) {
        //  print_r($class);exit;

        if ($notEqual != '') {
            $select = $this->_db->select()
                    ->from($this->_name, array($field))
                    ->where("$class =?", $course_id)
                    ->where("$field LIKE ?", "$like%")
                    ->where("$field !=?", $notEqual)
                    ->group('date');
            //echo $select; exit;
            $result = $this->getAdapter()
                    ->fetchAll($select);
        } else {
            $select = $this->_db->select()
                    ->from($this->_name, array($field))
                    ->where("$class =?", $course_id)
                    ->where("$field LIKE ?", "$like%")
                    ->group('date');
            //echo $select; exit;
            $result = $this->getAdapter()
                    ->fetchAll($select);
        }

        return count($result);
    }

    public function getDate($field) {
        $select = $this->_db->select()
                ->from($this->_name, array($field))
                ->group($field);
        // echo $select;exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function CountFaculty($class, $faculty, $date, $class_value, $faculty_value) {
        $select = $this->_db->select()
                ->from($this->_name, array($faculty, $class))
                ->where("$class =?", $class_value)
                ->where("$faculty =?", $faculty_value)
                ->where("$class !=?", 'Absent')
                ->where("$class !=?", 'Leave')
                ->where("$class !=?", 0)
                ->group($date);
        //echo $select;exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getFaculty($term_id, $batch_id, $course_id) {
        $select = $this->_db->select()
                ->from('employee_allotment_master')
                ->joinLeft(array("allocation_items" => "employee_allocation_items_master"), "allocation_items.ea_id=employee_allotment_master.ea_id", array('employee_id', 'faculty_id', 'visiting_faculty_id'))
                ->joinLeft(array("term" => "term_master"), "term.term_id=allocation_items.term_id")
                ->joinLeft(array("course" => "course_master"), "course.course_id=allocation_items.course_id")
                ->where("employee_allotment_master.academic_year_id=?", $batch_id)
                //->where("allocation_items.department_id=?",$department_id)
                ->where("employee_allotment_master.status != 2")
                ->where("employee_allotment_master.term_id = ?", $term_id)
                ->where("allocation_items.course_id=?", $course_id);
        //echo $select;die;			
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result); die;
        return $result;
    }
    
    
      public function getFeedFaculty($term_id, $batch_id, $course_id,$no_of_classes){
     
        $sel_array = array();
        $join_arr = array('faculty','class');
        foreach($join_arr as $key => $value){
            for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
            $joined_arr[] =$value."_$dcl";      
            }
        } 
                   $select = $this->_db->select();
                           $select->distinct();
                $select->from('student_attendance',$joined_arr);
                $select->where("batch_id=?", $batch_id);
                $select->where("term_id=?", $term_id);
                
                for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
                    if($dcl == 1){
                        $select->where("class_$dcl =?", $course_id);}
                        else {$select->orWhere("class_$dcl =?", $course_id);}
                }
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
              
                
                
        
        
    }

    public function checkDetails($stu_name, $batch_id, $term_id, $date,$section) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_name=?', $stu_name)
                ->where("batch_id=?", $batch_id)
                ->where("term_id=?", $term_id)
                ->where("section=?", $section)
                ->where("date =?", $date);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function checkDetail($stu_name, $batch_id, $term_id, $date, $class,$section) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_name=?', $stu_name)
                ->where("batch_id=?", $batch_id)
                ->where("term_id=?", $term_id)
                ->where("section=?", $section)
                ->where($class . "!=?", '0')
                ->where("date =?", $date);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function getRecords() {
        $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'),new Zend_Db_Expr("distinct stu.date, stu.section, stu.batch_id"))
                ->join(array('term' => 'term_master'), 'term.term_id = stu.term_id')
                ->join(array('batch' => 'academic_master'), 'batch.academic_year_id = stu.batch_id')
                //->where('stu.status !=?',2)
               // ->group(array('stu.date','stu.section'))
                ->order(array('batch.short_code ASC'));
        $results = $this->getAdapter()
                ->fetchAll($select);
      /*  foreach($results as $key => $value){
           $result[] =  $this->getAttendanceViewRecords($value);
        }
        echo "<pre>";print_r($result); exit;*/
        return $results;
    }
    
    
    public function getAttendanceViewRecords($values){
         $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'),array('DISTINCT(stu.section)'))
                ->join(array('term' => 'term_master'), 'term.term_id = stu.term_id')
                ->join(array('batch' => 'academic_master'), 'batch.academic_year_id = stu.batch_id')
                ->where('stu.date =?','11-07-2019')
               // ->group(array('stu.date','stu.section'))
                ->order(array('batch.short_code ASC'));
         //echo $select ; exit;
        $results = $this->getAdapter()
                ->fetchAll($select);
        foreach($results as $key => $new_val){
            $result[] = $new_val['date'];
        }
       // echo "<pre>";print_r($result); exit;
        $result['date'] = array_unique($result);
        return $result;
    
    }
    
    public function viewRecord($date_arr){
          $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'))
                ->join(array('term' => 'term_master'), 'term.term_id = stu.term_id')
                ->join(array('batch' => 'academic_master'), 'batch.academic_year_id = stu.batch_id')
                ->where('stu.date =?',$values['section'])
                ->where('stu.term_id =?',$values['term_id'])
                ->where('stu.batch_id =?',$values['batch_id'])
               // ->group(array('stu.date','stu.section'))
                ->order(array('batch.short_code ASC'));
         //echo $select ; exit;
        $results = $this->getAdapter()
                ->fetchAll($select);
    }

    public function getRecordById($id,$section, $no_of_class) {
        $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'))
                ->where('section =?', $section)
                ->where("stu.date =?", $id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        
        
        $all_details = $this->getRecordByResult($result,$section,$no_of_class);
        // echo "<pre>";print_r($all_details);echo "</pre>";exit;
        return $all_details;
    }

    public function getRecordByStudentId($id, $course_id, $term_id, $batch_id, $class) {
        $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'))
                //->joinLeft(array('batch' => 'batch_scheduler'),'batch.date = stu.date')
                ->where("stu." . $class . " =?", $course_id)
                ->where("stu.term_id =?", $term_id)
                ->where("stu.batch_id =?", $batch_id)
                ->where("stu.student_id =?", $id);
        // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        //  echo $select;exit;
        return count($result);
        $all_details = $this->getRecordByResult($result);
        // echo "<pre>";print_r($all_details);echo "</pre>";exit;
        return $all_details;
    }
    
    
    
        public function getRecordByStudentIdByMonth($id, $course_id, $term_id, $batch_id, $class,$month, $year,$section) {
           
          if(strlen($month)==1)
              $month = '0'.$month; 
        $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'))
                //->joinLeft(array('batch' => 'batch_scheduler'),'batch.date = stu.date')
                ->where("stu." . $class . " =?", $course_id)
                ->where("stu.term_id =?", $term_id)
                ->where("stu.date LIKE ?", "%_%_%_$month-$year%")
                ->where("stu.batch_id =?", $batch_id)
               // ->where("stu.section =?", $section)
                ->where("stu.student_id =?", $id);
    
        $result = $this->getAdapter()
                ->fetchAll($select);
       //echo $select;exit;
        return count($result);
        
        $all_details = $this->getRecordByResult($result);
        // echo "<pre>";print_r($all_details);echo "</pre>";exit;
        return $all_details;
    }
    
    
    
     public function getDatesByStudentIdByMonth($id, $course_id, $term_id, $batch_id, $class,$month, $year){
          if(strlen($month)==1)
              $month = '0'.$month; 
        $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'))
                ->where("stu." . $class . " =?", $course_id)
                ->where("stu.term_id =?", $term_id)
                ->where("stu.date LIKE ?", "%_%_%_$month-$year%")
                ->where("stu.batch_id =?", $batch_id)
                ->where("stu.student_id =?", $id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getRecordByStudentIdUpdateByMonth($id, $course_id, $term_id, $batch_id, $class,$month) {
        $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'), array('updated_date'))
                ->where("stu." . $class . " =?", $course_id)
                ->where("stu.term_id =?", $term_id)
                ->where("stu.batch_id =?", $batch_id)
                ->where("stu.student_id =?", $id)
                ->group("stu.student_id");
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['updated_date'];
        $all_details = $this->getRecordByResult($result);
        return $all_details;
    }   
    

    public function getRecordByStudentIdUpdate($id, $course_id, $term_id, $batch_id, $class) {
        $select = $this->_db->select()
                ->from(array('stu' => 'student_attendance'), array('updated_date'))
                //->joinLeft(array('batch' => 'batch_scheduler'),'batch.date = stu.date')
                ->where("stu." . $class . " =?", $course_id)
                ->where("stu.term_id =?", $term_id)
                ->where("stu.batch_id =?", $batch_id)
                ->where("stu.student_id =?", $id)
                ->group("stu.student_id");
        // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchRow($select);
        //  echo $select;exit;
        return $result['updated_date'];
        $all_details = $this->getRecordByResult($result);
        // echo "<pre>";print_r($all_details);echo "</pre>";exit;
        return $all_details;
    }

    public function getRecordByResult($result,$section,$cl_no) {
        $data_arr = array();
        
        for($dcl = 1 ; $dcl<=$cl_no; $dcl++){
        ${"data_class_$dcl"} = array();
        }
        
        $date_class_1 = array();
        $i = 0;
        
        while ($i < count($result)) {
            $data_arr[$i] = $result[$i];
             for($dcl = 1 ; $dcl<=$cl_no; $dcl++){
            ${"data_class_$dcl"}[$i] = $result[$i]["class_$dcl"];
             }
            $i++;
        }


        //
        
        for($dcl = 1 ; $dcl<=$cl_no; $dcl++){
        ${"data_class_$dcl"} = $this->getAllCourseDetails(${"data_class_$dcl"});
        }

        $j = 0;
        while ($j < count($result)) {
            for($dcl = 1 ; $dcl<=$cl_no; $dcl++){
            $result[$j]["class_$dcl"] = ${"data_class_$dcl"}[$j];
            }
            $j++;
        }
        return $result;
    
    }

    public function getId($start_date, $version) {

        // $start_date = '11-07-2016';
        $select = $this->_db->select()
                ->from('batch_scheduler', array('batch_schedule_id'))
                ->where('date=?', $start_date)
                ->where("publish=?", (float) $version);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getAllDescriptionDetails1($data_class_1, $field, $term) {

        $temp = '0';
        for ($i = 0; $i < count($data_class_1); $i++) {
            $select = $this->_db->select()
                    ->from('batch_scheduler', array('description_1'))
                    ->where('term_id=?', $term[$i])
                    ->where('publish=?', 0.2)
                    ->where('date=?', $data_class_1[$i]);
            // ->where('status !=?',2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;

            $data_class_1[$i] = $result[0]['description_1'];
        }

        return $data_class_1;
    }

    public function getAllDescriptionDetails2($data_class_1, $field, $term) {

        $temp = '0';
        for ($i = 0; $i < count($data_class_1); $i++) {
            $select = $this->_db->select()
                    ->from('batch_scheduler', array('description_2'))
                    ->where('term_id=?', $term[$i])
                    ->where('publish=?', 0.2)
                    ->where('date=?', $data_class_1[$i]);
            // ->where('status !=?',2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;

            $data_class_1[$i] = $result[0]['description_2'];
        }

        return $data_class_1;
    }

    public function getAllDescriptionDetails3($data_class_1, $field, $term) {

        $temp = '0';
        for ($i = 0; $i < count($data_class_1); $i++) {
            $select = $this->_db->select()
                    ->from('batch_scheduler', array('description_3'))
                    ->where('term_id=?', $term[$i])
                    ->where('publish=?', 0.2)
                    ->where('date=?', $data_class_1[$i]);
            // ->where('status !=?',2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;

            $data_class_1[$i] = $result[0]['description_3'];
        }

        return $data_class_1;
    }

    public function getAllDescriptionDetails4($data_class_1, $field, $term) {

        $temp = '0';
        for ($i = 0; $i < count($data_class_1); $i++) {
            $select = $this->_db->select()
                    ->from('batch_scheduler', array('description_4'))
                    ->where('term_id=?', $term[$i])
                    ->where('publish=?', 0.2)
                    ->where('date=?', $data_class_1[$i]);
            // ->where('status !=?',2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;

            $data_class_1[$i] = $result[0]['description_4'];
        }

        return $data_class_1;
    }

    public function getAllDescriptionDetails5($data_class_1, $field, $term) {

        $temp = '0';
        for ($i = 0; $i < count($data_class_1); $i++) {
            $select = $this->_db->select()
                    ->from('batch_scheduler', array('description_5'))
                    ->where('term_id=?', $term[$i])
                    ->where('publish=?', 0.2)
                    ->where('date=?', $data_class_1[$i]);
            // ->where('status !=?',2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;

            $data_class_1[$i] = $result[0]['description_5'];
        }

        return $data_class_1;
    }

    public function getAllCourseDetails($data_class) {

        $temp = '0';
        for ($i = 0; $i < count($data_class); $i++) {
            $select = $this->_db->select();
                    $select->from('course_master', array('course_code'));
                    $select->where('course_id =?', $data_class[$i]);
                    $select->where('status !=?', 2);
            
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;
            if (!empty($result))
                $data_class[$i] = $result[0]['course_code'];
        }
       // if(!empty($result)){
        //echo "<pre>";print_r($data_class); exit;}
        return $data_class;
    }

    public function getAllCourseDetails2($data_class_2) {

        $temp = '0';
        for ($i = 0; $i < count($data_class_2); $i++) {
            $select = $this->_db->select()
                    ->from('course_master', array('course_code'))
                    ->where('course_id =?', $data_class_2[$i])
                    ->where('status !=?', 2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            //print_r($result);exit;
            if (!empty($result))
                $data_class_2[$i] = $result[0]['course_code'];
        }


        return $data_class_2;
    }

    public function getAllCourseDetails3($data_class_3) {

        $temp = '0';
        for ($i = 0; $i < count($data_class_3); $i++) {
            $select = $this->_db->select()
                    ->from('course_master', array('course_code'))
                    ->where('course_id =?', $data_class_3[$i])
                    ->where('status !=?', 2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;
            if (!empty($result))
                $data_class_3[$i] = $result[0]['course_code'];
                }


        return $data_class_3;
    }

    public function getAllCourseDetails4($data_class_4) {
        $temp = '0';
        for ($i = 0; $i < count($data_class_4); $i++) {
            $select = $this->_db->select()
                    ->from('course_master', array('course_code'))
                    ->where('course_id =?', $data_class_4[$i])
                    ->where('status !=?', 2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;
            if (!empty($result))
                $data_class_4[$i] = $result[0]['course_code'];
        }

        return $data_class_4;
    }

    public function getAllCourseDetails5($data_class_5) {
        $temp = '0';
        for ($i = 0; $i < count($data_class_5); $i++) {
            $select = $this->_db->select()
                    ->from('course_master', array('course_code'))
                    ->where('course_id =?', $data_class_5[$i])
                    ->where('status !=?', 2);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // print_r($result);exit;
            if (!empty($result))
                $data_class_5[$i] = $result[0]['course_code'];
        }

        return $data_class_5;
    }

    public function getAttendanceResult($term_id, $batch_id, $course_id, $date_val,$section) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("batch_id=?", $batch_id)
                ->where("term_id=?", $term_id)
                ->where("section =?", $section)
                ->where("date =?", $date_val);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getCourseDetails($term_id, $batch_id,$empl_id='') {
           // print_r($term_id);exit;
            
        if($_SESSION['admin_login']['admin_login']->empl_id)
            $empl_id = $_SESSION['admin_login']['admin_login']->empl_id;
        
        
        if ($empl_id == '') {

            $select = $this->_db->select()
                    ->from('employee_allotment_master')
                    ->joinLeft(array("allocation_items" => "employee_allocation_items_master"), "allocation_items.ea_id=employee_allotment_master.ea_id", array('term_id', 'course_id', 'department_id', 'employee_id'))
                    ->joinLeft(array("term" => "term_master"), "term.term_id=allocation_items.term_id", array("term_id as term", "term_name"))
                    ->joinLeft(array("course" => "course_master"), "course.course_id=allocation_items.course_id", array("course_id as course", "course_name", 'course_code'))
                    ->where("employee_allotment_master.academic_year_id=?", $batch_id)
                    //->where("allocation_items.department_id=?",$department_id)
                    ->where("employee_allotment_master.status != 2")
                    ->where("employee_allotment_master.term_id = ?", $term_id);

            //echo $select;die;		
            $result = $this->getAdapter()
                    ->fetchAll($select);
            //print_r($result); die;
            return $result;
        }
        $select = $this->_db->select()
                ->from('employee_allotment_master')
                ->joinLeft(array("allocation_items" => "employee_allocation_items_master"), "allocation_items.ea_id=employee_allotment_master.ea_id", array('term_id', 'course_id', 'department_id', 'employee_id'))
                ->joinLeft(array("term" => "term_master"), "term.term_id=allocation_items.term_id", array("term_id as term", "term_name"))
                ->joinLeft(array("course" => "course_master"), "course.course_id=allocation_items.course_id", array("course_id as course", "course_name", 'course_code'))
                ->where("employee_allotment_master.academic_year_id=?", $batch_id)
                //->where("allocation_items.department_id=?",$department_id)
                ->where("employee_allotment_master.status != 2")
                ->where("employee_allotment_master.term_id = ?", $term_id)
                ->where("allocation_items.employee_id=?", $empl_id);
        //echo $select;die;			
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result); die;
        return $result;
    }
    
    
    function getFacultyCourseForAllTerms($empl_id){
        
        $select = $this->_db->select()
                ->from('employee_allotment_master')
                ->joinLeft(array("allocation_items" => "employee_allocation_items_master"), "allocation_items.ea_id=employee_allotment_master.ea_id", array('term_id', 'course_id', 'department_id', 'employee_id'))
                ->joinLeft(array("term" => "term_master"), "term.term_id=allocation_items.term_id", array("term_id as term", "term_name"))
                ->joinLeft(array("course" => "course_master"), "course.course_id=allocation_items.course_id", array("course_id as course", "course_name", 'course_code'))
                //->where("allocation_items.department_id=?",$department_id)
                ->where("employee_allotment_master.status != 2")
                ->where("allocation_items.employee_id=?", $empl_id);
        //echo $select;die;			
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result); die;
        return $result;
        
        
    }
    
    public function getCourseDetailsByCourseId($id){
        
        // $start_date = '11-07-2016';
        $select = $this->_db->select('*')
                ->from('course_master')
                ->where("course_id=?", $id)
                ->where("status !=?",2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result);exit;
        return $result;
        
        
    }

    public function getMaxVersionOnDate($start_date, $batch,$section) {
        $select = $this->_db->select()
                ->from('batch_scheduler', array(('max(publish) as maxId')))
                ->where("date=?", $start_date)
                ->where("section = ?",$section)
                ->where("batch =?", $batch);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['maxId'];
    }

    public function getTermIdAndBatchId($start_date, $version) {

        // $start_date = '11-07-2016';
        $select = $this->_db->select()
                ->from('batch_scheduler', array('term_id', 'batch'))
                ->where('date=?', $start_date)
                ->where("publish=?", (float) $version);
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result);exit;
        return $result;
    }

    public function getCourses($courses_id) {
        $arr = explode(',', $courses_id);

        $course_code = array();
        $course_id = array();
        $mai_result = array();
        if (count($arr) == 1) {
            $select = $this->_db->select()
                    ->from('course_master')
                    ->where("course_id = ?", $courses_id);
            $result = $this->getAdapter()
                    ->fetchAll($select);
            $course_code[0] = $result[0]['course_code'];
            $course_id[0] = $result[0]['course_id'];
        } else if (count($arr) > 1) {
            for ($i = 0; $i < count($arr); $i++) {
                $select = $this->_db->select()
                        ->from('course_master')
                        ->where("course_id = ?", $arr[$i]);
                $result = $this->getAdapter()
                        ->fetchAll($select);
                $course_code[$i] = $result[0]['course_code'];
                $course_id[$i] = $result[0]['course_id'];
            }
        }

        if (count($course_code) > 1) {
            $course_code = array_unique($course_code);
            $course_id = array_unique($course_id);
        }
        $main_result[0]['course_code'] = $course_code;
        $main_result[0]['course_id'] = $course_id;
        return $main_result;
    }

    public function getStudentList($term_id, $batch_id, $course_id) {
        $select = $this->_db->select()
                ->from(array("stu_info" => "erp_student_information"))
             //->join(array("grade"=>"grade_sheet"),"grade.student_id=stu_info.stu_id")
                ->where("stu_info.academic_id = ?", $batch_id)
                //->where("Allotment.term_id = ?",$term_id)
                ->where("stu_info.stu_status != ?", 2);
       // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    //=============================edited by satyam 22-04-2019=================
  /*   public function getStudentId($student_id)
     {
         $sql= "select * from grade_sheet";
         $result= db_query($sql);
         while($myrow = db_fetch($result))
             $student_id1= $myrow["student_id"];
         if($student_id==$student_id1)
         {
            echo "<script>alert('Your Grade Sheet is not Generated yet')</script>";
            die;
         }
         
     }  */
    //=========================================================================

    public function getAllDateDetails($term_id, $batch_id, $course_id, $start_date, $version, $no_of_classes,$sections){
        //  $start_date = '11-07-2016';
       
        $sql_arr = array();
        
        for ($i = 1; $i <= $no_of_classes; $i++){
            $sql  = '';
            $select = $this->_db->select();
                   $select ->from('batch_scheduler');
                    $select->where("batch=?", $batch_id);
                    $select->where("term_id =?", $term_id);
                    $select->where("date = ?", $start_date);
                    
                   
                    foreach($version as $key => $value){
                        if($key==0){
                            $sql .= " publish = ". (float) $value; 
                            $sql .= " AND section = '".$sections[$key]['id']."'";
                        }
                        else {
                            $sql .= " or publish = ". (float)$value;      
                            $sql .= " AND section = '". $sections[$key]['id']."'";
                        
                        }
                    }
					
                 
                    foreach($course_id as $key_course => $course_val){
                        if($key_course == 0)
                        $sql .= " AND (class_" . $i . " = ". $course_val;
                        else
                            $sql.= " or class_" . $i . " = ". $course_val;
                            
                    }
                    $sql .= ")";
                    $select->where($sql);
                // echo $select; die;
            $result = $this->getAdapter()
                    ->fetchAll($select);
            // $main[$j][$i] =count($result);
          

            if (count($result) > 0) {
                for ($k = 1; $k <= $no_of_classes; $k++) {
                    $courseMethod = "getAllCourseDetails";
                    foreach($result as $key => $value){
                    $course[0] = $value["class_$k"];
                    //    $main[0][$inc_date][$k] = $this->$courseMethod($course)[0];
                    $main[0][$key][$k]['class'] = $value["class_$k"] . "-" . $this->$courseMethod($course)[0];
                    $main[0][$key][$k]['section'] = $value["section"];
                    $main[0][$key][$k]['time'] = $value["time_$k"];
                    $main[0][$key][$k]['version'] = $value["publish"];
                    $main[0][$key][$k]['batch_schedule_id'] = $value["batch_schedule_id"];
                    
                    }
                }
            }
        }
        return $main;
    }
    
    
    public function getAllDateDetailsWeekly($term_id, $batch_id, $course_id, $start_date, $version, $no_of_classes,$sections){
        //  $start_date = '11-07-2016';
  
        $sql_arr = array();
        $withAllsection = array();
        $select_val = array('distinct(day) as day','term_id','batch','section','publish');
        for ($i = 1; $i <= $no_of_classes; $i++){
            $select_val[] = 'class_'.$i;
            $select_val[] = 'time_'.$i;
            $select_val[] = 'room_'.$i;
        }
        
        for ($i = 1; $i <= $no_of_classes; $i++){
            $sql  = '';
            $select = $this->_db->select();
                   $select ->from('batch_scheduler',$select_val);
                    $select->where("batch=?", $batch_id);
                    $select->where("term_id =?", $term_id);
                    $select->where("publish =?", (float)$version);
                    $select->where("section =?", $sections);
                    $j = 0;
                    foreach($course_id as $key_course => $course_val){
                        if($j == 0){
                        $sql .= " ( class_" . $i . " = ". $course_val ." )";}
                        else{
                        $sql.= " or (class_" . $i . " = ". $course_val ." )";}
                       
                          $j++;  
                    }
            $result = $this->getAdapter()
                    ->fetchAll($select);
         
          
            if (count($result) > 0) {
                for ($k = 1; $k <= $no_of_classes; $k++) {
                    $courseMethod = "getAllCourseDetails";
                    foreach($result as $key => $value){
                      
                    $course[0] = $value["class_$k"];
                   
                    $main[$value["day"]][$k]["class"] = $value["class_$k"] . "-" . $this->$courseMethod($course)[0];
                    $main[$value["day"]][$k]['section'] = $this->getsectionById($value["section"]);
                    $main[$value["day"]][$k]['section_id'] = $value["section"];
                    $main[$value["day"]][$k]['time'] = $value["time_$k"];
                    $main[$value["day"]][$k]['room'] = $value["room_$k"];
                    $main[$value["day"]][$k]['publish'] = $value["publish"];
                    $main[$value["day"]][$k]['day'] = $value["day"];
                    $main[$value["day"]][$k]['term'] =$this->gettermById($value["term_id"]); 
                  
                    }
                   
                                
                }
               
         
            }
        }
    
           
        return $main;
    }

    
       public function getsectionById($id){
        
            $select = $this->_db->select()
                ->from('section_master',array('name'))
                ->where("id=?", $id)
            ->order(array('name'));
                //->where("$this->_name.status !=?", 2);

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['name'];
        
    }
    
    
       public function gettermById($id){
        
            $select = $this->_db->select()
                ->from('term_master',array('term_name'))
                ->where("term_id=?", $id)
            ->order(array('term_name'));

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['term_name'];
        
    }
    
    
    

    public function getDateDetails($term_id, $batch_id, $course_id ,$section, $cl_no) {
        $date_details = $this->getFirstAndLastDate($term_id, $batch_id);
        $term_start = explode("/", $date_details['start_date']);
        $term_end = explode("/", $date_details['end_date']);
        $start = $term_start[2] . "-" . $term_start[1] . "-" . $term_start[0];
        $end = $term_end[2] . "-" . $term_end[1] . "-" . $term_end[0];
        $date = strtr($date_details['start_date'], '/', '-');
        $day_num = $this->date_diff2($start, $end);
        // print_r($day_num);exit;
        $k = $l = 0;
        $main = array();
        for ($j = 0; $j < (int) $day_num; $j++) {
            $All_courses_date_arr = array();
            $inc_date = date("d-m-Y", strtotime($date . ' + ' . $j . ' days'));
            for ($i = 1; $i <= $cl_no; $i++) {
                $select = $this->_db->select()
                        ->from('batch_scheduler')
                        ->where("batch=?", $batch_id)
                        ->where("term_id =?", $term_id)
                        ->where("section =?", $section)
                        ->where("date = ?", $inc_date)
                        ->where("class_" . $i . " =?", $course_id);
           
                $result = $this->getAdapter()
                        ->fetchAll($select);
                // $main[$j][$i] =count($result);

                if (count($result) > 0) {
                    $main[$j][$i] = $inc_date;
                    $All_courses_date_arr[$j][$i] = $inc_date;
                }
            }
        }
     //   echo "<pre>";print_r($main);exit;
        return $main;
    }

    public function date_diff2($date1 = '', $date2 = '') {


        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff ) / (60 * 60 * 24));
        return (int) $days;
    }

    public function getFirstAndLastDate($term_id, $batch_id) {
        //$this->getlatestVersion($term_id,$batch_id);
        $select = $this->_db->select()
                ->from('term_master', array('start_date', 'end_date'))
                ->where("academic_year_id=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where('status !=?', 2);
        // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function connectBatchSheduler($term_id, $batch_id, $course_id, $date_val,$section,$cl_no) {
        $arr = array();
        for($i = 1; $i<=$cl_no; $i++){
            $arr[$i] = 0;
        }
        for ($i = 1; $i <= $cl_no; $i++) {
            $select = $this->_db->select()
                    ->from('batch_scheduler')
                    ->where("batch=?", $batch_id)
                    ->where("term_id =?", $term_id)
                    ->where("section =?", $section)
                    ->where("class_" . $i . " =?", $course_id)
                    ->where("date =?", $date_val);
            // echo $select; exit;
            $result = $this->getAdapter()
                    ->fetchAll($select);
            $j = 0;
            if (count($result) > 0) {
                $arr[$i] = $j + 1;
            }
        }

        return $arr;
    }

    public function getRecordByBatchAndTerm($term_id, $batch_id,$no_of_classes,$section) {

        $select_array = array (
            "distinct(date)"
        );
        
        for($i = 1; $i<= $no_of_classes; $i++){
            $select_array[$i] = 'class_'.$i;
        }
        
        $select = $this->_db->select()
                ->from($this->_name, $select_array)
                ->where("batch_id=?", $batch_id)
                ->where("term_id =?", $term_id)
                ->where("section =?", $section)
                ->where('status !=?', 2)
                ->group('date');
      // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

}
