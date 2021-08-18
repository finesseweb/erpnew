<?php

class TimetableController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
	Private $_unit_id = null;
        private $accessConfig =NULL;


    public function init() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $this->_action = $this->getRequest()->getActionName();
        //access role id
        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");					
        $this->login_storage = $data = $storage->admin_login;
        $this->view->login_storage = $data;
        //print_r($data);exit;
        if( isset($data) ){
                $this->view->role_id = $data->role_id;
                $this->view->login_empl_id = $data->empl_id;
        }
        if ($this->_action == "login" || $this->_action == "forgot-password") {
            $this->_helper->layout->setLayout("adminlogin");
        } else {
            $this->_helper->layout->setLayout("layout");
        }
        $this->_act = new Application_Model_Adminactions();
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();
		
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        if($data->role_id == 0)
         //   $this->_redirect('student-portal/student-dashboard');
        if (!$data && $this->_action != 'login' &&
                $this->_action != 'forgot-password') {
            $this->_redirect('index/login');
            return;
        }
        if ($this->_action != 'forgot-password') {
            $this->_authontication = $data;
            $this->_agentsdata = $storage->agents_data;
        }
    }

            public function indexAction() {
                $storage = new Zend_Session_Namespace("admin_login");
                $this->view->action_name = 'timetable';
                $this->view->sub_title_name = 'timetable';
                $this->accessConfig->setAccess('SA_ACAD_TIME_TABLE',$storage->admin_login->role_id);
                $routine_model = new Application_Model_BatchSchedule(); 
                $form = new Application_Form_Routine();
                $batch_form = new Application_Form_BatchTerm(); 
                $this->view->type = $type;
                $this->view->form = $form;
                $this->view->common_field = $batch_form;
            }
            
            public function ajaxGetFacultyWiseViewAction(){
                $this->_helper->layout->disableLayout();
                if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
                    //==class objects 
                    $attendance_model = new Application_Model_Attendance();
                    $class_model = new Application_Model_ClassMaster();
                    $batch_schedule = new Application_Model_BatchSchedule();
                    $academic_model = new Application_Model_Academic();
                    $room_model = new Application_Model_Room();
                    $erp = new Application_Model_HRMModel();
                        
                     
                     //====user param
                     $faculty_id = $this->_getParam("faculty");
                      $batch = $this->_getParam("batch");
                     $term = $this->_getParam("term_id");
                     $date = date('d-m-Y');
                     
                     //====fetch values through database
                     
                     $max_no_of_classes = $class_model->getMaxClass();
                     $courses = $attendance_model->getFacultyCourseForAllTerms($faculty_id);
                     
                     $field_name[] = 'course_id';
                     
                     $course_id = $this->filterData($courses,$field_name,count($courses));
                     if(empty($batch) || empty($term))
                      $version_details  = $batch_schedule->getAllversion();
                      else
                      $version_details = $batch_schedule->getAllversionWithBatchTerm($batch, $term);
                     
                     $course = array();
                     foreach($course_id as $key => $value){
                         $course[] = $value['course_id'];  
                     }
                     $course_arr = $course;
                     
          foreach($version_details as $key => $version_detail){
              $no_of_classes = $max_no_of_classes;
              
                $result[$academic_model->getBatchCodeRecord($version_detail['batch'])['short_code']][$version_detail['term_id']][$version_detail['section']] = $attendance_model->getAllDateDetailsWeekly($version_detail['term_id'], $version_detail['batch'], $course, date('d-m-Y'), $version_detail['max_version'], $no_of_classes,$version_detail['section']);  
            }
            
         
            
         //   echo "<pre>";print_r($result);exit;
       
            $new_faculty_routine = array();
            $result['days'] = $this->weekdays(); 
            
             $result['weekends'] =   explode(',',$erp->getWeeklyOff()[0]['option_value']);
             foreach($result['days'] as $key => $value ) {
                            foreach($result as $batch => $term_value){
                                foreach($term_value as $term_id => $section_value){
                                    foreach ($section_value as $section_id => $routine_value){
                                        
                                for($class = 1; $class<=count($routine_value[$value]); $class++){
                                   $course = !empty($routine_value[$value][$class]['class'])? explode('-',$routine_value[$value][$class]['class']):'' ;
                                   if(!in_array($course[0], $course_arr))
                                      continue;
                                    $new_faculty_routine[$key][$class]['course_code'][] =  $course[1];
                                    if(empty($batch_id) || empty($term_id)){
                                    $new_faculty_routine[$key][$class]['batch'][] = $batch;
                                    $new_faculty_routine[$key][$class]['term'][] = $routine_value[$value][$class]['term'];
                                    }
                                    $new_faculty_routine[$key][$class]['section'][] = $routine_value[$value][$class]['section'];
                                    if(!is_null($routine_value[$value][$class]['room'])){
                                    $new_faculty_routine[$key][$class]['room'][] = $room_model->getRecord($routine_value[$value][$class]['room'])['room_number'];}
                                    else{
                                    $new_faculty_routine[$key][$class]['room'][] = '';}
                                    $new_faculty_routine[$key][$class]['time'][] = $routine_value[$value][$class]['time'];
                                }

                            }
                            }

             }
             }
             
           $class_records = $class_model->getRecords();
             $class_records = $this->mergData($class_records, array('class_name'),count($class_records));
             $this->view->classrecords = $class_records ;
            $this->view->no_of_classes = $max_no_of_classes ;
            $this->view->result = $result;  
            $this->view->faculty_routine = $new_faculty_routine;
                     
            }
                
            }
            
            
            
            public function ajaxGetRoomAction(){
                 //$room_model = new Application_Model_Room();
                $room_model =  new Application_Model_RoomMapping();
                    $this->_helper->layout->disableLayout();
                if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
                     $department = $this->_getParam("department");
                $rooms = $room_model->getRoomByDepartmentId($department);
              
                // echo '<option value ="">--Select--</option>';
                foreach($rooms as $key => $room_info){
                    echo "<option value = ".$room_info['room_id']." >".$room_info['room_number']." </option>";
                    
                }
                die;
                }
                
                
            }
            
            
            public function ajaxGetRoomWiseViewAction(){
                $this->_helper->layout->disableLayout();
                if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
                    //==class objects 
                    $attendance_model = new Application_Model_Attendance();
                    $class_model = new Application_Model_ClassMaster();
                    $batch_schedule = new Application_Model_BatchSchedule();
                    $academic_model = new Application_Model_Academic();
                    $room_model = new Application_Model_Room();
                    $erp = new Application_Model_HRMModel();
                        
                     
                     //====user param
                     $room = $this->_getParam("room");
                     $batch = $this->_getParam("batch");
                     $term = $this->_getParam("term_id");
                     $date = date('d-m-Y');
                     
                     //====fetch values through database
                     
                     $max_no_of_classes = $class_model->getMaxClass();
                     
                     if(empty($batch) || empty($term))
                      $version_details  = $batch_schedule->getAllversion();
                      else
                      $version_details = $batch_schedule->getAllversionWithBatchTerm($batch, $term);
                      $courses = array();
                      foreach($version_details as $key => $version_detail){
                        $courses[$version_detail['max_version']] = $batch_schedule->getCourseRoomWise($room,$max_no_of_classes,$version_detail['max_version']);
                      }
                      
                     
                      
                      for($i = 1; $i<= $max_no_of_classes; $i++){
                        $field_name[] = "class_$i";
                        $room_name[] = "room_$i";
                      }
                      
                     
                      foreach($courses as $key => $value){
                     $course_id[$key]['class'] = $this->filterData($value,$field_name,count($value));
                     $course_id[$key]['room'] = $this->filterData($value,$room_name,count($value));
                      }
                      
                      
                     
                     $course = array();
                     foreach($course_id as $key => $value){
                         foreach($value['room'] as $room_key => $room_val){
                             foreach($room_val as $val_key => $val_value){
                            if($val_value == $room){
                               $arr = explode('_', $val_key);
                                $course[] = $value['class'][$room_key]["class_$arr[1]"];
                                }
                             }
                         }
                     }
                   $course = array_unique($course);
                   if(count($course) == 0)
                       $course = array(0);
          foreach($version_details as $key => $version_detail){
              $no_of_classes = $max_no_of_classes;
             // echo $no_of_classes; exit;
                $result[$academic_model->getBatchCodeRecord($version_detail['batch'])['short_code']][$version_detail['term_id']][$version_detail['section']]  = $attendance_model->getAllDateDetailsWeekly($version_detail['term_id'], $version_detail['batch'], $course, date('d-m-Y'), $version_detail['max_version'], $no_of_classes,$version_detail['section']);  
            }
            $new_faculty_routine = array();
            $result['days'] = $this->weekdays(); 
            
             $result['weekends'] =   explode(',',$erp->getWeeklyOff()[0]['option_value']);
             foreach($result['days'] as $key => $value ) {
                           foreach($result as $batch => $term_value){
                                foreach($term_value as $term_id => $section_value){
                                    foreach ($section_value as $section_id => $routine_value){
                                        for($class = 1; $class<=count($routine_value[$value]); $class++){
                                            if($room != $routine_value[$value][$class]['room'])
                                                continue;
                                                    $course = !empty($routine_value[$value][$class]['class'])? explode('-',$routine_value[$value][$class]['class']):'' ;
                                                     $new_faculty_routine[$key][$class]['course_code'][] =  $course[1];
                                                     $new_faculty_routine[$key][$class]['batch'][] = $batch;
                                                     $new_faculty_routine[$key][$class]['term'][] = $routine_value[$value][$class]['term'];
                                                     $new_faculty_routine[$key][$class]['section'][] = $routine_value[$value][$class]['section'];
                                                     if(!is_null($routine_value[$value][$class]['room'])){
                                                     $new_faculty_routine[$key][$class]['room'][] = $room_model->getRecord($routine_value[$value][$class]['room'])['room_number'];}
                                                     else{
                                                     $new_faculty_routine[$key][$class]['room'][] = '';}
                                                     $new_faculty_routine[$key][$class]['time'][] = $routine_value[$value][$class]['time'];
                                            
                                }

                            }
                           }
                           }

             }
            $class_records = $class_model->getRecords();
             $class_records = $this->mergData($class_records, array('class_name'),count($class_records));
             $this->view->classrecords = $class_records ;
            $this->view->no_of_classes = $max_no_of_classes ;
            $this->view->result = $result;  
            $this->view->faculty_routine = $new_faculty_routine;
                     
            }
                
            }
            public function ajaxGetCourseWiseViewAction(){
                $this->_helper->layout->disableLayout();
                if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
                    //==class objects 
                    $attendance_model = new Application_Model_Attendance();
                    $class_model = new Application_Model_ClassMaster();
                    $batch_schedule = new Application_Model_BatchSchedule();
                    $academic_model = new Application_Model_Academic();
                    $room_model = new Application_Model_Room();
                    $erp = new Application_Model_HRMModel();
                        
                     
                     //====user param
                     $course_param = $this->_getParam("course_id");
                     $date = date('d-m-Y');
                     
                     //====fetch values through database
                     
                     $max_no_of_classes = $class_model->getMaxClass();
                      $version_details  = $batch_schedule->getAllversion();
                      $course[] = $course_param;
                   
          foreach($version_details as $key => $version_detail){
              $no_of_classes = $max_no_of_classes;
                $result[$academic_model->getBatchCodeRecord($version_detail['batch'])['short_code']][$version_detail['term_id']][$version_detail['section']]  = $attendance_model->getAllDateDetailsWeekly($version_detail['term_id'], $version_detail['batch'], $course, date('d-m-Y'), $version_detail['max_version'], $no_of_classes,$version_detail['section']);  
            }
            $new_faculty_routine = array();
            $result['days'] = $this->weekdays();
            
             $result['weekends'] =   explode(',',$erp->getWeeklyOff()[0]['option_value']);
             foreach($result['days'] as $key => $value ) {
                           foreach($result as $batch => $term_value){
                                foreach($term_value as $term_id => $section_value){
                                    foreach ($section_value as $section_id => $routine_value){
                                        for($class = 1; $class<=count($routine_value[$value]); $class++){
                                            $course = !empty($routine_value[$value][$class]['class'])? explode('-',$routine_value[$value][$class]['class']):'' ;
                                                    if($course_param != $course[0])
                                                         continue;
                                                     $new_faculty_routine[$key][$class]['course_code'][] =  $course[1];
                                                     $new_faculty_routine[$key][$class]['batch'][] = $batch;
                                                     $new_faculty_routine[$key][$class]['term'][] = $routine_value[$value][$class]['term'];
                                                     $new_faculty_routine[$key][$class]['section'][] = $routine_value[$value][$class]['section'];
                                                     if(!is_null($routine_value[$value][$class]['room'])){
                                                     $new_faculty_routine[$key][$class]['room'][] = $room_model->getRecord($routine_value[$value][$class]['room'])['room_number'];}
                                                     else{
                                                     $new_faculty_routine[$key][$class]['room'][] = '';}
                                                     $new_faculty_routine[$key][$class]['time'][] = $routine_value[$value][$class]['time'];
                                            
                                }

                            }
                           }
                           }

             }
             $class_records = $class_model->getRecords();
             $class_records = $this->mergData($class_records, array('class_name'),count($class_records));
             $this->view->classrecords = $class_records ;
            $this->view->no_of_classes = $max_no_of_classes ;
            $this->view->result = $result;  
            $this->view->faculty_routine = $new_faculty_routine;
                     
            }
                
            }
            
            
            public function ajaxGetWeekWiseViewAction(){
                $this->_helper->layout->disableLayout();
                if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
                    //==class objects 
                    $attendance_model = new Application_Model_Attendance();
                    $class_model = new Application_Model_ClassMaster();
                    $batch_schedule = new Application_Model_BatchSchedule();
                    $academic_model = new Application_Model_Academic();
                    $room_model = new Application_Model_Room();
                    $erp = new Application_Model_HRMModel();
                        
                     
                     //====user param
                      $batch = $this->_getParam("batch");
                     $term = $this->_getParam("term_id");
                    
                    
                     
                     //====fetch values through database
                     
                     $max_no_of_classes = $class_model->getMaxClass();
                   
                         if(empty($batch) || empty($term)){
                    //  $version_details  = $batch_schedule->getAllversion();
                         }
                      else
                      $version_details = $batch_schedule->getAllversionWithBatchTerm($batch, $term);
                     $courses = array();
                      foreach($version_details as $key => $version_detail){
                        $courses[$version_detail['max_version']] = $batch_schedule->getCourseDayWise($max_no_of_classes,$version_detail['max_version']);
                      }
                      
                      foreach($courses as $key => $value){
                          foreach($value as $new_key => $real_value){
                          for($i = 1; $i<=$max_no_of_classes; $i++){
                            $course[] =  $real_value["class_$i"];
                          }
                          }
                      }
                      $course = array_filter($course, function($value){ return $value != '';});
                      $course = array_unique($course);
                  //  echo"<pre>"; print_r($version_details);exit;
          foreach($version_details as $key => $version_detail){
              $no_of_classes = $max_no_of_classes;
                $result[$academic_model->getBatchCodeRecord($version_detail['batch'])['short_code']][$version_detail['term_id']][$version_detail['section']]  = $attendance_model->getAllDateDetailsWeekly($version_detail['term_id'], $version_detail['batch'], $course, date('d-m-Y'), $version_detail['max_version'], $no_of_classes,$version_detail['section']);  
                
            }
            
             
            $new_faculty_routine = array();
            $result['days'] = $this->weekdays();
            
             $result['weekends'] =   explode(',',$erp->getWeeklyOff()[0]['option_value']);
             foreach($result['days'] as $key => $value ) {
                           foreach($result as $batch => $term_value){
                                foreach($term_value as $term_id => $section_value){
                                    foreach ($section_value as $section_id => $routine_value){
                                        for($class = 1; $class<=count($routine_value[$value]); $class++){
                                            $course = !empty($routine_value[$value][$class]['class'])? explode('-',$routine_value[$value][$class]['class']):'' ;
                                                     $new_faculty_routine[$key][$class]['course_code'][] =  $course[1];
                                                     $new_faculty_routine[$key][$class]['batch'][] = $batch;
                                                     $new_faculty_routine[$key][$class]['term'][] = $routine_value[$value][$class]['term'];
                                                     $new_faculty_routine[$key][$class]['section'][] = $routine_value[$value][$class]['section'];
                                                     if(!is_null($routine_value[$value][$class]['room'])){
                                                     $new_faculty_routine[$key][$class]['room'][] = $room_model->getRecord($routine_value[$value][$class]['room'])['room_number'];}
                                                     else{
                                                     $new_faculty_routine[$key][$class]['room'][] = '';}
                                                     $new_faculty_routine[$key][$class]['time'][] = $routine_value[$value][$class]['time'];
                                            
                                }

                            }
                           }
                           }

             }
             $class_records = $class_model->getRecords();
             $class_records = $this->mergData($class_records, array('class_name'),count($class_records));
            $this->view->classrecords = $class_records ;
            $this->view->no_of_classes = $max_no_of_classes ;
            $this->view->result = $result;  
            $this->view->faculty_routine = $new_faculty_routine;
                     
            }
                
            }
           public function  weekdays(){
               return  array(
                     
                        'Mon' => 'Monday',
                        'Tue' => 'Tuesday',
                        'Wed' => 'Wednesday',
                        'Thu' => 'Thursday',
                        'Fri' => 'Friday',
			'Sat' => 'Saturday',
                        'Sun' => 'Sunday',
                    ); 
           }       
}