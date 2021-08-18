<?php

class BatchScheduleController extends Zend_Controller_Action {

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
    Private $id = NULL;
    private $accessConfig =NULL;

    public function init() {

        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();

        $config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;

        $this->_action = $this->getRequest()->getActionName();

        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->holidayCategory = $holiday_category = $zendConfig->holiday_category->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $this->login_storage = $data = $storage->admin_login;
        $this->view->login_storage = $data;
        //print_r($data);exit;
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
            $this->_redirect('student-portal/class-schedule');
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
        $this->view->action_name = 'Batch Schedule';
        $this->view->sub_title_name = 'Batch Schedule';
        $this->accessConfig->setAccess('SA_ACAD_INTERACTIVE_LEARNING_SESSION');
        $EvaluationComponents_model = new Application_Model_BatchSchedule();
        $section_model = new Application_Model_Section();
        $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
        $course_master  =new Application_Model_Course();
        $class_master = new Application_Model_ClassMaster();
        $ExperientialEvaluationComponents_model = new Application_Model_ExperientialEvaluationComponents();
        
        $EvaluationComponents_form = new Application_Form_BatchSchedule();
        $course_report = new Application_Model_CourseReport();
        $this->id = $ec_id = $this->_getParam("id");
        $version = $this->_getParam('version');
        $section = $this->_getParam('section');
        // print_r($version);exit;
        //print_r($ec_id);die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $EvaluationComponents_form;

        switch ($type) {
            case "add":
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                //print_r($this->login_storage);exit;
                //Show only components for the logged in faculty
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
             //   if (in_array($role_id, $this->roleConfig)) {
                    $result = $EvaluationComponents_model->getNotPublishedRecord();
              //  }
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);  
                $counter_array = array('compare_batch_schedule' => 0);
                $public_version = 0.0;
                if (isset($_POST['submit_type']) && $_POST['submit_type'] == 'publish') {
                    if ($this->getRequest()->isPost()) {                        
                        if ($this->getRequest()->getPost()) {
                            
                            
                            $batch_id = $this->getRequest()->getPost('academic_year_id');
                            $term_id = $this->getRequest()->getPost('term_id');
                            
                              
                              $no_of_classes = $class_master->getRecordByTermIdAndBatch(0, 0);
                            
                            $section = $this->getRequest()->getPost('section');
                            
                            $date = $this->getRequest()->getPost('date');
                            $days = $this->getRequest()->getPost('days');
                            $day_diff = $this->getRequest()->getPost('day_diff');
                            $start_date = $this->getRequest()->getPost('start_date');
                          
                            
                            $class_arr = array();
                            $description_arr = array();
                            
                            
                            $course_code =  $this->getRequest()->getPost('course_code');
                            $course_count = $this->getRequest()->getPost('course_count');
                            $c_data['batch_id'] = $batch_id;
                            $c_data['term_id'] = $term_id;
                            $course_bool = $EvaluationComponents_model->checkValue($batch_id,$term_id,$section);
                            $c_data['section'] =  $section;  
                            
                            foreach($course_code as $key => $value){
                            $c_data['course_code'] =  $value;  
                            $c_data['course_count'] = $course_count[$key];
                            
                            
                            if($course_bool==0){
                                $course_report->insert($c_data);
                            }
                            if($course_bool>0){
                                $course_report->update($c_data,array('term_id =?'=> $term_id,'batch_id =?'=>$batch_id,'section =?'=>$section,'course_code =?'=>$value));
                            }
                            }
                            
                            
                            $day_count = $this->getRequest()->getPost('day_count');
                            $last_published_version = $EvaluationComponents_model->publish_version($batch_id, $term_id,$section, $date[1],$class_arr);
                            $min_version = $EvaluationComponents_model->minVersion($batch_id, $term_id,$section, $date[1], $class_arr);
                            
                            for($i = 0; $i<=$day_diff; $i++){
                               
                                    $inc_date = date("d-m-Y", strtotime($start_date . ' + ' . $i . ' days'));
                                    $day = date('l', strtotime($inc_date)); 
                               
                                for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
                                     ${"class$dcl$day"} = $this->getRequest()->getPost("class$dcl$day");
                               ${"description$dcl$day"} = $this->getRequest()->getPost("Extra$dcl$day");
                               ${"time$dcl$day"} = $this->getRequest()->getPost("time$dcl$day");
                               ${"room$dcl$day"} = $this->getRequest()->getPost("room$dcl$day");
                                    $class["class_$dcl"] = empty(${"class$dcl$day"}) ? '' : ${"class$dcl$day"};
                                }
                                $val = $EvaluationComponents_model->checkIfAnyChange($batch_id, $term_id,$section, $last_published_version, $inc_date, $class, $dcl);
                                $counter_array['compare_batch_schedule'] += $val;
                            }
                            
                           // echo $counter_array['compare_batch_schedule'].count($class1).$last_published_version; exit;
                            
                            
                            if($counter_array['compare_batch_schedule'] == $day_diff && $min_version==0.0)
                            {
                                 $public_version = (float) $last_published_version+0.1;
                            }
                            else {
                                $public_version = $last_published_version;
                                        }
                            $val1 = $EvaluationComponents_model->saveAtFirstTime($batch_id, $term_id,$section, 0.0);
                            $course_count = array();
                            for ($i = 0; $i < $day_diff; $i++) {
                                $inc_date = date("d-m-Y", strtotime($start_date . ' + ' . $i . ' days'));
                                    $day = date('l', strtotime($inc_date)); 
                                $data = array(
                                    'batch' => $batch_id,
                                    'date' => $inc_date,
                                    'term_id' => $term_id,
                                    'section' => $section,
                                    'day' => $day,
                                    'status' => 0,
                                    'day_count' => $day_count[$i],
                                    'updated_date' => date("Y-m-d"),
                                );
                                for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
                                    $data["class_$dcl"] = ${"class$dcl"."$day"};
                                    
                                    if(!empty(${"class$dcl"."$day"})){
                                    $course_count[${"class$dcl"."$day"}] +=1; }
                                    
                                    $data["description_$dcl"] = $course_count[${"class$dcl"."$day"}];
                                    if(!empty($data["class_$dcl"])){
                                        $data["time_$dcl"] = ${"time$dcl"."$day"};
                                        $data["room_$dcl"] = ${"room$dcl"."$day"};
                                    }
                                     if(!empty(${"class$dcl"."$day"})){
                                    $course_bool = $EvaluationComponents_model->checkValue($batch_id,$term_id,$section);
                                    $course_details = $course_master->getCourseCodeById(${"class$dcl"."$day"});
                                     $c_data['course_code'] =  $course_details['course_code'];  
                                    $c_data['course_count'] = $data["description_$dcl"];
                                        if($course_bool>0){
                                            $course_report->update($c_data,array('term_id =?'=> $term_id,'batch_id =?'=>$batch_id,'section =?'=>$section,'course_code =?'=>$course_details['course_code']));
                                        }
                                    
                                }
                                    
                                }
                                //echo "<pre>";print_r($c_data);exit;
                               
                                if ((int)$counter_array['compare_batch_schedule'] == $day_diff){
                                     $data['publish'] = $public_version;
                                    $EvaluationComponents_model->update($data, array('date=?' => $inc_date,'section =?'=>$section,'publish=?' => (float)$last_published_version));
                                    //$EvaluationComponents_model->delete(array('batch =?' =>  $batch_id,'term_id =?'=>$term_id,'publish=?'=>(float)0.0));
                                }
                                else{
                                    $public_version = (float) $last_published_version+0.1;
                                     $data['publish'] = $public_version;
                                    $EvaluationComponents_model->insert($data);
                                    $EvaluationComponents_model->delete(array('batch =?' =>  $batch_id,'term_id =?'=>$term_id,'section =?'=>$section,'publish=?'=>(float)0.0));
                                }
                            } 
                            $this->_flashMessenger->addMessage('Batch Scheduler Successfully Published');
                            $this->_redirect('batch-schedule/index');
                        }
                    }
                }

           
                break;
            case 'edit':
                $myarra = array();
                $result = $EvaluationComponents_model->getRecord($ec_id, $version,$section);
                   //echo "<pre>";print_r($result);exit;
                   $from_date = strtr($result[0]['date'],"-","/");
                   $to_date = strtr($result[count($result)-2]['date'],"-","/");
                   $myarra['Academic_id'] = $result[0]['batch'];
                   $myarra['Term_id'] = $result[0]['term_id'];
                   $myarra['section'] = $section;
                   $myarra['version_id'] = $result[0]['publish'];
                   $myarra['from_date'] = $from_date;
                   $myarra['to_date'] =  $to_date ;
                   $_SESSION['myVal']['term_id'] =  $myarra['Term_id'];
                   $_SESSION['myVal']['version_id'] =  $myarra['version_id'];
                   $_SESSION['myVal']['section'] = $section;
                   
                 
                   $no_of_classes = $class_master->getRecordByTermIdAndBatch(0, 0);
                 
                //echo "<pre>";print_r( $_SESSION['select_option']);exit;
              //  echo "<pre>";print_r($result[0]);exit;
                //$this->_helper->layout->disableLayout();		
                //$this->view->result = $result;
                  
                  
                      $Academic_model = new Application_Model_Academic();
                    $data = $Academic_model->getDropDownList();
                     
                     $EvaluationComponents_form->getElement('academic_year_id')->setAttrib('style', array('display:initial'));
                $employee_id = $EvaluationComponents_form->createElement('select', 'Academic_id');
                $employee_id->setAttrib('class', array('form-control', 'chosen-select'));
                $employee_id->removeDecorator("htmlTag");
                $employee_id->addMultiOptions(array('' => 'Select'));
                $employee_id->setRegisterInArrayValidator(false);
                $employee_id->addMultiOptions($data);
                $EvaluationComponents_form->addElement($employee_id);
                $item_result = $EvaluationComponents_model->getItemRecords($ec_id);
                    
                
                $EvaluationComponents_form->getElement('term_id')->setAttrib('style', array('display:initial'));
                $employee_id = $EvaluationComponents_form->createElement('select', 'Term_id');
                $employee_id->setAttrib('class', array('form-control', 'chosen-select'));
                $employee_id->removeDecorator("htmlTag");
                $employee_id->addMultiOptions(array('' => 'Select'));
                $employee_id->setRegisterInArrayValidator(false);
                $EvaluationComponents_form->addElement($employee_id);
                
                
                $EvaluationComponents_form->removeElement('section');
                $data = $this->getSection($myarra['Term_id']);
             
                $employee_id = $EvaluationComponents_form->createElement('select', 'section');
                $employee_id->setAttrib('class', array('form-control', 'chosen-select'));
                $employee_id->removeDecorator("htmlTag");
                $employee_id->addMultiOptions(array('' => 'Select'));
                $employee_id->setRegisterInArrayValidator(false);
                $employee_id->addMultiOptions($data);
                $EvaluationComponents_form->addElement($employee_id);
                
                
                
                
                
                       $EvaluationComponents_form->getElement('version_id')->setAttrib('style', array('display:initial'));
                $employee_id = $EvaluationComponents_form->createElement('select', 'version_id');
                $employee_id->setAttrib('class', array('form-control', 'chosen-select'));
                $employee_id->removeDecorator("htmlTag");
                $employee_id->addMultiOptions(array('' => 'Select'));
                $employee_id->setRegisterInArrayValidator(false);
                $EvaluationComponents_form->addElement($employee_id);
                //print_r($item_result);exit;
                // $this->view->item_result = $item_result;
              //echo "<pre>";print_r($myarra);exit;
                $EvaluationComponents_form->populate($myarra);
                 
             $result = $this->batchSchedule($myarra['Term_id'], $myarra['Academic_id'],$section, $myarra['version_id'], $myarra['from_date'], $myarra['to_date']); 
       $class_records = $class_master->getRecords();
       $class_records = $this->mergData($class_records, array('class_name'),count($class_records));
             $this->view->classrecords = $class_records;
                $this->view->no_of_classes = $no_of_classes;
                $this->view->result = $result;
                
                if ($this->getRequest()->isPost()) {
                    if ($this->getRequest()->getPost()) {
                        $class1 = $this->getRequest()->getPost('class1');
                        $class2 = $this->getRequest()->getPost('class2');
                        $class3 = $this->getRequest()->getPost('class3');
                        $class4 = $this->getRequest()->getPost('class4');
                        $class5 = $this->getRequest()->getPost('class5');
                        $date = $this->getRequest()->getPost('date');
                        //print_r($date);exit;
                        $batch_id = $this->getRequest()->getPost('academic_year_id');
                        $term_id = $this->getRequest()->getPost('term_id');
                        for ($i = 0; $i < count($class1); $i++) {
                            // $bool=$EvaluationComponents_model->checkDetails($batch_id,$term_id,$date[$i]);
                            $data = array(
                                'date' => $date[$i],
                                'class_1' => $class1[$i],
                                'class_2' => $class2[$i],
                                'class_3' => $class3[$i],
                                'class_4' => $class4[$i],
                                'class_5' => $class5[$i],
                                'status' => 0
                            );
                            $EvaluationComponents_model->update($data, array('date=?' => $date[$i]));
                        }

                        $this->_flashMessenger->addMessage('Batch Scheduler Updated Successfully');
                        $this->_redirect('batch-schedule/index');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                $result = $EvaluationComponents_model->gebatchIdTermId($ec_id);
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('batch=?' => $result['batch'], 'term_id=?' => $result['term_id'], 'publish=?' => (float) $version));
                    //$EvaluationComponentsItems_model->update($data, array('batch=?' => $result['batch'],'term_id=?'=>$result['term_id']));
                    $this->_flashMessenger->addMessage('Batch Scheduler Deleted Successfully');
                    $this->_redirect('batch-schedule/index/index');
                }
                break;
            default:
			
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                //print_r($this->login_storage);exit;
                //Show only components for the logged in faculty
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                
                    $result = $EvaluationComponents_model->getRecords();
              //  echo "<prE>";print_r($result);exit;
                
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
    
    public function batchSchedule($term_id, $batch_id,$section ,$version_id, $start_date, $end_date){
        
        
        
            $erp = new Application_Model_HRMModel();
           // print_r($term_id);exit;
             $termDetails = new Application_Model_BatchSchedule();
             $classMaster = new Application_Model_ClassMaster();
            $result = $termDetails->getTermDetails($batch_id, $term_id);
            $allDetails = new Application_Model_BatchSchedule();
            $resultDetails = $allDetails->getALL($batch_id, $term_id,$section, $version_id);
          
            $result['class_value'] = $resultDetails;
          
            //  $terms_count = strlen($result['term_name']);
            // $term_val = substr($result['term_name'],$terms_count-1);
 
            $course_result = $termDetails->getCourseDetails($batch_id, $term_id);
            $holidayList = new Application_Model_DmiHoliday();
            $all_holiday = $holidayList->getHolidayList($this->holidayCategory);
            
            $no_of_classes = $classMaster->getRecordByTermIdAndBatch(0, 0);
            //getting record from studentAttendance      
            //print_r($resultDetails);exit;
            $allRecordsFromStudentAttendance = new Application_Model_Attendance();
            $studentAttendance = $allRecordsFromStudentAttendance->getRecordByBatchAndTerm($term_id, $batch_id,$no_of_classes,$section);
            $result['Attendance_result'] = $studentAttendance;
            //$holidayList = new Application_Model_DmiHoliday();
            //$all_holiday = $holidayList->getHolidayList();
            //  print($result['start_date']."_".$result['end_date']);
            if(empty($start_date) && empty($end_date)){
            $term_start = explode("/", $result['start_date']);
            $term_end = explode("/", $result['end_date']);
            $result['chek_date'] = $result['start_date'];
            $result['selected_date'] = 0;
            }
            else {
               
                $term_start = explode("/", $start_date);
                $term_end = explode("/", $end_date);
                 $result['selected_date'] = $term_start[1] . "/" . $term_start[0] . "/" . $term_start[2];
                  $result['chek_date'] = $result['start_date'];
                // print_r($result['selected_date']);exit;
                    }
               
            //$start = strtr($result['start_date'], '/', '-');
            //$end = strtr($result['end_date'], '/', '-');
            $start = $term_start[2] . "-" . $term_start[1] . "-" . $term_start[0];
            $end = $term_end[2] . "-" . $term_end[1] . "-" . $term_end[0];
            // print($start."_".$end);
            // print_r($end);exit;
            // $start = date("Y-m-d", strtotime($start));
            // $end = date("Y-m-d", strtotime($end));
            $result['day_diff'] = date_diff2($start, $end);
            
            //echo $result['day_diff'];exit;
            $result['course_result'] = $course_result;
            $result['holidays'] = $all_holiday;
            $result['weekends'] =   explode(',',$erp->getWeeklyOff()[0]['option_value']);
            return $result;
      
        
        
        
    }

    public function newSelectBoxes() {
        $EvaluationComponents_form = new Application_Form_BatchSchedule();
    }


       public function ajaxGetTermsNameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            // print_r($academic_year_id); die;
            // $Corecourselearning_model= new Application_Model_Corecourselearning();
            $student_model = new Application_Model_BatchSchedule();

            $result = $student_model->getDropDownListTerm($academic_year_id);
          
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['term_id'] . '" >' . $val['term_name'] . '</option>';
            }
        }die;
    }


    public function ajaxGetEmployeeTermsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $eval_component_id = $this->_getParam("eval_component_id");
            $this->view->eval_component_id = $eval_component_id;
            // print_r($academic_id);die;

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getEmployeeAllTerms($academic_year_id, $department_id, $employee_id);
            //echo '<pre>'; print_r($result); die;
            $ExperientialAllotment_model = new Application_Model_ExperientialAllotment(); 
            $result1 = $ExperientialAllotment_model->getEvaluationItems($academic_year_id, $department_id, $employee_id);
            //$EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();

            $this->view->result = $result;
            $this->view->result1 = $result1;
        }
    }

    public function ajaxGetTermsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $component_grade_id = $this->_getParam("component_grade_id");
            if ($component_grade_id) {
                $ComponentGrade_model = new Application_Model_ComponentGrade();
                $grade_result = $ComponentGrade_model->getRecord($component_grade_id);
            }

            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getTerms($academic_year_id, $department_id, $employee_id);
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Terms</label>';
            echo '<select type="text" name="term_id" id="term_id" class="form-control">';
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                $selected = '';
                if ($k == $grade_result['term_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . ' >' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

    public function ajaxGetCoursesAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $component_grade_id = $this->_getParam("component_grade_id");
            if ($component_grade_id) {
                $ComponentGrade_model = new Application_Model_ComponentGrade();
                $grade_result = $ComponentGrade_model->getRecord($component_grade_id);
            }
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getCourses($academic_year_id, $department_id, $employee_id, $term_id);
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control">';
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                $selected = '';
                if ($k == $grade_result['course_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

    public function ajaxGetEvaluationComponentsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");
            $component_grade_id = $this->_getParam("component_grade_id");
            if ($component_grade_id) {
                $ComponentGrade_model = new Application_Model_ComponentGrade();
                $grade_result = $ComponentGrade_model->getRecord($component_grade_id);
            }
            $EvaluationComponents_model = new Application_Model_EvaluationComponents();
            $result = $EvaluationComponents_model->getComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id);
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Components</label>';
            echo '<select type="text" name="component_id" id="component_id" class="form-control">';
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                $selected = '';
                if ($k == $grade_result['component_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

    
    public function ajaxGetEmployeeValidationAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            //print_r($academic_id); die;
            $EvaluationComponents_model = new Application_Model_EvaluationComponents();
            $result = $EvaluationComponents_model->getEvlComponentCount($academic_year_id, $department_id, $employee_id);
            $counts = count($result['ec_id']);
            echo json_encode($counts);
            die;
            //echo '<pre>'; print_r($counts); die;
            // $this->view->result = $result;
        }
    }

    public function ajaxGetVersionAction() {
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('academic_year_id');
            $section = $this->_getParam('section');
            $termDetails = new Application_Model_BatchSchedule();
            $result = $termDetails->getVersion($batch_id, $term_id, $section);

            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['version'] . '" >' . $val['version'] . '</option>';
            }
        }die;
    }
    
    
    public function getSection($term_id){
        
        
            $termDetails = new Application_Model_Section();
            $result = $termDetails->getRecordByTermIndex($term_id);
         $option[''] =  'Select';
            foreach ($result as $k => $val) {

               $option [$val['id']] = $val['name'];
            }
        
        return $option; 
    }
    
    public function ajaxGetSectionAction() {
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $classDetails = new Application_Model_ClassMaster();
            $groupd_class_record = $classDetails->getGroupedRecord();
            foreach($groupd_class_record as $key => $value){
                $data[] = $value['total'];
                
            }
            $total_clas_to_be_created = (max($data));
            $batch_schedule_model = new Application_Model_BatchSchedule();
            $batch_schedule_model->create_column((int)$total_clas_to_be_created,array('class','description','time','room'));
            
            $attendance_model = new Application_Model_Attendance();
            $attendance_model->create_column((int)$total_clas_to_be_created,array('class','faculty'));
            
            $termDetails = new Application_Model_Section();
            $result = $termDetails->getRecordByTermIndex($term_id);

            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['id'] . '" >' . $val['name'] . '</option>';
            }
        }die;
    }
    
   
    
    public function ajaxBatchScheduleViewAction() {
        $this->_helper->layout->disableLayout();
        $EvaluationComponents_model = new Application_Model_BatchSchedule();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');
            $section = $this->_getParam('section');
            $version_id = $this->_getParam('version_id');
            $start_date = $this->_getParam("start_date");
            $end_date = $this->_getParam('end_date');
           // print_r($term_id);exit;
             $erp = new Application_Model_HRMModel();
             $termDetails = new Application_Model_BatchSchedule();
            $result = $termDetails->getTermDetails($batch_id, $term_id);
            $allDetails = new Application_Model_BatchSchedule();
            $resultDetails = $allDetails->getALL($batch_id, $term_id,$section, $version_id);
          
            $result['class_value'] = $resultDetails;
          
            //  $terms_count = strlen($result['term_name']);
            // $term_val = substr($result['term_name'],$terms_count-1);
 
            $course_result = $termDetails->getCourseDetails($batch_id, $term_id);
            $holidayList = new Application_Model_DmiHoliday();
            $all_holiday = $holidayList->getHolidayList($this->holidayCategory);
            //getting record from studentAttendance      
            //print_r($resultDetails);exit;
            
            $class_master = new Application_Model_ClassMaster();
            $no_of_classes = $class_master -> getRecordByTermIdAndBatch(0, 0);
            $times = $class_master->getClassTime($term_id,$batch_id);
            
            $allRecordsFromStudentAttendance = new Application_Model_Attendance();
            
            $studentAttendance = $allRecordsFromStudentAttendance->getRecordByBatchAndTerm($term_id, $batch_id, $no_of_classes, $section);
            $result['Attendance_result'] = $studentAttendance;
            //$holidayList = new Application_Model_DmiHoliday();
            //$all_holiday = $holidayList->getHolidayList();
            //  print($result['start_date']."_".$result['end_date']);
            if(empty($start_date) && empty($end_date)){
            $term_start = explode("/", $result['start_date']);
            $term_end = explode("/", $result['end_date']);
            $result['chek_date'] = $result['start_date'];
            $result['selected_date'] = 0;
            }
            else {
               
                $term_start = explode("/", $start_date);
                $term_end = explode("/", $end_date);
                 $result['selected_date'] = $term_start[1] . "/" . $term_start[0] . "/" . $term_start[2];
                  $result['chek_date'] = $result['start_date'];
                // print_r($result['selected_date']);exit;
                    }
               
            //$start = strtr($result['start_date'], '/', '-');
            //$end = strtr($result['end_date'], '/', '-');
            $start = $term_start[2] . "-" . $term_start[1] . "-" . $term_start[0];
            $end = $term_end[2] . "-" . $term_end[1] . "-" . $term_end[0];
            // print($start."_".$end);
            // print_r($end);exit;
            // $start = date("Y-m-d", strtotime($start));
            // $end = date("Y-m-d", strtotime($end));
            $result['day_diff'] = date_diff2($start, $end);
            
            //echo $result['day_diff'];exit;
            $result['course_result'] = $course_result;
            $result['holidays'] = $all_holiday;
            $result['weekends'] =   explode(',',$erp->getWeeklyOff()[0]['option_value']);
            $class_records = $class_master->getRecords();
            $class_records =$this->mergData($class_records, array('class_name'), count($class_records));
            $this->view->classrecords = $class_records;
            $this->view->no_of_classes = $no_of_classes;
            $this->view->result = $result;
        }
    }
    public function ajaxScheduleComponentsViewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');
            $section = $this->_getParam("section");
            $department = $this->_getParam("department");
               $erp = new Application_Model_HRMModel();

            $termDetails = new Application_Model_BatchSchedule();
            $result = $termDetails->getTermDetails($batch_id, $term_id);
            $allDetails = new Application_Model_BatchSchedule();
            
            $resultDetails = $allDetails->allDetail($batch_id, $term_id, $section);
          
            $result['class_value'] = $resultDetails;
          
            //  $terms_count = strlen($result['term_name']);
            // $term_val = substr($result['term_name'],$terms_count-1);
 
            $course_result = $termDetails->getCourseDetails($batch_id, $term_id,$section);
              
            $holidayList = new Application_Model_DmiHoliday();
            $all_holiday = $holidayList->getHolidayList($this->holidayCategory);
            //getting record from studentAttendance      
            //print_r($resultDetails);exit;
            
            $class_master = new Application_Model_ClassMaster();
            $no_of_classes = $class_master -> getRecordByTermIdAndBatch(0 ,0);
            $class_hours = $class_master->getClassHours($term_id, $batch_id);
            $times = $class_master->getClassTime(0,0);
            $allRecordsFromStudentAttendance = new Application_Model_Attendance();
            
            $studentAttendance = $allRecordsFromStudentAttendance->getRecordByBatchAndTerm($term_id, $batch_id,$no_of_classes,$section);
          
            $result['Attendance_result'] = $studentAttendance;
            //$holidayList = new Application_Model_DmiHoliday();
            //$all_holiday = $holidayList->getHolidayList();
            //  print($result['start_date']."_".$result['end_date']);
            $term_start = explode("/", $result['start_date']);
            $term_end = explode("/", $result['end_date']);
            //$start = strtr($result['start_date'], '/', '-');
            //$end = strtr($result['end_date'], '/', '-');
            $start = $term_start[2] . "-" . $term_start[1] . "-" . $term_start[0];
            $end = $term_end[2] . "-" . $term_end[1] . "-" . $term_end[0];
            // print($start."_".$end);
            // print_r($end);exit;
            // $start = date("Y-m-d", strtotime($start));
            // $end = date("Y-m-d", strtotime($end));
            $result['day_diff'] = date_diff2($start, $end);
            //echo $result['day_diff'];exit;
            $result['course_result'] = $course_result;
            $result['holidays'] = $all_holiday;
            $result['weekends'] =   explode(',',$erp->getWeeklyOff()[0]['option_value']);
           // $room_model = new Application_Model_Room();
            $room_model =  new Application_Model_RoomMapping();
            $rooms = $room_model->getRoomByDepartmentId($department);
            $class_records = $class_master->getRecords();
            $class_records =$this->mergData($class_records, array('class_name'), count($class_records));
            
            $this->view->classrecords = $class_records;
            $this->view->rooms = $rooms;
             $this->view->times = $times;
            $this->view->class_hours = $class_hours;
            $this->view->no_of_classes = $no_of_classes;
            $this->view->result = $result;
        }
    }

    public function ajaxEvaluationComponentsViewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');
            $section = $this->_getParam("section");
            $erp = new Application_Model_HRMModel();
            $termDetails = new Application_Model_BatchSchedule();
            $classMaster = new Application_Model_ClassMaster();
            $result = $termDetails->getTermDetails($batch_id, $term_id);

            $allDetails = new Application_Model_BatchSchedule();
            $resultDetails = $allDetails->allDetails($batch_id, $term_id,$section);
            $result['class_value'] = $resultDetails;

            //  $terms_count = strlen($result['term_name']);
            // $term_val = substr($result['term_name'],$terms_count-1);

            $course_result = $termDetails->getCourseDetails($batch_id, $term_id);
            $holidayList = new Application_Model_DmiHoliday();
            $all_holiday = $holidayList->getHolidayList($this->holidayCategory);
            //$holidayList = new Application_Model_DmiHoliday();
            //$all_holiday = $holidayList->getHolidayList();
            //  print($result['start_date']."_".$result['end_date']);
             $allRecordsFromStudentAttendance = new Application_Model_Attendance();
            $studentAttendance = $allRecordsFromStudentAttendance->getRecordByBatchAndTerm($term_id, $batch_id);
            $result['Attendance_result'] = $studentAttendance;
            
            
            $term_start = explode("/", $result['start_date']);
            $term_end = explode("/", $result['end_date']);
            //$start = strtr($result['start_date'], '/', '-');
            //$end = strtr($result['end_date'], '/', '-');
            $start = $term_start[2] . "-" . $term_start[1] . "-" . $term_start[0];
            $end = $term_end[2] . "-" . $term_end[1] . "-" . $term_end[0];
            // print($start."_".$end);
            // print_r($end);exit;
            // $start = date("Y-m-d", strtotime($start));
            // $end = date("Y-m-d", strtotime($end));
            $result['day_diff'] = date_diff2($start, $end);
            //echo $result['day_diff'];exit;
            $result['course_result'] = $course_result;
            $result['holidays'] = $all_holiday;
             $result['weekends'] =   explode(',',$erp->getWeeklyOff()[0]['option_value']);
            $this->view->result = $result;
        }
    }
    
    
      public function ajaxGetBatchSceduleSessionsAction() {
       $term = new Application_Model_TermMaster();
        $term_id = $this->_getParam('term_id');
        $academic_year_id = $this->_getParam('batch_id');
        $section = $this-_getParam('section');
        $start_date = $this->_getParam('start_date');
        $end_date = $this->_getParam('end_date');
      //  print_r($term_id);exit;
         $term_start = explode("/", $start_date);
            $term_end = explode("/", $end_date);
            
            $start1 = date_create($term_start[2] . "-" . $term_start[1] . "-" . $term_start[0]);
            $end1 = date_create($term_end[2] . "-" . $term_end[1] . "-" . $term_end[0]);
            
   
        $version_id = $this->_getParam('version');
        //getting all the courses 
        $courses = $term->getCourses($academic_year_id,$term_id);
      
        if(count(Courses)>0){
             $result['course_details'] = $this->getCourseNames($courses,date_format($start1,"Y-m-d"), date_format($end1, "Y-m-d"), $academic_year_id, $term_id, $version_id,$section);
        }
        else{
         echo 'No Courses';   exit;
        }
       foreach($result['course_details'] as $key => $value){
          $result['course_details'][$key]['course_count'] = $result['course_details']['course_count'][$key];
       }
      
        echo json_encode($result['course_details']);exit;
    }
    
      public function ajaxGetBatchSceduleSessions1Action() {
       $term = new Application_Model_TermMaster();
    
        $term_id = $this->_getParam('term_id');
        $academic_year_id = $this->_getParam('batch_id');
        $section = $this->_getParam('section');
        
        $dates = $term->getStartAndEndDate($term_id, $academic_year_id);
    
              $term_start = explode("/", $dates['start_date']);
            $term_end = explode("/", $dates['end_date']);
            $start1 = date_create($term_start[2] . "-" . $term_start[1] . "-" . $term_start[0]);
            $end1 = date_create($term_end[2] . "-" . $term_end[1] . "-" . $term_end[0]);
            
           $version_id = $term->getMaxVersion(date_format($start1,"d-m-Y"))['version'];
        
           
            
        //getting all the courses 
        $courses = $term->getCourses($academic_year_id,$term_id);
      
        if(count(Courses)>0){
             $result['course_details'] = $this->getCourseNames($courses,date_format($start1,"Y-m-d"), date_format($end1, "Y-m-d"), $academic_year_id, $term_id, $version_id,$section);
        }
        else{
         echo 'No Courses';   exit;
        }
       foreach($result['course_details'] as $key => $value){
          $result['course_details'][$key]['course_count'] = $result['course_details']['course_count'][$key];
       }
      
        echo json_encode($result['course_details']);exit;
    }
    
    
    //function to get course names
    public function getCourseNames(array $courses, $start_date, $end_date, $batch_id, $term_id, $version_id,$section){
      
           $course_name = new Application_Model_TermMaster();
      $result = array();
      $i=0;
        foreach($courses as $key){
           $result[$key['course_id']] = $course_name->getCourseName($key['course_id']);
          
        }
         foreach($result as $key => $value){
        $result['course_count'][$key] = (int)$course_name->getCourseReport($value['course_code'],$batch_id, $term_id,$section, $version);
        }
        return $result;
    }
    
    
    public function ajaxValidateAction(){
           $this->_helper->layout->disableLayout();
            $batch_schedule = new Application_Model_BatchSchedule();
            $course_master = new Application_Model_Course();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $version_details  = $batch_schedule->getAllversion();
            
            $course_id = $this->_getParam('course_id');
            $class_name = $this->_getParam('class_name');
            
            
            preg_match_all('/\d+/', $class_name,$arr);
            $stiky = (string)$arr[0][0];
            $class = 'class_'.$arr[0][0];
            $day = explode($stiky,$class_name);
           
            $faculty_arr = $this->getFaculty($course_id);
            $db_course = array();
            foreach($version_details as $key => $value){
            $db_course[$value['batch']][$value['term_id']][$value['max_version']][$value['section']] = $batch_schedule->getCourseByoneClass($class,$day[1],$value['max_version'],$value['section']);
            }
           
            $course_key = array();
            foreach($db_course as $batch_id => $batch_val){
                foreach($batch_val as $term_id => $term_val){
                    foreach($term_val as $version => $version_val){
                       foreach($version_val as $key => $value){
                          $course_key[] = $value[$class];
                       }
                       } 
                    }
                }
                  
                $course_key = array_filter($course_key, function($value){return $value != '';});
                
                $course_key = array_unique($course_key);
                $db_faculty_arr = array();
                foreach($course_key as $key => $value){
                $db_faculty_arr[$value] = $this->getFaculty($value);
                }
                $db_faculty = [];
                
                foreach($db_faculty_arr as $course_code => $course_value){
                     foreach($course_value as $key => $value){
                        $db_faculty[] = $value;
                     }
                }
                $result = array_intersect($faculty_arr, $db_faculty);
              
            echo count($result)>1?1:0;exit;
          
            
        }
    } 
    
    
    public function getFaculty($course_id){
           $batch_schedule = new Application_Model_BatchSchedule();
        $result = $batch_schedule->getFacultybyCourseId($course_id);
         $faculty_arr = array();
            foreach($result as $key => $value){
            $faculty_arr[] = $value['employee_id'];
            foreach(explode(',',$value['faculty_id']) as $faculty_key => $faculty_value){
                $faculty_arr[] = $faculty_value;
            }
            foreach(explode(',',$value['visiting_faculty_id']) as $faculty_key => $faculty_value){
                $faculty_arr[] = $faculty_value;
            }
            }
            $faculty_arr = array_filter($faculty_arr, function($value){ return $value != '' ;});
            $faculty_arr = array_filter($faculty_arr, function($value){ return $value != 'NA' ;});
            return array_unique($faculty_arr);
    }  

}

function date_diff2($date1 = '', $date2 = '') {


    $diff = abs(strtotime($date2) - strtotime($date1));

    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff ) / (60 * 60 * 24));
    return (int) $days;
}

