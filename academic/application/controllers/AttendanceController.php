<?php

class AttendanceController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    private $_flashMessenger = null;
    private $login_storage = NULL;
    private $roleConfig = NULL;
    private $smsConfig = NULL
;
    private $accessConfig =NULL;
    public function init() {

        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();

        $config = $zendConfig->mainconfig->toArray();
        $this->smsConfig = $sms = $zendConfig->smsconfig->toArray();
        $this->view->mainconfig = $config;

        $this->_action = $this->getRequest()->getActionName();

        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->holidayCategory = $holiday_category = $zendConfig->holiday_category->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $this->login_storage = $data = $storage->admin_login;
        
     
        $this->view->login_storage = $data;
       //echo "<pre>";print_r($data);exit;
        if (isset($data)) {
            $this->view->role_id = $data->role_id;
            $this->view->login_empl_id = $data->empl_id;
        }

        if ($this->_action == "login" || $this->_action == "forgot-password") {

            $this->_helper->layout->setLayout("adminlogin");
        } else {

            $this->_helper->layout->setLayout("layout");
        }


        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();

        $this->_act = new Application_Model_Adminactions();

        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    protected function authonticate() {

        $storage = new Zend_Session_Namespace("admin_login");

        $data = $storage->admin_login;
                if($data->role_id == 0)
            $this->_redirect('student-portal/attendance');
        if (!$data && $this->_action != 'login' &&
                $this->_action != 'forgot-password'  ) {
            

            $this->_redirect('index/login');

            return;
        }

        if ($this->_action != 'forgot-password') {

            $this->_authontication = $data;

            $this->_agentsdata = $storage->agents_data;
        }
    }

    public function indexAction() {
        $this->view->action_name = 'Attendance';
        $this->view->sub_title_name = 'attendance';
         $this->accessConfig->setAccess('SA_ACAD_P_ATTENDANCE');
        $atendence_saver_model = new Application_Model_Attendance();
        $attendance_form = new Application_Form_Attendance();
        $class_master = new Application_Model_ClassMaster();
        $section_model = new Application_Model_Section();
         $employee_model = new Application_Model_HRMModel();
        $mobile_message = new Application_Model_Mobile();
        $ec_id = $this->_getParam("id");
        $term_id = $this->_getParam("term");
        $batch_id = $this->_getParam("batch");
        $section = $this->_getParam("section");
        //print_r($ec_id);die;
        
        
        $type = $this->_getParam("type");
        $this->view->type = $type;
       // $this->view->result = "hello";
        $this->view->form = $attendance_form;
        switch ($type) {
            case "add":
                echo "add "; 
               
                if ($this->getRequest()->isPost()) {
                    if ($attendance_form->isValid($this->getRequest()->getPost())) {
                        $parrents_number = array('mother_no','father_no');
                        
                        $bool = 0;
                        $stu_name = $this->getRequest()->getPost('name');
                        $batch_id = $this->getRequest()->getPost('academic_year_id');
                        $term_id = $this->getRequest()->getPost('term_id');
                        $date = $this->getRequest()->getPost('date');
                        $student_id = $this->getRequest()->getPost('student_id');
                        $course_id = $this->getRequest()->getPost('course_id');
                        $section = $this->getRequest()->getPost('section');
                        $clno = $class_master->getRecordByTermIdAndBatch(0,0);
                        
                        for($dcl = 1; $dcl<= $clno; $dcl++){
                              if($this->getRequest()->getPost("faculty_$dcl") != '')
                                ${"faculty_$dcl"}=$this->getRequest()->getPost("faculty_$dcl");
                            else 
                              ${"faculty_$dcl"} =  0; //========[ZERO IS DEFAULT VALUE FOR CLASS dynamic]================//
                           }
                        
                            for ($i = 0; $i < count($stu_name); $i++){
                                
                            for($dcl = 1; $dcl<= $clno; $dcl++){
                             if($this->getRequest()->getPost("class$dcl".$i) == 'Absent')
                                     ${"class_$dcl"} = $this->getRequest()->getPost("class$dcl".$i)."-".$course_id;
                              else if($this->getRequest()->getPost("class$dcl".$i) == 'Leave')
                                     ${"class_$dcl"}=$this->getRequest()->getPost("class$dcl".$i)."-".$course_id;
                             else if($this->getRequest()->getPost("class$dcl".$i) != '')
                                ${"class_$dcl"} = $this->getRequest()->getPost("class$dcl".$i);
                            else 
                              ${"class_$dcl"} = 0; //========[ZERO IS DEFAULT VALUE FOR CLASS1]================//
                            }
                            
                            
                         
                            
                            
                            //=======[For-Faculty]============//
                       
                            
                            //=====[class-check]======//
                            $bool = $atendence_saver_model->checkDetails($stu_name[$i],$batch_id,$term_id,$date,$section);
                            
                            for($dcl = 1; $dcl<= $clno; $dcl++){
                            ${"bool$dcl"} = $atendence_saver_model->checkDetail($stu_name[$i],$batch_id,$term_id,$date,"class_$dcl",$section);
                            ${"boolf$dcl"} = $atendence_saver_model->checkDetail($stu_name[$i],$batch_id,$term_id,$date,"faculty_$dcl",$section);
                            }
                          //  print($class_1."=".$class_2."=".$class_3."=".$class_4."=".$class_5);exit;
                          $data = array(
                                'student_name' => $stu_name[$i],
                                'date' => $date,
                                'batch_id' => $batch_id,
                                'term_id' => $term_id,
                                'section' => $section,
                                'student_id' => $student_id[$i],
                                'updated_date' => date('Y-m-d')
                          );
                          //==========[sending mobile message to parents and  teachers]=====================//
                          
                            if($this->smsConfig['send']==1){
                                   for($dcl = 1; $dcl<= $clno; $dcl++){
                                         if(${"class_$dcl"} == 'Absent'){
                                             $message = "This  is a remainder message from (DMI), $stu_name[$i] is not present in class $dcl on ".date('d-m-Y');
                                             for($numc=0; $numc<2; $numc++ ){
                                                    $number = $this->getRequest()->getPost($parrents_number[$numc])[$i];
                                                   $mobile_message->sendMessageOnMobile($message,$number);
                                               }
                                               $mobile_message->sendMessageOnMobile($message,$_SESSION['admin_login']['admin_login']->phone); 
                                         }
                                      } 
                                   }                  

                        //==========[PUSH FIVE CLASS VALUE IS SOMETHING IS THERE ]==========//
                            if ($bool < 1) {
                                for($dcl = 1; $dcl<= $clno; $dcl++){
                                if (${"bool$dcl"} == 0)
                                    $data["class_$dcl"] = ${"class_$dcl"};
                           
                                  if ($boolf1 == 0)
                                        $data["faculty_$dcl"] = ${"faculty_$dcl"};
                                    }
                                $atendence_saver_model->insert($data);
                            }
                            else
                            {  for($dcl = 1; $dcl<= $clno; $dcl++){
                                if(${"class_$dcl"}!='0')
                                    $data["class_$dcl"] = ${"class_$dcl"};
                                    if (${"faculty_$dcl"} != '0')
                                    $data["faculty_$dcl"] = ${"faculty_$dcl"};
                                        }
                               $atendence_saver_model->update($data,array('date=?' => $date,'section=?'=>$section,'student_name=?'=>$stu_name[$i])); 
                            }
                        }
                        $this->_flashMessenger->addMessage('Attendence Successfully added');
                        $this->_redirect('attendance/index');
                    }
                }
                

                break;
            case 'edit':
               $clno = $class_master->getRecordByTermIdAndBatch(0,0);
                $result = $atendence_saver_model->getRecordById($ec_id,$section,$clno); 
                $course_model = new Application_Model_Course();
                
                //=========================================================================
          
               foreach($result as $key => $value )
                {
                   for($dcl = 1 ; $dcl<=$clno; $dcl++){
                        $result[$key]["faculty_$dcl"] = $employee_model->getAllEmployee($value["faculty_$dcl"])[0]['name'];
                   }
                }
                $class_records = $class_master->getRecords();
       $class_records = $this->mergData($class_records, array('class_name'),count($class_records));
             $this->view->classrecords = $class_records;
                $this->view->no_of_classes = $clno;
                $this->view->result = $result; 
                
                break;
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $atendence_saver_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('evaluation-components/index');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                //print_r($this->login_storage);exit;
                //Show only components for the logged in faculty
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                $result = $atendence_saver_model->getRecords();
                foreach($result as $key => $value){
                    if(!empty($value['section']))
                  $result[$key]['name'] = $section_model->getRecordById($value['section']);
                }
                 
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
    
    
        public function ajaxGetSectionAction() {
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
        
            $termDetails = new Application_Model_Section();
            $result = $termDetails->getRecordByTermIndex($term_id);

            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['id'] . '" >' . $val['name'] . '</option>';
            }
        }die; 
    }
    

    public function ajaxGetCourseAction() {
        $course_details = new Application_Model_Attendance();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam('term_id');
            $batch_id = $this->_getParam('academic_year_id');
            
               
            
                    /*$classDetails = new Application_Model_ClassMaster();
            $groupd_class_record = $classDetails->getGroupedRecord();
            foreach($groupd_class_record as $key => $value){
                $data[] = $value['total'];
            }
            $total_clas_to_be_created = (max($data));
            $batch_schedule_model = new Application_Model_Attendance();
            $batch_schedule_model->create_column((int)$total_clas_to_be_created,array('class','faculty'));*/
  
            
            $result = $course_details->getCourseDetails($term_id, $batch_id);
            echo '<option value="">Select</option>';
            foreach ($result as $value) {
                echo '<option value="' . $value['course_id'] . '" >' . $value['course_code'] . '</option>';
            }
        }die;
    }
    
      public function ajaxGetCourseNewAction() {
        $course_details = new Application_Model_Attendance();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('academic_year_id');
            $result = $course_details->getCourseDetails($term_id, $batch_id);
           
           echo json_encode($result);exit;
        }
      }
    
    

    public function ajaxGetDateAction() {
        $date_details = new Application_Model_Attendance();
        $class_master = new Application_Model_ClassMaster();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');
            $course_id = $this->_getParam("course_id");
            $section = $this->_getParam("section");
            // print_r($course_id);exit;
            $no_of_classes = $class_master -> getRecordByTermIdAndBatch(0, 0);
            $result = $date_details->getDateDetails($term_id, $batch_id, $course_id,$section,$no_of_classes);
            // print_r($result); exit;
            $i = 0;
            $arr = array();
            foreach ($result as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $arr[] = $value1;
                }
            }
            $unique_data = array_unique($arr);
            echo json_encode($unique_data);
            /*echo '<option value="">Select</option>';
            foreach ($unique_data as $value) {
                echo '<option value="' . $value . '" >' . $value . '</option>';
            }*/
        }die;
    }

    public function ajaxStudentListViewAction() {
        $student_list = new Application_Model_Attendance();
        $term_info = new Application_Model_TermMaster();
         $class_master = new Application_Model_ClassMaster();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');
            $course_id = $this->_getParam("course_id");
            $date_val = $this->_getParam("date_val");
            $section = $this->_getParam('section');
            // print_r($date_val);exit;
            //$date_str = date("d-m-Y",strtotime($date_val));
           $attendance_details =  $student_list->getAttendanceResult($term_id, $batch_id, $course_id, $date_val,$section);
            $cl_no = $class_master -> getRecordByTermIdAndBatch(0, 0);
            $no_of_classes = $student_list->connectBatchSheduler($term_id, $batch_id, $course_id, $date_val,$section,$cl_no);
            $result = $student_list->getStudentList($term_id, $batch_id, $course_id);
            
               $year_id = $term_info->getYearId($term_id);
             $GradeSheet_model = new Application_Model_GradeSheet();
             
            
            
            
              
            $result[count($result) - 1]['class_no'] = $no_of_classes;
            $result[count($result) - 1]['course_id'] = $course_id;
            $result[count($result) - 1]['attendance_details'] = $attendance_details;
         // echo "<pre>";print_r($result);exit;
           /*   foreach($result as $key => $value){
                //  echo $value['stu_id']; exit;
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber1($batch_id, $year_id, $value['stu_id']);
                if($gradesheet_number!=0)
                {
                 //   echo count($result[$key]);exit;
                    unset($result[$key]);
                    unset($result[count($result)]['attendance_details'][$key]);
                }
             }*/
             $result = array_values($result);
           $result[count($result) - 1]['attendance_details'] = array_values($result[count($result) - 1]['attendance_details']);
           $class_records = $class_master->getRecords();
       $class_records = $this->mergData($class_records, array('class_name'),count($class_records));
             $this->view->classrecords = $class_records;
          $this->view->no_of_classes = $cl_no;
            $this->view->result = $result;
        }
    }
    
    
    
public function ajaxGetFacultyAction()
{    $employee_model = new Application_Model_HRMModel();
     $faculty = new Application_Model_Attendance();
     $term_id = $this->_getParam('term_id');
     $batch_id = $this->_getParam('batch_id');
     $course_id = $this->_getParam('course_id');
     $result = $faculty->getFaculty($term_id, $batch_id, $course_id);
     // print_r($course_cordinatior);exit;
     $faculty_id = explode(',',$result[0]['faculty_id']);
     $visiting_faculty = explode(',',$result[0]['visiting_faculty_id']);
     
     //======[MERGING BOTH FACULTY ARRAY]=======//
     $faculty_arr = array_merge($faculty_id,$visiting_faculty);
     $faculty_arr[count($faculty_arr)] =  $result[0]['employee_id']; 
      //$faculty_rr = array_merge($faculty_arr,$result[0]['employee_id']);
     
        //====={MAKING ARRAY UNIQUE}==============//
    
     $all_unique_faculty = array_unique($faculty_arr);
   
     
     //======[GETTING NAME OF AL THE EMPLYOEE]=======//
 $i = 0;
 foreach($all_unique_faculty as $key => $value){
     
     if($value!='NA'){
         if($value)
              $empl_name[$i] = $employee_model->getAllEmployee1($value)[0];
     }
     $i++;
     
 }
   
     //=========[SETTING SELECT BOX]=========//
       
      echo '<option value="'.$_SESSION['admin_login']['admin_login']->empl_id.'">'.$_SESSION['admin_login']['admin_login']->real_name.'</option>';
      
        if (count($empl_name) > 0) {
            foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
            }die;
        }
     
     die;
    
}




public function ajaxGetAttendanceViewAction() {
        $student_form = new Application_Form_StudentElement();

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");//==accepting from outside
            $atendence_saver_model = new Application_Model_Attendance();
            $employee_model = new Application_Model_HRMModel();
            $course_report = new Application_Model_CourseReport();
            $student_info  = new Application_Model_FeeDetails();
            $present = 0;
            $absent = 0;
            $leave = 0;
            $total_class = 0;
            $total_absent = 0;
            $total_present = 0;
            $total_leave = 0;

            $batch_id = $this->_getParam("batch_id");//===accepting from outside 
            $course_id = $this->_getParam("course_id");//===accepting from outside   

            //$running_terms = $this->getRecentTerm($batch_id);


            $single_result = array();
            if (!empty($term_id)) {
                
              $student_result =  $student_info->getRecordsByBatchTerm($term_id,$batch_id);//===geting student information on the basis of academic and term
                
                $course_result = $atendence_saver_model->getCourseDetailsByCourseId($course_id);//=====getting course details with the help of course id
                
                $course_count = $course_report->getTotalNumberOfDays($term_id, $batch_id);
                
                foreach ($student_result as $key => $value) {
                    
                    $single_result[$key]['stu_name'] = $value['participants_name'];
                    $single_result[$key]['course_code'] = $course_result[0]['course_code'];
                    $single_result[$key]['course_name'] = $course_result[0]['course_name'];
                    $single_result[$key]['course_id']   = $course_result[0]['course_id'];
                    
                    foreach ($course_count as $key1 => $value1) {
                        if ($course_result[0]['course_code'] == $value1['course_code']) {
                            //===[FETCH VALUES FOR PRESENT FOR STUDENT]======//
              $result_class1[$key]['p'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], $course_id, $term_id, $batch_id, 'class_1');
              $result_class2[$key]['p'] = $atendence_saver_model->getRecordByStudentId($value['student_id'],$course_id, $term_id, $batch_id, 'class_2');
              $result_class3[$key]['P'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], $course_id, $term_id, $batch_id, 'class_3');
              $result_class4[$key]['P'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], $course_id, $term_id, $batch_id, 'class_4');
              $result_class5[$key]['P'] = $atendence_saver_model->getRecordByStudentId($value['student_id'],$course_id, $term_id, $batch_id, 'class_5');


                            //===[FETCH VALUES FOR ABSENT FOR STUDENT]======//
               $result_class1[$key]['A'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Absent' . "-" . $course_id, $term_id, $batch_id, 'class_1');
               $result_class2[$key]['A'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Absent' . "-" . $course_id, $term_id, $batch_id, 'class_2');
               $result_class3[$key]['A'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Absent' . "-" . $course_id, $term_id, $batch_id, 'class_3');
               $result_class4[$key]['A'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Absent' . "-" . $course_id, $term_id, $batch_id, 'class_4');
               $result_class5[$key]['A'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Absent' . "-" . $course_id, $term_id, $batch_id, 'class_5');
               
                            //===[FETCH VALUES FOR LEAVE FOR STUDENT]======//
               $result_class1[$key]['L'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Leave' . "-" . $course_id, $term_id, $batch_id, 'class_1');
               $result_class2[$key]['L'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Leave' . "-" . $course_id, $term_id, $batch_id, 'class_2');
               $result_class3[$key]['L'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Leave' . "-" . $course_id, $term_id, $batch_id, 'class_3');
               $result_class4[$key]['L'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Leave' . "-" . $course_id, $term_id, $batch_id, 'class_4');
               $result_class5[$key]['L'] = $atendence_saver_model->getRecordByStudentId($value['student_id'], 'Leave' . "-" .$course_id, $term_id, $batch_id, 'class_5');
                           
                                //===[FETCH VALUES FOR UPDATED DATE FOR STUDENT]======//
               $result_class1[$key]['updated_date'] = $atendence_saver_model->getRecordByStudentIdUpdate($value['student_id'], $course_id, $term_id, $batch_id, 'class_1');
             $result_class2[$key]['updated_date'] = $atendence_saver_model->getRecordByStudentIdUpdate($value['student_id'], $course_id, $term_id, $batch_id, 'class_2');
             $result_class3[$key]['updated_date'] = $atendence_saver_model->getRecordByStudentIdUpdate($value['student_id'], $course_id, $term_id, $batch_id, 'class_3');
             $result_class4[$key]['updated_date'] = $atendence_saver_model->getRecordByStudentIdUpdate($value['student_id'], $course_id, $term_id, $batch_id, 'class_4');
             $result_class5[$key]['updated_date'] = $atendence_saver_model->getRecordByStudentIdUpdate($value['student_id'], $course_id, $term_id, $batch_id, 'class_5');

                            if (!empty($result_class1[$key]['updated_date']))
                                $single_result[$key]['updated_date'] = $result_class1[$key]['updated_date'];
                            if (!empty($result_class2[$key]['updated_date']))
                                $single_result[$key]['updated_date'] = $result_class2[$key]['updated_date'];
                            if (!empty($result_class3[$key]['updated_date']))
                                $single_result[$key]['updated_date'] = $result_class3[$key]['updated_date'];
                            if (!empty($result_class4[$key]['updated_date']))
                                $single_result[$key]['updated_date'] = $result_class4[$key]['updated_date'];
                            if (!empty($result_class5[$key]['updated_date']))
                                $single_result[$key]['updated_date'] = $result_class5[$key]['updated_date'];
                            
                            //============[CALCULATION FOR PRESENT ABSENT AND LEAVES]===========//
     $single_result[$key]['p'] = $result_class1[$key]['p'] + $result_class2[$key]['p'] + $result_class3[$key]['P'] + $result_class4[$key]['P'] + $result_class5[$key]['P'];
     $single_result[$key]['A'] = $result_class1[$key]['A'] + $result_class2[$key]['A'] + $result_class3[$key]['A'] + $result_class4[$key]['A'] + $result_class5[$key]['A'];
     $single_result[$key]['L'] = $result_class1[$key]['L'] + $result_class2[$key]['L'] + $result_class3[$key]['L'] + $result_class4[$key]['L'] + $result_class5[$key]['L'];

     $single_result[$key]['total_class'] = $value1['course_count'];
     $total_class += $value1['course_count'];
     $total_present += $result_class1[$key]['p'] + $result_class2[$key]['p'] + $result_class3[$key]['P'] + $result_class4[$key]['P'] + $result_class5[$key]['P'];
     $total_absent += $result_class1[$key]['A'] + $result_class2[$key]['A'] + $result_class3[$key]['A'] + $result_class4[$key]['A'] + $result_class5[$key]['A'];
     $total_leaves += $result_class1[$key]['L'] + $result_class2[$key]['L'] + $result_class3[$key]['L'] + $result_class4[$key]['L'] + $result_class5[$key]['L'];

     $single_result[$key]['rest_days'] = $value1['course_count'] - ($single_result[$key]['p'] + $single_result[$key]['A'] + $single_result[$key]['L']);
                        }
                    }
                }
            }
         
            $this->view->result = $single_result;
        }
    }




        public function reportAction() {
        $this->view->action_name = 'attendance_report';
        $this->view->sub_title_name = 'Student Attendance';
        $this->accessConfig->setAccess('SA_ACAD_ATTENDANCE_REPORT');
        $student_form =  new Application_Form_Attendance();;

        $this->view->form = $student_form;
    }

    
    

}
