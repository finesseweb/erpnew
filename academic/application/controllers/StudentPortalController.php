<?php

class StudentPortalController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    Private $_unit_id = null;
    Private $_baseurl = NULL;
    private $accessConfig =NULL;

    public function init() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $accessConfig = new accessLevel();
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $this->_baseurl = $config['host'];
        $this->_action = $this->getRequest()->getActionName();
        //access role id
        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->studentConfig = $zendConfig->student_holiday_controller->toArray();
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
        $this->_act = new Application_Model_Adminactions();
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        if ($data->role_id != 0)
            $this->_redirect("index/");
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
        $this->view->action_name = 'studentinfo';
        $this->view->sub_title_name = 'Student';
        $student_model = new Application_Model_StudentPortal();
        $student_form = new Application_Form_StudentPortal();
        $participant_model = new Application_Model_ParticipantsLogin();
        $alumni_model = new Application_Model_Alumni();
        $student_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_form;

        switch ($type) {
            
        }
    }

    //===========[STUDENT DASHBOARD]=================//
    public function studentDashboardAction() {
        $this->view->action_name = 'student-dashboard';
        $this->view->sub_title_name = 'Student Dashboard';
        $student_dashboard_form = new Application_Form_StudentDashboard();
        $this->view->form = $student_dashboard_form;
    }

    //================[RESULT COMMAND]==============//
    public function resultAction() {
        $this->view->action_name = 'result';
        $this->view->sub_title_name = 'Student Result';
        $student_model = new Application_Model_StudentPortal();
        $student_form = new Application_Form_StudentPortal();
        $participant_model = new Application_Model_ParticipantsLogin();
        $alumni_model = new Application_Model_Alumni();
        $student_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    
                }
                break;
            case 'edit':
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $student_model->getRecord($student_id);
                $alumni_detail = $student_model->getAlumniDetail($student_id);
                $this->view->alumni_detail = $alumni_detail;
                $student_form->populate($result);
                // print_r($result);exit;
                $this->view->result = $result;

                break;
            case 'delete':
                $data['status'] = 2;

                break;

            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $student_model->getRecords();
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

    //====================[ATTENDANCE COMMAND STARTS]============//    


    public function ajaxGetAttendanceViewAction() {
        $student_form = new Application_Form_StudentElement();

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $atendence_saver_model = new Application_Model_Attendance();
            $class_master = new Application_Model_ClassMaster();
            $employee_model = new Application_Model_HRMModel();
            $course_report = new Application_Model_CourseReport();
            $present = 0;
            $absent = 0;
            $leave = 0;
            $total_class = 0;
            $total_absent = 0;
            $total_present = 0;
            $total_leave = 0;

            $batch_id = $this->login_storage->participant_academic;

           // $running_terms = $this->getRecentTerm($batch_id);
             
               $no_of_classes = $class_master->getRecordByTermIdAndBatch(0, 0);
               
            $single_result = array();
            if (!empty($term_id)) {
                
                $course_result = $atendence_saver_model->getCourseDetails($term_id, $batch_id);

                $course_count = $course_report->getTotalNumberOfDays($term_id, $batch_id);
                foreach ($course_result as $key => $value) {
                    $single_result[$key]['course_code'] = $value['course_code'];
                    $single_result[$key]['course_name'] = $value['course_name'];
                    $single_result[$key]['course_id'] = $value['course_id'];
                    foreach ($course_count as $key1 => $value1) {
                        if ($value['course_code'] == $value1['course_code']) {
                              for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
                        ${"result_class$dcl"}[$key]['p'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);
          

                        ${"result_class$dcl"}[$key]['A'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, 'Absent' . "-" . $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);


                        ${"result_class$dcl"}[$key]['L'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, 'Leave' . "-" . $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);




                        ${"result_class$dcl"}[$key]['updated_date'] = $atendence_saver_model->getRecordByStudentIdUpdateByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);
               


                        if (!empty(${"result_class$dcl"}[$key]['updated_date']))
                            $single_result[$key]['updated_date'] = $result_class1[$key]['updated_date'];
                        //============[FOR LEAVE]===========//


                        $single_result[$key]['p'] += ${"result_class$dcl"}[$key]['p'];
                        $single_result[$key]['A'] += ${"result_class$dcl"}[$key]['A'];
                        $single_result[$key]['L'] += ${"result_class$dcl"}[$key]['L'] ;





                       
                        $total_present += ${"result_class$dcl"}[$key]['p'] ;
                        $total_absent += ${"result_class$dcl"}[$key]['A'] ;
                        $total_leaves += ${"result_class$dcl"}[$key]['L'] ;
                        }
                         $single_result[$key]['total_class'] = $value1['course_count'];
                        $total_class += $value1['course_count'];
                        $single_result[$key]['rest_days'] = $value1['course_count'] - ($single_result[$key]['p'] + $single_result[$key]['A'] + $single_result[$key]['L']);
                        }
                    }
                }
            }


            $this->view->result = $single_result;
        }
    }

    public function attendanceAction() {
        $this->view->action_name = 'attendance';
        $this->view->sub_title_name = 'Student Attendance';
        $student_form = new Application_Form_StudentElement();

        $this->view->form = $student_form;
        $atendence_saver_model = new Application_Model_Attendance();
        $class_master = new Application_Model_ClassMaster();
        $employee_model = new Application_Model_HRMModel();
        $course_report = new Application_Model_CourseReport();
        $present = 0;
        $absent = 0;
        $leave = 0;
        $total_class = 0;
        $total_absent = 0;
        $total_present = 0;
        $total_leave = 0;

        $batch_id = $this->login_storage->participant_academic;
        
        $running_terms = $this->getRecentTerm($batch_id);
        

        $term_id = $running_terms['term_id'];
        $single_result = array();
           $no_of_classes = $class_master->getRecordByTermIdAndBatch(0, 0);
        if (!empty($term_id)) {
            $course_result = $atendence_saver_model->getCourseDetails($term_id, $batch_id);

            $course_count = $course_report->getTotalNumberOfDays($term_id, $batch_id);
            foreach ($course_result as $key => $value) {
                $single_result[$key]['course_code'] = $value['course_code'];
                $single_result[$key]['course_name'] = $value['course_name'];
                $single_result[$key]['course_id'] = $value['course_id'];
                foreach ($course_count as $key1 => $value1) {
                    if ($value['course_code'] == $value1['course_code']) {
  for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
                        ${"result_class$dcl"}[$key]['p'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);
                        ${"result_class$dcl"}[$key]['A'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, 'Absent' . "-" . $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);
                        ${"result_class$dcl"}[$key]['L'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, 'Leave' . "-" . $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);
                        ${"result_class$dcl"}[$key]['updated_date'] = $atendence_saver_model->getRecordByStudentIdUpdateByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);
                        if (!empty(${"result_class$dcl"}[$key]['updated_date']))
                            $single_result[$key]['updated_date'] = $result_class1[$key]['updated_date'];
                        //============[FOR LEAVE]===========//


                        $single_result[$key]['p'] += ${"result_class$dcl"}[$key]['p'];
                        $single_result[$key]['A'] += ${"result_class$dcl"}[$key]['A'];
                        $single_result[$key]['L'] += ${"result_class$dcl"}[$key]['L'] ;





                       
                        $total_present += ${"result_class$dcl"}[$key]['p'] ;
                        $total_absent += ${"result_class$dcl"}[$key]['A'] ;
                        $total_leaves += ${"result_class$dcl"}[$key]['L'] ;
                        }
                         $single_result[$key]['total_class'] = $value1['course_count'];
                        $total_class += $value1['course_count'];
                        $single_result[$key]['rest_days'] = $value1['course_count'] - ($single_result[$key]['p'] + $single_result[$key]['A'] + $single_result[$key]['L']);
                    }
                }
            }
        }


        $this->view->result = $single_result;
    }

    public function ajaxGetAttendanceAction() {

        $atendence_saver_model = new Application_Model_Attendance();
        $class_master = new Application_Model_ClassMaster();
        $employee_model = new Application_Model_HRMModel();
        $course_report = new Application_Model_CourseReport();
        $present = 0;
        $absent = 0;
        $leave = 0;
        $total_class = 0;
        $total_absent = 0;
        $total_present = 0;
        $total_leave = 0;

        $batch_id = $this->login_storage->participant_academic;

        $running_terms = $this->getRecentTerm($batch_id);
        $term_id = $this->_getParam("term_id");
        $batch_id = $this->_getParam("batch_id");
        $month = $this->_getParam('month');
        $year = $this->_getParam('year');
        $no_of_classes = $class_master->getRecordByTermIdAndBatch(0, 0);
        $single_result = array();
        if (!empty($term_id)) {
            $course_result = $atendence_saver_model->getCourseDetails($term_id, $batch_id);

            $course_count = $course_report->getTotalNumberOfDays($term_id, $batch_id);
            foreach ($course_result as $key => $value) {
                $single_result[$key]['course_code'] = $value['course_code'];
                $single_result[$key]['course_name'] = $value['course_name'];
                $single_result[$key]['course_id'] = $value['course_id'];
                foreach ($course_count as $key1 => $value1) {
                    if ($value['course_code'] == $value1['course_code']) {
                        for($dcl = 1; $dcl<=$no_of_classes; $dcl++){
                        ${"result_class$dcl"}[$key]['p'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);
          

                        ${"result_class$dcl"}[$key]['A'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, 'Absent' . "-" . $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);


                        ${"result_class$dcl"}[$key]['L'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, 'Leave' . "-" . $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);




                        ${"result_class$dcl"}[$key]['updated_date'] = $atendence_saver_model->getRecordByStudentIdUpdateByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, "class_$dcl", $month, $year);
               


                        if (!empty(${"result_class$dcl"}[$key]['updated_date']))
                            $single_result[$key]['updated_date'] = $result_class1[$key]['updated_date'];
                        //============[FOR LEAVE]===========//


                        $single_result[$key]['p'] += ${"result_class$dcl"}[$key]['p'];
                        $single_result[$key]['A'] += ${"result_class$dcl"}[$key]['A'];
                        $single_result[$key]['L'] += ${"result_class$dcl"}[$key]['L'] ;





                       
                        $total_present += ${"result_class$dcl"}[$key]['p'] ;
                        $total_absent += ${"result_class$dcl"}[$key]['A'] ;
                        $total_leaves += ${"result_class$dcl"}[$key]['L'] ;
                        }
                         $single_result[$key]['total_class'] = $value1['course_count'];
                        $total_class += $value1['course_count'];
                        $single_result[$key]['rest_days'] = $value1['course_count'] - ($single_result[$key]['p'] + $single_result[$key]['A'] + $single_result[$key]['L']);
                    }
                }
            }
        }

        $attendance_result['total_class'] = $total_class;
        $attendance_result['total_present'] = $total_present;
        $attendance_result['total_absent'] = $total_absent;
        $attendance_result['total_leaves'] = $total_leaves;

        echo json_encode($attendance_result);
        die;
    }

//====================[ATTENDANCE COMMAND ENDS]============//   
    //==============[ASSIGNMENTS]==============//




    public function ajaxGetAssignmentViewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $assignment_model = new Application_Model_Assignment();
            $assignment_form = new Application_Form_Assignment();
            $student_form = new Application_Form_StudentElement();
            $student_assignment_model = new Application_Model_SubmitAssignment();
            $not_uploaded = '';
            $not_uploaded_status = 0;



            if (isset($_POST['submit'])) {

                $data = $_POST['selected_id'];
                $result = array();
                foreach ($data as $key => $id) {
                    $result['description'] = $_POST["description_$id"];
                    $result['assignment_status'] = $_POST["assignment_status_$id"];
                    $result['stu_updated_date'] = date('Y-m-d');


                    $dirPath = APPLICATION_PATH . '/../public/Assignments/student/' . $id . '/assignment_details/';
                    //print_r($dirPath);exit;	
                    if (!file_exists($dirPath)) {
                        mkdir($dirPath, 755, true);
                    }
                    $file_name = $_FILES["uploadFile_$id"]["name"][$key];

                    $tem_name = $_FILES["uploadFile_$id"]["tmp_name"][$key];
                    $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "pdf" || $imageFileType == "doc" || $imageFileType == "docx" || $imageFileType == "ppt" || $imageFileType == "xls") {
                        if (move_uploaded_file($tem_name, $dirPath . $file_name)) {
                            $result['upload_file'] = "public/Assignments/student/" . $id . "/assignment_details/" . $file_name;
                            $student_assignment_model->update($result, array('submitted_id=?' => $id));
                            $_SESSION['message'] = 'Assignments submitted Successfully';
                        } else {
                            $not_uploaded .= $file_name . ',';
                            $not_uploaded_status = 1;
                        }
                    } else {
                        $not_uploaded .= $file_name . ',';
                        $not_uploaded_status = 1;
                    }
                }

                if ($not_uploaded_status == 1)
                    $_SESSION['message'] = 'Your Respected file filename "' . $not_uploaded . '" is unable to upload please check size !';
                $this->_redirect('assignment');
            }

            switch ($type) {
                case "add":
                    if ($this->getRequest()->isPost()) {
                        
                    }
                    break;
                case 'read':
                    $data = array('notification_status' => 1);
                    $student_assignment_model->update($data, array('assignment_id=?' => $id, 'student_id=?' => $_SESSION['admin_login']['admin_login']->student_id));
                    $result = $assignment_model->getRecordsByCurrentBatchAndTerm($_SESSION['admin_login']['admin_login']->participant_academic, $term_id);

                    $i = 0;
                    foreach ($result as $key) {
                        $file_arr = explode('/', $key['upload_file']);
                        $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                        $result[$i]['filename1'] = $file_name[0];
                        $file_arr = explode('/', $key['filename']);
                        $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                        $result[$i]['filename2'] = $file_name[0];
                        $result[$i]['course_id'] = $assignment_model->getCourseName($key['course_id']);
                        if ($key['assignment_status'] == 0)
                            $result[$i]['assignment_status'] = 'Incomplete';
                        else
                            $result[$i]['assignment_status'] = 'Completed';
                        $i++;
                    }
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);

                    break;
                case 'delete':
                    $data['status'] = 2;

                    break;

                default:
                    $messages = $this->_flashMessenger->getMessages();
                    $this->view->messages = $messages;
                    $result = $assignment_model->getRecordsByCurrentBatchAndTerm($_SESSION['admin_login']['admin_login']->participant_academic, $term_id);

                    $i = 0;
                    foreach ($result as $key) {
                        $file_arr = explode('/', $key['upload_file']);
                        $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                        $result[$i]['filename1'] = $file_name[0];
                        $file_arr = explode('/', $key['filename']);
                        $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                        $result[$i]['filename2'] = $file_name[0];
                        $result[$i]['course_id'] = $assignment_model->getCourseName($key['course_id']);
                        if ($key['assignment_status'] == 0)
                            $result[$i]['assignment_status'] = 'Incomplete';
                        else
                            $result[$i]['assignment_status'] = 'Completed';

                        $i++;
                    }
                    // print_r($result);exit;
                    $page = $this->_getParam('page', 1);
                    $paginator_data = array(
                        'page' => $page,
                        'result' => $result
                    );
                    $this->view->paginator = $this->_act->pagination($paginator_data);
                    break;
            }
        }
    }

    public function assignmentsAction() {
        $this->view->action_name = 'assignments';
        $this->view->sub_title_name = 'Student Assignments';
        $assignment_model = new Application_Model_Assignment();
        $assignment_form = new Application_Form_Assignment();
        $student_form = new Application_Form_StudentElement();
        $student_assignment_model = new Application_Model_SubmitAssignment();
        $this->view->form = $student_form;
        $recent_batch = $this->getRecentBatch();
        if (!$recent_batch['recent']['batch_id']) {
            $recent_batch['recent']['batch_id'] = 0;
            $recent_batch['recent']['term_id'] = 0;
        }
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_form;
        $not_uploaded = '';
        $not_uploaded_status = 0;
        if (isset($_POST['submit'])) {

            $data = $_POST['selected_id'];
            //  print_r($data);exit;
            $result = array();
            foreach ($data as $key => $id) {

                $result['description'] = $_POST["description_$id"];
                $result['assignment_status'] = $_POST["assignment_status_$id"];
                $result['stu_updated_date'] = date('Y-m-d');
                // echo "<pre>"; print_r($result); exit;
                $dirPath = APPLICATION_PATH . '/../public/Assignments/student/' . $id . '/assignment_details/';
                //print_r($dirPath);exit;	
                if (!file_exists($dirPath)) {
                    mkdir($dirPath, 755, true);
                }
                $file_name = $_FILES["uploadFile_$id"]["name"];

                $tem_name = $_FILES["uploadFile_$id"]["tmp_name"];
                //  print_r($tem_name);exit;

                $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "pdf" || $imageFileType == "doc" || $imageFileType == "docx" || $imageFileType == "ppt" || $imageFileType == "xls") {

                    if (move_uploaded_file($tem_name, $dirPath . $file_name)) {
                        $result['upload_file'] = "public/Assignments/student/" . $id . "/assignment_details/" . $file_name;
                        $student_assignment_model->update($result, array('submitted_id=?' => $id));
                        $_SESSION['message'] = 'Assignments submitted Successfully';
                        $_SESSION['class'] = 'alert-success';
                    } else {
                        $not_uploaded .= $file_name . ',';
                        $not_uploaded_status = 1;
                    }
                } else {
                    $not_uploaded .= $file_name . ',';
                    $not_uploaded_status = 1;
                }
            }
            //   print_r($not_uploaded);exit;
            if ($not_uploaded_status == 1) {
                $_SESSION['message'] = 'Please upload with your documents 1 !';
                $_SESSION['class'] = 'alert-danger';
            }

            $this->_redirect('assignment');
        }
        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    
                }
                break;
            case 'read':
                $data = array("notification_status" => 1);

                $student_assignment_model->update($data, array('assignment_id=?' => $id, 'student_id=?' => $_SESSION['admin_login']['admin_login']->student_id));
                $result = $assignment_model->getRecordsByCurrentBatchAndTerm($recent_batch['recent']['batch_id'], $recent_batch['recent']['term_id']);

                $i = 0;
                foreach ($result as $key) {
                    $file_arr = explode('/', $key['upload_file']);
                    $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                    $result[$i]['filename1'] = $file_name[0];
                    $file_arr = explode('/', $key['filename']);
                    $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                    $result[$i]['filename2'] = $file_name[0];
                    $result[$i]['course_id'] = $assignment_model->getCourseName($key['course_id']);
                    if ($key['assignment_status'] == 0)
                        $result[$i]['assignment_status'] = 'Incomplete';
                    else
                        $result[$i]['assignment_status'] = 'Completed';

                    $i++;
                }

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);

                break;
            case 'delete':
                $data['status'] = 2;

                break;

            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                //print_r($term_id."====".$recent_batch['recent']['term_id']);exit;
                $result = $assignment_model->getRecordsByCurrentBatchAndTerm($_SESSION['admin_login']['admin_login']->participant_academic, $recent_batch['recent']['term_id']);
                // $result = $assignment_model->getRecordsByCurrentBatchAndTerm($recent_batch['recent']['batch_id'], $recent_batch['recent']['term_id']);

                $i = 0;
                foreach ($result as $key) {
                    $file_arr = explode('/', $key['upload_file']);
                    $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                    $result[$i]['filename1'] = $file_name[0];
                    $file_arr = explode('/', $key['filename']);
                    $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                    $result[$i]['filename2'] = $file_name[0];
                    $result[$i]['course_id'] = $assignment_model->getCourseName($key['course_id']);
                    if ($key['assignment_status'] == 0)
                        $result[$i]['assignment_status'] = 'Incomplete';
                    else
                        $result[$i]['assignment_status'] = 'Completed';
                    $i++;
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

    public function ajaxGetNotificationAction() {
        $this->view->action_name = 'assignments';
        $this->view->sub_title_name = 'Student Assignments';
        $assignment_model = new Application_Model_Assignment();
        $assignment_form = new Application_Form_Assignment();
        $term_id = $this->_getParam("_param1");
        $batch_id = $this->_getParam('_param0');


        $recent_batch = $this->getRecentBatch();
        // echo "<pre>";print_r($recent_batch); exit;
        if (!$recent_batch['recent']['batch_id']) {
            $recent_batch['recent']['batch_id'] = 0;
            $recent_batch['recent']['term_id'] = 0;
        }
        $recent_batch['recent']['batch_id'] = $batch_id;
        $recent_batch['recent']['term_id'] = $term_id;
        $result = $assignment_model->getRecordsByCurrentBatchAndTerm1($recent_batch['recent']['batch_id'], $recent_batch['recent']['term_id']);


        $i = 0;
        foreach ($result as $key) {
            //echo "<pre>";print_r($key);exit;
            $file_arr = explode('/', $key['filename']);
            $file_name = explode(".", $file_arr[count($file_arr) - 1]);
            $result[$i]['assignment_id'] = $this->_baseurl . "student-portal/assignments/type/read/id/" . $key['assignment_id'];
            $result[$i]['filename1'] = $file_name[0];
            $result[$i]['course_id'] = $assignment_model->getCourseName($key['course_id']);
            $i++;
        }
        // echo "<pre>"; print_r($result);exit;
        echo json_encode($result);
        die;
    }

    //===============[CLASS_SECHEDULE CODE GOES HERE]===========// 
    public function classScheduleAction() {
        $this->view->action_name = 'class-schedule';
        $this->view->sub_title_name = 'Student Batch Schedule';
        $student_model = new Application_Model_StudentPortal();
        $student_form = new Application_Form_StudentElement();
        $participant_model = new Application_Model_ParticipantsLogin();
        $alumni_model = new Application_Model_Alumni();
        $student_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    
                }
                break;
            case 'edit':
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $student_model->getRecord($student_id);
                $alumni_detail = $student_model->getAlumniDetail($student_id);
                $this->view->alumni_detail = $alumni_detail;
                $student_form->populate($result);
                // print_r($result);exit;
                $this->view->result = $result;

                break;
            case 'delete':
                $data['status'] = 2;

                break;

            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $student_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //===========[FESS CODE GOES HERE]==============//
    public function feeStatusAction() {
        $this->view->action_name = 'structure';
        $this->view->sub_title_name = 'Fee Structure';
        $FeeStructure_model = new Application_Model_FeeStructure();
        $FeeStructure_form = new Application_Form_FeeStructure();
        $FeeStructureItems_model = new Application_Model_FeeStructureItems();
        $FeeStructureTermItems_model = new Application_Model_FeeStructureTermItems();
        $structure_id = $this->_getParam("id");
        //print_r($feehead_id);die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $FeeStructure_form;

        switch ($type) {
            case "add":


                break;
            case 'edit':

                break;
            case 'delete':

                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $FeeStructure_model->getRecordsForStudent();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function ajaxGetFeeDetailsViewAction() {


        $FeeCategory_model = new Application_Model_FeeCategory();
        $FeeHeads_model = new Application_Model_FeeHeads();
        $FeeStructure_model = new Application_Model_FeeStructure();
        $StructureItems_model = new Application_Model_FeeStructureItems();
        $term_model = new Application_Model_TermMaster();
        $TermItems_model = new Application_Model_FeeStructureTermItems();
        $structure_id = $this->_getParam("id");
        $this->view->structure_id = $structure_id;

        $type = $this->_getParam("type");
        $this->view->type = $type;
        //$this->view->form = $GradeAllocationReport_form;
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $result = $TermItems_model->getItemRecords($structure_id);
            $this->view->result = $result;
            $result1 = $StructureItems_model->getStructureRecords($structure_id);
            $this->view->result1 = $result1;
            $academic_year_id = $TermItems_model->getAcademicId($structure_id);

            $terms_data = $term_model->getRecordByAcademicId($academic_year_id['academic_id']);
            $this->view->term_data = $terms_data;

            $Category_data = $FeeCategory_model->getCategory();
            $this->view->Category_data = $Category_data;
            $Feeheads_data = $FeeHeads_model->getFeeheads();
            // print_r($Feeheads_data);die;
            $this->view->Feeheads_data = $Feeheads_data;





            /* $FeeCategory_model = new Application_Model_FeeCategory();
              $FeeHeads_model = new Application_Model_FeeHeads();
              $FeeStructure_model = new Application_Model_FeeStructure();
              $StructureItems_model = new Application_Model_FeeStructureItems();
              $TermItems_model = new Application_Model_FeeStructureTermItems();
              $structure_id = $this->_getParam("id");
              $this->view->structure_id = $structure_id;
              //print_r($structure_id); die;
              $type = $this->_getParam("type");
              $this->view->type = $type;
              //$this->view->form = $GradeAllocationReport_form;
              $this->_helper->layout->disableLayout();
              if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
              $result = $TermItems_model->getItemRecords($structure_id);
              //print_r($result);die;
              $this->view->result = $result;
              $result1 = $StructureItems_model->getStructureRecords($structure_id);
              //print_r($result1);die;
              $this->view->result1 = $result1;

              $Category_data = $FeeCategory_model->getCategory();
              $this->view->Category_data = $Category_data;
              $Feeheads_data = $FeeHeads_model->getFeeheads();
              // print_r($Feeheads_data);die;
              $this->view->Feeheads_data = $Feeheads_data;
             * 
             */
        }
    }

    ///=============[DMI HOLIDAY GOES HERE]===============// 
    public function holidayAction() {
        $this->view->action_name = 'holiday';
        $this->view->sub_title_name = 'Student Holiday';
        $holidayList = new Application_Model_DmiHoliday();
        $all_holiday = $holidayList->getHolidayList($this->holidayCategory[0]);

        $this->view->result = $all_holiday;
    }

    public function ajaxScheduleComponentsViewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');

            $termDetails = new Application_Model_BatchSchedule();
            $result = $termDetails->getTermDetails($batch_id, $term_id);
            $allDetails = new Application_Model_BatchSchedule();
            $resultDetails = $allDetails->allDetail($batch_id, $term_id);
            $result['class_value'] = $resultDetails;

            //  $terms_count = strlen($result['term_name']);
            // $term_val = substr($result['term_name'],$terms_count-1);

            $course_result = $termDetails->getCourseDetails($batch_id, $term_id);
            $holidayList = new Application_Model_DmiHoliday();
            $all_holiday = $holidayList->getHolidayList($this->holidayCategory);
            //getting record from studentAttendance      
            //print_r($resultDetails);exit;
            $allRecordsFromStudentAttendance = new Application_Model_Attendance();
            $studentAttendance = $allRecordsFromStudentAttendance->getRecordByBatchAndTerm($term_id, $batch_id);
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
            $result['day_diff'] = $this->date_diff2($start, $end);
            //echo $result['day_diff'];exit;
            $result['course_result'] = $course_result;
            $result['holidays'] = $all_holiday;
            $this->view->result = $result;
        }
    }

    function date_diff2($date1 = '', $date2 = '') {


        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff ) / (60 * 60 * 24));
        return (int) $days;
    }

    public function ajaxGetStudentsByBatchAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            if ($batch_id) {
                $StudentPortal_model = new Application_Model_StudentPortal();
                $students = $StudentPortal_model->getDistincetStudentsByBatchId($batch_id);

                echo '<option value="">Select </option>';
                foreach ($students as $k => $val) {
                    echo '<option value="' . $val['stu_id'] . '" >' . $val['students'] . ' (' . $val['stu_id'] . ')</option>';
                }
            }
        }
        die;
    }

    //============[Feed BAck Action]==========//

    public function feedBackInstructorAction() {
        $this->view->action_name = 'FeedBack';
        $this->view->sub_title_name = 'Student FeedBack Instructor';
        $student_feed_form = new Application_Form_StudentFeedElement();
        $student_feed_model = new Application_Model_StudentFeed();
        $instructor_feed = new Application_Model_InstructorFeed();
        $this->view->form = $student_feed_form;

        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getPost()) {
                $data['course'] = $this->getRequest()->getPost('course');
                $data['instructor'] = $this->getRequest()->getPost('instructor');
                $data['batch'] = $this->getRequest()->getPost('batch');
                $data['term'] = $this->getRequest()->getPost('term');
                //  print_r($data);exit;
                $len = $this->getRequest()->getPost('count');
                $len1 = $this->getRequest()->getPost('count1');

                $bool = $student_feed_model->hasRated($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['term'], $data['batch']);

                $bool1 = $instructor_feed->hasRated($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['instructor'], $data['term'], $data['batch']);


                if ($bool == 0) {
                    for ($i = 1; $i <= count($len); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_$i"));

                        if (is_array($arr) && strlen($arr[1]) == 1) {
                            $data['feed'] = $arr[1];
                            $data['question_no_c'] = $arr[0];
                        } else {
                            if (empty($arr[0]))
                                $arr[0] = null;

                            $data['feed'] = $arr[0];

                            $data['question_no_c'] = $this->getRequest()->getPost("Q_$i");
                        }



                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        $student_feed_model->insert($data);
                    }
                }

                if ($bool > 0) {
                    $k = 0;
                    $bool = $student_feed_model->hasRatedQuestion($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['instructor'], $data['term'], $data['batch']);
                    for ($i = 1; $i <= count($len); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_$i"));

                        if (is_array($arr) && strlen($arr[1]) == 1) {
                            $data['feed'] = $arr[1];
                            $data['question_no_c'] = $arr[0];
                        } else {
                            if (empty($arr[0]))
                                $arr[0] = null;

                            $data['feed'] = $arr[0];
                            $data['question_no_c'] = $this->getRequest()->getPost("Q_$i");
                        }


                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;

                        if ($bool == 0) {
                            $student_feed_model->update($data, array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'course=?' => $data['course'], 'question_no_c=?' => $data['question_no_c'], 'term=?' => $data['term'], 'batch=?' => $data['batch']));
                        } else {

                            if ($k == 0) {
                                $student_feed_model->delete(array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'course=?' => $data['course'], 'term=?' => $data['term'], 'batch=?' => $data['batch']));
                                $k = $k + 1;
                            }

                            $student_feed_model->insert($data);
                        }
                    }
                }


                if ($bool1 > 0) {

                    $k = 0;
                    $bool = $instructor_feed->hasRatedQuestion($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['term'], $data['batch']);

                    for ($i = 1; $i <= count($len1); $i++) {

                        $arr = explode("-", $this->getRequest()->getPost("rate_I$i"));

                        if (is_array($arr) && strlen($arr[1]) == 1) {
                            $data['feed'] = $arr[1];
                            $data['question_no_c'] = $arr[0];
                        } else {
                            if (empty($arr[0]))
                                $arr[0] = null;
                            $data['feed'] = $arr[0];
                            $data['question_no_c'] = $this->getRequest()->getPost("Q_I$i");
                        }
                        // print_r($data);
                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        if ($bool == 0) {
                            $instructor_feed->update($data, array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'instructor=?' => $data['instructor'], 'question_no_c=?' => $data['question_no_c'], 'term=?' => $data['term'], 'batch=?' => $data['batch'], 'course=?' => $data['course']));
                        } else {


                            if ($k == 0) {
                                $instructor_feed->delete(array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'instructor=?' => $data['instructor'], 'term=?' => $data['term'], 'batch=?' => $data['batch']));
                                $k = $k + 1;
                            }

                            $instructor_feed->insert($data);
                        }
                    }
                }


                if ($bool1 == 0) {
                    for ($i = 1; $i <= count($len1); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_I$i"));
                        if (is_array($arr) && strlen($arr[1]) == 1) {
                            $data['feed'] = $arr[1];
                            $data['question_no_c'] = $arr[0];
                        } else {

                            if (empty($arr[0]))
                                $arr[0] = null;

                            $data['feed'] = $arr[0];
                            $data['question_no_c'] = $this->getRequest()->getPost("Q_I$i");
                        }
                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        $instructor_feed->insert($data);
                    }
                }
            }
        }
    }

    public function ajaxCheckFacultyAction() {
        $instructor_feed = new Application_Model_InstructorFeed();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam('term_id');
            $batch_id = $this->_getParam('academic_year_id');
            $course_id = $this->_getParam('course_id');
            $instructor = $this->_getParam('faculty_id');
            $result = $instructor_feed->hasRatedFaculty($_SESSION['admin_login']['admin_login']->student_id, $course_id, $term_id, $batch_id, $instructor);
            echo $result;
            exit;
        }
    }

    public function feedBackAction() {
        $this->view->action_name = 'FeedBack';
        $this->view->sub_title_name = 'Student FeedBack';
        $student_feed_form = new Application_Form_StudentFeedElement();
        $student_feed_model = new Application_Model_StudentFeed();
        $instructor_feed = new Application_Model_InstructorFeed();
        $course_details = new Application_Model_Attendance();
        $this->view->form = $student_feed_form;
        $term_and_batch = $this->getRecentTerm($_SESSION['admin_login']['admin_login']->participant_academic);
        
        if(!$term_and_batch['term_id']){
        $this->view->custom_message  = 'Your are not Present in this academic year'; 
        }
        else if(!$term_and_batch['batch_id']){
            $this->view->custom_message  = 'Your are not Present in this academic year'; 
        }
        else
        {
        
        $Allcoursese = $course_details->getCourseDetails($term_and_batch['term_id'], $term_and_batch['batch_id']);
        $this->view->AllCourses = $Allcoursese;
        $this->view->FeedCourses = $student_feed_model->courseFeed($term_and_batch['term_id'], $term_and_batch['batch_id'], $_SESSION['admin_login']['admin_login']->student_id);
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getPost()) {
                $data['course'] = $this->getRequest()->getPost('course');
                $data['instructor'] = $this->getRequest()->getPost('instructor');
                $data['batch'] = $this->getRequest()->getPost('batch');
                $data['term'] = $this->getRequest()->getPost('term');
                //  print_r($data);exit;
                $len = $this->getRequest()->getPost('count');
                $len1 = $this->getRequest()->getPost('count1');
                $data['instructor'] = '';
                $bool = $student_feed_model->hasRated($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['term'], $data['batch']);

                $bool1 = $instructor_feed->hasRated($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['instructor'], $data['term'], $data['batch']);

                if ($bool == 0) {
                    for ($i = 1; $i <= count($len); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_$i"));

                        if (is_array($arr) && strlen($arr[1]) == 1) {
                            $data['feed'] = $arr[1];
                            $data['question_no_c'] = $arr[0];
                        } else {
                            if (empty($arr[0]))
                                $arr[0] = null;

                            $data['feed'] = $arr[0];

                            $data['question_no_c'] = $this->getRequest()->getPost("Q_$i");
                        }



                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        $student_feed_model->insert($data);
                    }
                }

                if ($bool > 0) {
                    $k = 0;
                    $bool = $student_feed_model->hasRatedQuestion($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['instructor'], $data['term'], $data['batch']);
                    for ($i = 1; $i <= count($len); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_$i"));

                        if (is_array($arr) && strlen($arr[1]) == 1) {
                            $data['feed'] = $arr[1];
                            $data['question_no_c'] = $arr[0];
                        } else {
                            if (empty($arr[0]))
                                $arr[0] = null;

                            $data['feed'] = $arr[0];
                            $data['question_no_c'] = $this->getRequest()->getPost("Q_$i");
                        }


                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;

                        if ($bool == 0) {
                            $student_feed_model->update($data, array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'course=?' => $data['course'], 'question_no_c=?' => $data['question_no_c'], 'term=?' => $data['term'], 'batch=?' => $data['batch']));
                        } else {

                            if ($k == 0) {
                                $student_feed_model->delete(array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'course=?' => $data['course'], 'term=?' => $data['term'], 'batch=?' => $data['batch']));
                                $k = $k + 1;
                            }

                            $student_feed_model->insert($data);
                        }
                    }
                }


                if ($bool1 > 0) {

                    $k = 0;
                    $bool = $instructor_feed->hasRatedQuestion($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['term'], $data['batch']);

                    for ($i = 1; $i <= count($len1); $i++) {

                        $arr = explode("-", $this->getRequest()->getPost("rate_I$i"));

                        if (is_array($arr) && strlen($arr[1]) == 1) {
                            $data['feed'] = $arr[1];
                            $data['question_no_c'] = $arr[0];
                        } else {
                            if (empty($arr[0]))
                                $arr[0] = null;
                            $data['feed'] = $arr[0];
                            $data['question_no_c'] = $this->getRequest()->getPost("Q_I$i");
                        }
                        // print_r($data);
                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        if ($bool == 0) {
                            $instructor_feed->update($data, array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'instructor=?' => $data['instructor'], 'question_no_c=?' => $data['question_no_c'], 'term=?' => $data['term'], 'batch=?' => $data['batch'], 'course=?' => $data['course']));
                        } else {


                            if ($k == 0) {
                                $instructor_feed->delete(array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'instructor=?' => $data['instructor'], 'term=?' => $data['term'], 'batch=?' => $data['batch']));
                                $k = $k + 1;
                            }

                            $instructor_feed->insert($data);
                        }
                    }
                }


                if ($bool1 == 0) {
                    for ($i = 1; $i <= count($len1); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_I$i"));
                        if (is_array($arr) && strlen($arr[1]) == 1) {
                            $data['feed'] = $arr[1];
                            $data['question_no_c'] = $arr[0];
                        } else {

                            if (empty($arr[0]))
                                $arr[0] = null;

                            $data['feed'] = $arr[0];
                            $data['question_no_c'] = $this->getRequest()->getPost("Q_I$i");
                        }
                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        $instructor_feed->insert($data);
                    }
                }
            }
        }
        }
    }

    public function ajaxGetCourseAction() {
        $course_details = new Application_Model_Attendance();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('academic_year_id');
            $result = $course_details->getCourseDetails($term_id, $batch_id);
            //  print_r($result);exit;
            echo '<option value="">Select</option>';
            foreach ($result as $value) {
                echo '<option value="' . $value['course_id'] . '" >' . $value['course_code'] . '</option>';
            }
        }die;
    }

    public function ajaxGetFacultyAction() {
        $employee_model = new Application_Model_HRMModel();
        $faculty = new Application_Model_Attendance();
         $class_master = new Application_Model_ClassMaster();
        $term_id = $this->_getParam('term_id');
        $batch_id = $this->_getParam('academic_year_id');
        $course_id = $this->_getParam('course_id');
        /* $result = $faculty->getFaculty($term_id, $batch_id, $course_id); */
        $no_of_classes  = $class_master->getRecordByTermIdAndBatch(0, 0);
        
        $result = $faculty->getFeedFaculty($term_id, $batch_id, $course_id,$no_of_classes);
        $index = 0;
        $facultyFeed = array();
        foreach ($result as $key => $value) {
            foreach ($value as $new_key => $new_value) {
                if ($new_value == $course_id) {
                    $fac = explode('_', $new_key);
                    $facultyFeed[$index] = $value['faculty_' . $fac[1]];
                    $index++;
                }
            }
        }




        $uniqfaculty = array_unique($facultyFeed);
        //======[GETTING NAME OF AL THE EMPLYOEE]=======//
        for ($i = 0; $i < count($uniqfaculty); $i++) {
            $empl_name[$i] = $employee_model->getAllEmployee($uniqfaculty[$i])[0];
        }
        //=========[SETTING SELECT BOX]=========//
        echo '<option value="">Select</option>';
        if (count($empl_name) > 0) {
            foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
            }die;
        }

        die;
    }

    public function ajaxGetRatingViewAction() {
        $course = $this->_getParam('course');
        $instructor = $this->_getParam('instructor');
        $question_model = new Application_Model_Questionnaire();
        $rating_model = new Application_Model_RatingMaster();
        $student_feed_model = new Application_Model_StudentFeed();
        $instructor_feed = new Application_Model_InstructorFeed();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam('term_id');
            $batch_id = $this->_getParam('academic_year_id');
            $course_id = $this->_getParam('course_id');
            $instructor = $this->_getParam('instructor_id');
            if (!empty($course_id)) {
                $result['course'] = $question_model->getAllQuestionByQuestionType1(1, $term_id, $batch_id);
            }
            if (!empty($instructor)) {
                $result['instructor'] = $question_model->getAllQuestionByQuestionType1(2, $term_id, $batch_id);
            }
            $i = 0;
            foreach ($result['course'] as $key => $value) {
                $arr = explode(',', $value['selected_question']);
                foreach ($arr as $key1 => $value1) {
                    $result['course'][$key]['questions'][$i++] = $question_model->getAllQuestionByQuestionType2(1, $value1);
                }
            }
            $i = 0;
            foreach ($result['instructor'] as $key => $value) {
                $arr = explode(',', $value['selected_question']);
                foreach ($arr as $key1 => $value1) {
                    $result['instructor'][$key]['questions'][$i++] = $question_model->getAllQuestionByQuestionType2(2, $value1);
                }
            }

            $result['course_value'] = $student_feed_model->ratedValue($_SESSION['admin_login']['admin_login']->student_id, $course_id, $term_id, $batch_id);
            $result['rating'] = $rating_model->getRecords1();
            //   echo "<pre>"; print_r($result);exit;
            $this->view->result = $result;
        }
    }

    public function ajaxGetRatingInstructorViewAction() {
        $course = $this->_getParam('course');
        $instructor = $this->_getParam('instructor');
        $question_model = new Application_Model_Questionnaire();
        $rating_model = new Application_Model_RatingMaster();
        $student_feed_model = new Application_Model_StudentFeed();
        $instructor_feed = new Application_Model_InstructorFeed();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam('term_id');
            $batch_id = $this->_getParam('academic_year_id');
            $course_id = $this->_getParam('course_id');
            $instructor = $this->_getParam('instructor_id');
            if (!empty($course_id)) {
                $result['course'] = $question_model->getAllQuestionByQuestionType1(1, $term_id, $batch_id);
            }
            if (!empty($instructor)) {
                $result['instructor'] = $question_model->getAllQuestionByQuestionType1(2, $term_id, $batch_id);
            }
            $i = 0;
            foreach ($result['course'] as $key => $value) {
                $arr = explode(',', $value['selected_question']);
                foreach ($arr as $key1 => $value1) {
                    $result['course'][$key]['questions'][$i++] = $question_model->getAllQuestionByQuestionType2(1, $value1);
                }
            }
            $i = 0;
            foreach ($result['instructor'] as $key => $value) {
                $arr = explode(',', $value['selected_question']);
                foreach ($arr as $key1 => $value1) {
                    $result['instructor'][$key]['questions'][$i++] = $question_model->getAllQuestionByQuestionType2(2, $value1);
                }
            }
            $result['instructor_value'] = $instructor_feed->ratedValue($_SESSION['admin_login']['admin_login']->student_id, $instructor, $term_id, $batch_id, $course_id);

            //  echo "<pre>"; print_r($result);exit;
            $result['rating'] = $rating_model->getRecords1();

            $this->view->result = $result;
        }
    }

    public function gradeSheetAction() {
        $this->view->action_name = 'GradeSheet';
        $this->view->sub_title_name = 'Student GradeSheet';
    }

    public function ajaxGradeSheetAction() {
        $this->view->close = '';
        $check = '';
        $stu_id = $this->_getParam("id");

        $academic_id = $this->_getParam("acd_id");

        $year_id = $this->_getParam("year");
        $term_id = $this->_getParam("term");
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        $messages = $this->_flashMessenger->getMessages();
        $this->view->messages = $messages;

        /**
         * If this student was discontinued in past, we have to fetch student's grades from more than one batches. Else, from only one batch
         *  
         */
        //If this student was discontinued in past
        $student_model = new Application_Model_StudentPortal();
        $stu_pre_details = $student_model->fetchDiscontinuedBatchesOfStudent($stu_id);

        if (is_array($stu_pre_details) && !empty($stu_pre_details)) {
            //Find out all Batch Id and student id in each batch
            $batch_arr = array();
            $student_ids = array();
            $student_first_batch = array(); //It will store student detail of First Batch
            $stu_pre_details1 = $student_model->fetchAllBatchesOfStudent($stu_id); //Fetching all batches detail of student
            foreach ($stu_pre_details1 as $row) {
                $batch_arr[] = $row['academic_id'];
                $student_ids[] = $row['student_id'];
                if (empty($student_first_batch)) {//Since, data is coming in ascending order of Student id, so the first row will be the detail of first batch of student
                    $student_first_batch = $row;
                }
            }
            $this->view->student_ids = $student_ids;
            $this->view->batch_arr = $batch_arr;
            ############################# [START] Fetching Experiential Courses and grades ####################################
            //Fetch all Experiential Learning Courses by Batch id and First/Second year
            $ExperientialLearning_model = new Application_Model_ExperientialLearning();
            $el_result1 = $ExperientialLearning_model->getExperRecordsByBatches($batch_arr, $year_id);
            /*
              $elc_ids = array();
              foreach($el_result1 as $row_el){
              $elc_ids[] = $row_el['elc_id'];
              }
             * 
             */

            //Fetch all grades of experiential learning courses
            $ExperientialGradeAllocation_model = new Application_Model_ExperientialGradeAllocation();
            $exp_course_grades_after_penalties = $ExperientialGradeAllocation_model->getExpGradesByBatches($batch_arr, $student_ids);
            $this->view->exp_course_grades_after_penalties = $exp_course_grades_after_penalties;
            //print_r($exp_course_grades_after_penalties);exit;
            //Filter only those Experiential Courses in which student appeared,
            $exp_course_result = array();
            foreach ($exp_course_grades_after_penalties as $row) {
                foreach ($el_result1 as $row_exp_course) {
                    if (($row_exp_course['elc_id'] == $row['course_id']) && ($row_exp_course['academic_year_id'] == $row['academic_id'])) {
                        $exp_course_result[] = $row_exp_course;
                    }
                }
            }
            //print_r($exp_course_result);exit;
            ############################# [END] Fetching Experiential Courses and grades ####################################        
            ############################# [START] Fetching Core Courses and grades ##########################################   
            //Select all Terms of First/Second year of Student. We will fetch all Terms of student from 'ourse_grade_after_penalties_items' table. If 'final_grade' column's value is ZERO, it meas student didn't appeared in the term and we will not fetch these rows.
            //Fetching list of all terms of all batches
            $TermMaster_model = new Application_Model_TermMaster();
            $term_result1 = $TermMaster_model->getTermsByBatchesYear($batch_arr, $year_id);
            $term_ids = array();
            foreach ($term_result1 as $row) {
                $term_ids[] = $row['term_id'];
            }

            $CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
            $course_grades_after_penalties = $CourseGradeAfterpenalties_model->getStudentGradesByBatches($batch_arr, $term_ids, $student_ids);
            $this->view->course_grades_after_penalties = $course_grades_after_penalties;
            //Filter only those Terms in which student appeared,
            $term_result = array();
            foreach ($course_grades_after_penalties as $row) {
                foreach ($term_result1 as $row_term) {
                    if ($row_term['term_id'] == $row['term_id']) {
                        $term_result[] = $row_term;
                    }
                }
            }
            //print_r($term_result);exit;
            ############################# [END] Fetching Core Courses and grades #################################### 

            $Academic_model = new Application_Model_Academic();
            $academic = $Academic_model->getRecord($student_first_batch['academic_id']);
            $this->view->academic_data = $academic;



            $this->view->stu_id = $student_first_batch['stu_id'];
            $this->view->academic_id = $student_first_batch['academic_id'];
            $this->view->term_result = $term_result;
            $this->view->stu_result = $student_first_batch;

            $this->view->expr_result = $exp_course_result;
            if ($check != 'admin' && empty($this->view->term_id)) {
                //Genering New Number/Counter of Gradesheet to be added on top right corner of gradesheet
                $student_model1 = new Application_Model_StudentPortal();
                $student_detail1 = $student_model1->getRecord($stu_id);
                
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber1($academic_id, $year_id, $student_detail1['stu_id']);
               
                if ($gradesheet_number == 0) {
                    echo "<script>alert('Your Grade Sheet is not Generated yet')</script>";

                    die;
                }
                $this->view->gradesheet_number = $gradesheet_number;
            }
            $htmlcontent = $this->view->render('report/secondyeargradesheetreport_discontinued_student.phtml');
            print_r($htmlcontent);
            exit;
            //Genering New Number/Counter of Gradesheet to be added on top right corner of gradesheet
            $student_model1 = new Application_Model_StudentPortal();
            $student_detail1 = $student_model1->getRecord($stu_id);
            $GradeSheet_model = new Application_Model_GradeSheet();
            $gradesheet_number = $GradeSheet_model->getGradeSheetNumber1($academic_id, $year_id, $student_detail1['stu_id']);
            $this->view->gradesheet_number = $gradesheet_number;
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $student_first_batch['stu_fname'] . '-' . $academic['short_code'] . '- Second Year Grade Report Details');
        } else {

            $this->view->stu_id = $stu_id;
            $this->view->academic_id = $academic_id;


            $TermMaster_model = new Application_Model_TermMaster();
            $term_result = $TermMaster_model->getTerms($academic_id, $year_id);

            $this->view->term_result = $term_result;

            $studentreport_model = new Application_Model_StudentReport();
            $stu_result = $studentreport_model->getRecord($stu_id);
            $this->view->stu_result = $stu_result;

            $Academic_model = new Application_Model_Academic();
            $academic = $Academic_model->getRecord($academic_id);

            $this->view->academic_data = $academic;
            if ($check != 'admin' && empty($this->view->term_id)) {

                //Genering New Number/Counter of Gradesheet to be added on top right corner of gradesheet
                $student_model1 = new Application_Model_StudentPortal();
                $student_detail1 = $student_model1->getRecord($stu_id);

                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber1($academic_id, $year_id, $student_detail1['stu_id']);


                if ($gradesheet_number == 0) {

                    echo "<script>alert('Your Grade Sheet is not Genarated yet')</script>";
                    die;
                }
                $this->view->gradesheet_number = $gradesheet_number;
            }
            $htmlcontent = $this->view->render('report/secondyeargradesheetreport.phtml');
            print_r($htmlcontent);
            exit;
        }
    }


    public function placementJobAnnouncementAction()
    {
            $this->view->action_name = 'MasterSelectionProcess';
        $this->view->sub_title_name = 'MasterSelectionProcess';
        $placement_models = new Application_Model_jobAnnouncement();
     

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $placement_models->getRecords_selection_process();
              
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
        
    }


 






    public function ajaxGetTermsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $TermMaster_model = new Application_Model_TermMaster();
            $term_result = $TermMaster_model->getTermDropDownList($this->login_storage->participant_academic);
            echo '<option value="">Select </option>';
            foreach ($term_result as $k => $val) {
                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }
        exit;
    }

    public function ajaxGetTermsDefaultAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->login_storage->participant_academic;
            $TermMaster_model = new Application_Model_TermMaster();
            $term_result = $TermMaster_model->getTermDropDownList($academic_year_id);
            echo '<option value="">--Select--</option>';
            foreach ($term_result as $k => $val) {
                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }
        exit;
    }

    public function ajaxGetRecentTermAndBatchAction() {

        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $term_name = '';
        $my_date = $this->_getParam('my_date');
        $date = date_create($my_date);
        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnAcademicId($this->login_storage->participant_academic);
        foreach ($result as $key) {
            $term_start = explode("/", $key['start_date']);
            $term_end = explode("/", $key['end_date']);
            $start = date_create($term_start[2] . "-" . $term_start[1] . "-" . $term_start[0]);
            $end = date_create($term_end[2] . "-" . $term_end[1] . "-" . $term_end[0]);
            if (strtotime(date_format($date, "Y-m-d")) >= strtotime(date_format($start, "Y-m-d")) && strtotime(date_format($date, "Y-m-d")) <= strtotime(date_format($end, "Y-m-d"))) {
                $term_id = $key['term_id'];
                $academic_year_id = $key['academic_year_id'];
                $start_date = date_format($start, "Y-m-d");
                $end_date = date_format($end, "Y-m-d");
                $term_name = $key['term_name'];
                $academic_name = $key['academic_name'];
                break;
            }
        }
        $result['recent']['batch_id'] = $academic_year_id;
        $result['recent']['term_id'] = $term_id;
        $result['recent']['term_name'] = $term_name;
        $result['recent']['batch_name'] = $academic_name;

        echo json_encode($result['recent']);
        exit;
    }

    //=========[GET RUNNING TERM]========//   

    public function getRecentTerm($batch_id) {

        $my_date = date('d-m-Y');
       //echo $my_date; exit;
        $date = date_create($my_date);
         
        $term = new Application_Model_TermMaster();

        $result = $term->getTermOnAcademicId($batch_id);
     //   echo "<prE>";print_r($result);exit;
        foreach ($result as $key) {
            $term_start = explode("/", $key['start_date']);
            $term_end = explode("/", $key['end_date']);
            $start = date_create($term_start[2] . "-" . $term_start[1] . "-" . $term_start[0]);
            $end = date_create($term_end[2] . "-" . $term_end[1] . "-" . $term_end[0]);
           
            if (strtotime(date_format($date, "Y-m-d")) >= strtotime(date_format($start, "Y-m-d")) && strtotime(date_format($date, "Y-m-d")) <= strtotime(date_format($end, "Y-m-d"))) { 
                $term_id = $key['term_id'];
                $academic_year_id = $key['academic_year_id'];
                $start_date = date_format($start, "Y-m-d");
                $end_date = date_format($end, "Y-m-d");
                $term_name = $key['term_name'];
                $academic_name = $key['academic_name'];
                break;
            }
        }
        if($academic_year_id){
        $result['recent']['batch_id'] = $academic_year_id;
        $result['recent']['term_id'] = $term_id;
        $result['recent']['term_name'] = $term_name;
        $result['recent']['batch_name'] = $academic_name;
        return $result['recent'];
        }
 else {
      echo "date does not lie in btween any of the existing terms !."; die;
     
 }
    }

    public function getRecentBatch() {

        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $my_date = date('d-m-Y');
        $date = $my_date;
        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnDate($my_date);
        // echo "<pre>"; print_r($result);exit;
        foreach ($result as $key) {
            $term_start = explode("/", $key['start_date']);
            $term_end = explode("/", $key['end_date']);
            $start = $term_start[2] . "-" . $term_start[1] . "-" . $term_start[0];
            $end = $term_end[2] . "-" . $term_end[1] . "-" . $term_end[0];

            //echo "<pre>"; print_r(strtotime(date('Y-m-d')).'====='.strtotime($start).'======='.strtotime($end)); exit;
            if (strtotime(date('Y-m-d')) >= strtotime($start) && strtotime(date('Y-m-d')) <= strtotime($end)) {
                $term_id = $key['term_id'];
                $academic_year_id = $key['academic_year_id'];
                $start_date = $start;
                $end_date = $end;
                $term_name = $key['term_name'];
                $academic_name = $key['academic_name'];
                break;
            }
        }

        $result['recent']['batch_id'] = $academic_year_id;
        $result['recent']['term_id'] = $term_id;

        // echo "<pre>";print_r(strtotime($start_date).'======'.strtotime($end_date).'======='.strtotime(date('Y-m-d')));exit;
        return $result;
    }

    public function changePasswordAction() {
        $this->view->sub_title_name = 'Change Password';
        $this->view->action_name = 'changePassword';
        $change_pword_form = new Application_Form_ChangePassword();
        $participant = new Application_Model_ParticipantsLogin();
        $this->view->form = $change_pword_form;

        if ($this->getRequest()->isPost()) {
            if ($change_pword_form->isValid($this->getRequest()->getPost())) {
                $data = $change_pword_form->getValues();
                $arr_data['participant_pword'] = $data['new_password'];
                $result = $participant->getInfo($this->login_storage->participant_email);
                if (md5($result['participant_pword']) != md5($data['current_password'])) {
                    $_SESSION['flash_message'] = 'Incorrect current password';
                } else {
                    $participant->update($arr_data, array('user_id=?' => $this->login_storage->user_id));
                    $_SESSION['flash_message'] = 'Password updated successfully.';
                    $this->_redirect('student-portal/change-password');
                }
            }
        }
    }

    public function ajaxGetFeeTransDetailsAction() {
        $studentFeeDetails = new Application_Model_FeeDetails();
        $stuTrans = new Application_Model_StuTrans();
        $total_fee_paid = 0; //=====init[BY ASHUTOSH]
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            $term_id = $this->_getParam("term_id");
            $pdm_id = $this->login_storage->roll_no;
            $user_id = $studentFeeDetails->getUserId($batch_id, $term_id, $pdm_id);
            $feePaidDetails = $stuTrans->getStuTrans($user_id['fee_details_id']);


            //=========[IF USER ID IS NOT AVAILABLE]==========[BY ASHUTOSH]//
            if (!is_array($feePaidDetails)) {
                $feeTrans['message'] = "There is no student in this Term or in this Academic!";
                echo json_encode($feeTrans);
                exit;
            }
            //===========[IF USER ID IS AAILABLE BUT NO TRANSATION HAS BEEN MADE FROM 'fa_STU_TRANS' in erp DATABASE]=====[BY ASHUTOSH]//
            if (is_array($feePaidDetails) && count($feePaidDetails) == 0) {
                $feePaidDetails = $studentFeeDetails->getStuTransReal($user_id['fee_details_id'])[0]['total_fee'];
                $feeTrans['total'] = 0;
                $feeTrans['due_fee'] = $feePaidDetails;
                //$feeTrans['message'] = "No transaction has been made from this $pdm_id";
                echo json_encode($feeTrans);
                exit;
            }
            foreach ($feePaidDetails as $key => $value) {
                $total_fee_paid += $value['ov_amount']; //calculate total image
                $feeTrans['trans_details'][$key]['trans_amount'] = $value['ov_amount'];
                $feeTrans['trans_details'][$key]['trans_date'] = $value['trans_date'];
                $feeTrans['trans_details'][$key]['due_date'] = $value['due_date'];
            }

            $feeTrans['total'] = $total_fee_paid;
            $feeTrans['due_fee'] = $user_id['total_fee'] - $total_fee_paid;
            $feeTrans['fee_to_pay'] = $user_id['total_fee'];
            $feeTrans['fee_discount'] = $user_id['fee_discount'] . "%";
            $feeTrans['fee'] = $user_id['fee'];
            $feeTrans['tuition_fee'] = $user_id['service_fee'];
            $feeTrans['other_annual_charges'] = $user_id['other_annual_charges'];
            echo json_encode($feeTrans);
            exit;
        }
    }

    public function getTrans($batch_id, $term_id) {
        $studentFeeDetails = new Application_Model_FeeDetails();
        $stuTrans = new Application_Model_StuTrans();
        $total_fee_paid = 0; //=====init[BY ASHUTOSH]

        $pdm_id = $this->login_storage->roll_no;
        $user_id = $studentFeeDetails->getUserId($batch_id, $term_id, $pdm_id);
        $feePaidDetails = $stuTrans->getStuTrans($user_id['fee_details_id']);

        //=========[IF USER ID IS NOT AVAILABLE]==========[BY ASHUTOSH]//
        if (!is_array($feePaidDetails)) {
            $feeTrans['message'] = "There is no student in this Term or in this Academic!";
            return $feeTrans;
        }
        //===========[IF USER ID IS AAILABLE BUT NO TRANSATION HAS BEEN MADE FROM 'fa_STU_TRANS' in erp DATABASE]=====[BY ASHUTOSH]//
        if (is_array($feePaidDetails) && count($feePaidDetails) == 0) {
            $feeTrans['message'] = "No transaction has been made from this $pdm_id";
            return $feeTrans;
        }
        foreach ($feePaidDetails as $key => $value) {
            $total_fee_paid += $value['ov_amount']; //calculate total image
            $feeTrans['trans_details'][$key]['trans_amount'] = $value['ov_amount'];
            $feeTrans['trans_details'][$key]['trans_date'] = $value['trans_date'];
        }
        $feeTrans['due_date'] = $feePaidDetails[0]['due_date'];
        $feeTrans['total'] = $total_fee_paid;
        $feeTrans['due_fee'] = $user_id['total_fee'] - $total_fee_paid;
        $feeTrans['fee_to_pay'] = $user_id['total_fee'];
        $feeTrans['fee_discount'] = $user_id['fee_discount'] . "%";
        $feeTrans['fee'] = $user_id['fee'];
        $feeTrans['tuition_fee'] = $user_id['tuition_fee'];
        $feeTrans['service_fee'] = $user_id['service_fee'];
        $feeTrans['other_annual_charges'] = $user_id['other_annual_charges'];

        return $feeTrans;
    }

    public function getFullTrans($batch_id, $term_id,$term_name='') {
        
        $studentFeeDetails = new Application_Model_FeeDetails();
        $term_details = new Application_Model_TermMaster();
        $stuTrans = new Application_Model_StuTrans();
        $total_fee_paid = 0; //=====init[BY ASHUTOSH]
        $feeTrans = array();
        $pdm_id = $this->login_storage->roll_no;
        $user_id = $studentFeeDetails->getUserIdByBatch($batch_id, $pdm_id,$term_id);
        foreach ($user_id as $key1 => $value1) {
            $feePaidDetails = $stuTrans->getStuTrans($value1['fee_details_id']);

            //=========[IF USER ID IS NOT AVAILABLE]==========[BY ASHUTOSH]//
            if (!is_array($feePaidDetails)) {
                $feeTrans[$key1]['message'] = "There is no student in this Term or in this Academic!";
                return $feeTrans;
            }
            //===========[IF USER ID IS AAILABLE BUT NO TRANSATION HAS BEEN MADE FROM 'fa_STU_TRANS' in erp DATABASE]=====[BY ASHUTOSH]//
            if (is_array($feePaidDetails) && count($feePaidDetails) == 0) {
                $feeTrans[$key1]['message'] = "No transaction has been made from this $pdm_id for $term_name";
                return $feeTrans;
            }
            foreach ($feePaidDetails as $key => $value) {
                $total_fee_paid += $value['ov_amount']; //calculate total image
                $feeTrans[$key1]['trans_details']['trans_amount'] += $value['ov_amount'];
                $feeTrans[$key1]['trans_details']['trans_date'] = $value['trans_date'];
            }
            $feeTrans[$key1]['due_date'] = $feePaidDetails[0]['due_date'];
            $feeTrans[$key1]['total'] = $total_fee_paid;
            $feeTrans[$key1]['term_id'] = $value1['term_id'];
            $feeTrans[$key1]['term_name'] = $term_details->getValidateTermNameById($value1['term_id'])['term_name'];
            $feeTrans[$key1]['due_fee'] = $value1['total_fee'] - $total_fee_paid;
            $feeTrans[$key1]['fee_to_pay'] = $value1['total_fee'];
            $feeTrans[$key1]['fee_discount'] = $value1['fee_discount'] . "%";
            $feeTrans[$key1]['fee'] = $value1['fee'];
            $feeTrans[$key1]['tuition_fee'] = $value1['tuition_fee'];
            $feeTrans[$key1]['service_fee'] = $value1['service_fee'];
            $feeTrans[$key1]['other_annual_charges'] = $value1['other_annual_charges'];
        }
        if(count($feeTrans)==0){
                $feeTrans[0]['message'] = "No transaction has been made from this $pdm_id for $term_name";
                return $feeTrans;}
        //echo "<pre>";print_r($feeTrans);exit;
        return $feeTrans;
    }

    public function ajaxGetAttendanceDatesAction() {

        $atendence_saver_model = new Application_Model_Attendance();
        
        $class_master = new Application_Model_ClassMaster();
        
        //echo "<pre>"; print_r($_SESSION);exit;
        
        $batch_id = $this->_getParam("batch_id");

        $term_id = $this->_getParam("term_id");
        
        $section = $this->_getParam("section");

$no_of_classes  = $class_master->getRecordByTermIdAndBatch(0, 0);
        $student_id = $this->login_storage->student_id;
        $result['present'] = $atendence_saver_model->getPresent($batch_id, $term_id, $student_id,$no_of_classes,$section);
        $result['Absent'] = $atendence_saver_model->getAbsent($batch_id, $term_id, $student_id,$no_of_classes,$section);
        echo json_encode($result);
        die;
    }

    public function printTransctionReportAction() {
        $batch_id = $this->_getParam("batch_id");
        $term_id = $this->_getParam("term_id");
        $term_name = $this->_getParam('term_name');
        $result = $this->getTrans($batch_id, $term_id);
        $result['due_fee_in_words'] = $this->number_to_word($result['due_fee']);
        $result['total_in_words'] = $this->number_to_word($result['total']);
        //========Term name to view for pdf ====//
        $result['term_name'] = $term_name;
        $this->view->data = $result;

        $htmlcontent = $this->view->render('report/transction_report.phtml');
       // echo $htmlcontent; exit;
        $pdfheader = '';
        $pdffooter = '';
        $this->_act->generatePdf1($pdfheader, $pdffooter, $htmlcontent, "Transaction-on-" . date('d-m-Y'));
    }

    public function number_to_word($num = '') {
        $num = (string) ( (int) $num );

        if ((int) ( $num ) && ctype_digit($num)) {
            $words = array();

            $num = str_replace(array(',', ' '), '', trim($num));

            $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven',
                'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen',
                'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');

            $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty',
                'seventy', 'eighty', 'ninety', 'hundred');

            $list3 = array('', 'thousand', 'million', 'billion', 'trillion',
                'quadrillion', 'quintillion', 'sextillion', 'septillion',
                'octillion', 'nonillion', 'decillion', 'undecillion',
                'duodecillion', 'tredecillion', 'quattuordecillion',
                'quindecillion', 'sexdecillion', 'septendecillion',
                'octodecillion', 'novemdecillion', 'vigintillion');

            $num_length = strlen($num);
            $levels = (int) ( ( $num_length + 2 ) / 3 );
            $max_length = $levels * 3;
            $num = substr('00' . $num, -$max_length);
            $num_levels = str_split($num, 3);

            foreach ($num_levels as $num_part) {
                $levels--;
                $hundreds = (int) ( $num_part / 100 );
                $hundreds = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
                $tens = (int) ( $num_part % 100 );
                $singles = '';

                if ($tens < 20) {
                    $tens = ( $tens ? ' ' . $list1[$tens] . ' ' : '' );
                } else {
                    $tens = (int) ( $tens / 10 );
                    $tens = ' ' . $list2[$tens] . ' ';
                    $singles = (int) ( $num_part % 10 );
                    $singles = ' ' . $list1[$singles] . ' ';
                }
                $words[] = $hundreds . $tens . $singles . ( ( $levels && (int) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' );
            }

            $commas = count($words);

            if ($commas > 1) {
                $commas = $commas - 1;
            }

            $words = implode(', ', $words);
            $words = trim(str_replace(' ,', ',', trim(ucwords($words))), ', ');

            /*    if ($commas) {

              $words = str_replace(',', ' and', $words);
              } */

            return $words;
        } else if (!( (int) $num )) {
            return 'Zero';
        }
        return '';
    }

    public function myDataAction() {
        $dmi_holiday = new Application_Model_DmiHoliday();
        $dates = array();
        $student_holidays = $dmi_holiday->getHolidayListForStudents();
        $year = $this->_getParam('year');
        $month = $this->_getParam('month');

        //$details = $this->getDate($month, $year);

        foreach ($student_holidays as $key => $value) {
            $now = new DateTime($value['start']);
            $ref = new DateTime($value['end']);
            $diff = $now->diff($ref);
            if ($value['category'] == 39) {
                for ($i = 0; $i < $diff; $i++) {
                    array_push($dates, array('date' => date('Y-m-d', strtotime("+$i day", strtotime($value['start']))),
                        'badge' => true,
                        'title' => $value['title']));
                }
            } else {
                for ($i = 0; $i < $diff; $i++) {
                    array_push($dates, array('date' => date('Y-m-d', strtotime("+$i day", strtotime($value['start']))),
                        'badge' => true,
                        'title' => $value['title']));
                }
            }
        }

        for ($i = 0; $i < count($dates); $i++) {

            $dates[$i] = array(
                'date' => $dates[$i]['date'],
                'badge' => ($i & 1) ? true : false,
                'title' => 'Example for ' . $dates[$i]['date'],
                'body' => '<p class="lead">Information for this date</p><p>You can add <strong>html</strong> in this block</p>',
                'footer' => 'Extra information',
            );


            $dates[$i]['title'] = $student_holidays[$i]['title'];
            $dates[$i]['body'] = '<p>' . $student_holidays[$i]['description'] . '</p>';
            $dates[$i]['footer'] = '
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           
            ';
        }
        echo json_encode($dates);
        exit;
    }

    public function getDate($month, $year) {
        $atendence_saver_model = new Application_Model_Attendance();
        $employee_model = new Application_Model_HRMModel();
        $course_report = new Application_Model_CourseReport();
        $result = false;
        $resultDate = array();

        $batch_id = $this->login_storage->participant_academic;

        $running_terms = $this->getRecentTerm($batch_id);
        $term_id = 20;

        $single_result = array();

        if (!empty($term_id)) {
            $course_result = $atendence_saver_model->getCourseDetails($term_id, $batch_id);

            $course_count = $course_report->getTotalNumberOfDays($term_id, $batch_id);
            foreach ($course_result as $key => $value) {
                $single_result[$key]['course_code'] = $value['course_code'];
                $single_result[$key]['course_name'] = $value['course_name'];
                $single_result[$key]['course_id'] = $value['course_id'];
                foreach ($course_count as $key1 => $value1) {
                    if ($value['course_code'] == $value1['course_code']) {


                        $result_class1[$key]['P'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_1', $month, $year);
                        $result_class2[$key]['P'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_2', $month, $year);
                        $result_class3[$key]['P'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_3', $month, $year);
                        $result_class4[$key]['P'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_4', $month, $year);
                        $result_class5[$key]['P'] = $atendence_saver_model->getRecordByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_5', $month, $year);




                        $result_class1[$key]['p_data'] = $atendence_saver_model->getDatesByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_1', $month, $year);
                        $result_class2[$key]['p_data'] = $atendence_saver_model->getDatesByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_2', $month, $year);
                        $result_class3[$key]['p_data'] = $atendence_saver_model->getDatesByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_3', $month, $year);
                        $result_class4[$key]['p_data'] = $atendence_saver_model->getDatesByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_4', $month, $year);
                        $result_class5[$key]['p_data'] = $atendence_saver_model->getDatesByStudentIdByMonth($_SESSION['admin_login']['admin_login']->student_id, $value['course_id'], $term_id, $batch_id, 'class_5', $month, $year);


                        $result[$key]['p1'] = $result_class1[$key]['p_data'];
                        $result[$key]['p2'] = $result_class2[$key]['p_data'];
                        $result[$key]['p3'] = $result_class3[$key]['p_data'];
                        $result[$key]['p4'] = $result_class4[$key]['p_data'];
                        $result[$key]['p5'] = $result_class5[$key]['p_data'];
                    }
                }
            }
        }

        echo"<pre>";
        print_r($resultDate);
        exit;
    }

    public function ajaxGetStudentClassAction() {
        //======[Default Variable]=========///
        $empl_name_v = $empl_name_f = '';

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            $term_id = $this->_getParam("term_id");
            $date = $this->_getParam('date');
            $section = $this->_getParam('section');
            $studentCourses = new Application_Model_BatchSchedule();
            $classMaster = new Application_Model_ClassMaster();
            $no_of_classes = $classMaster->getRecordByTermIdAndBatch(0, 0);
            $employee = new Application_Model_HRMModel();
            $max = $studentCourses->getMaxVersionOnDate($date, $batch_id,$section);
            $studentCourseId = $studentCourses->getCourseId($batch_id, $term_id, $date, $max, $no_of_classes,$section);
            $studentCourseName = $studentCourses->getCourseName($studentCourseId);
            $facultys = $studentCourses->getFaculty($studentCourseId, $term_id);

            foreach ($facultys as $key => $value) {

                $empl_name_v = $empl_name_f = '';
                $employee_name = $employee->getAllEmployee($value['employee_id'])[0]['name'];
                $facultys[$key]['employee_id'] = $employee_name;
                
                $faculty_id = explode(',', $value['faculty_id']);
                $visiting_faculty = explode(',', $value['visiting_faculty_id']);
                //======[MERGING BOTH FACULTY ARRAY]=======//
                $faculty_arr = $faculty_id;
                $vf_arr = $visiting_faculty;

                //====={MAKING ARRAY UNIQUE}==============//
                //$all_unique_faculty = array_unique($faculty_arr);
                //======[GETTING NAME OF AL THE EMPLYOEE]=======//
                
                
                for ($i = 0; $i < count($faculty_arr); $i++) {
                    if ($faculty_arr[$i] != 'NA' && !empty($faculty_arr[$i]))
                        $empl_name_f .= $employee->getAllEmployee($faculty_arr[$i])[0]['name'] . ',';
                }

                for ($i = 0; $i < count($vf_arr); $i++) {
                    if ($vf_arr[$i] != 'NA'  && !empty($faculty_arr[$i]))
                        $empl_name_v .= $employee->getAllEmployee($vf_arr[$i])[0]['name'] . ',';
                }
               foreach($studentCourseName as $key2 => $value_course){
                   if($value['course_id'] == $value_course['course_id']){
                        $studentCourseName[$key2]['cordinator'] = $employee_name;
                        $studentCourseName[$key2]['dmi_faculty'] = $empl_name_f;
                        $studentCourseName[$key2]['visiting_faculty'] = $empl_name_v;
                   }
               }
                //=========[SETTING SELECT BOX]=========//  
            }
            
            $studentCourse['date'] = date('l d M Y', strtotime($date));

            if (is_array($studentCourseName)) {
                for ($i = 0; $i < count($studentCourseId); $i++) {
                    $class = "class_" . ($i + 1);

                    for ($j = 0; $j < count($studentCourseName); $j++) {

                        if ($studentCourseId[$class] == $studentCourseName[$j]['course_id']) {
                            $faculty_id = $this->check1($date, $studentCourseName[$j]['course_id'], $batch_id, $i + 1, $term_id)[0]['faculty_id'];
                            if ($faculty_id)
                                $studentCourse[$i]['faculty'] = $employee->getAllEmployee1($faculty_id)[0]['name'];
                            else
                                $studentCourse[$i]['faculty'] = 'Not Allotted';
                            $studentCourse[$i]['course_name'] = $studentCourseName[$j]['course_name'];
                            $studentCourse[$i]['course_code'] = $studentCourseName[$j]['course_code'];
                            $studentCourse[$i]['course_id'] = $studentCourseName[$j]['course_id'];
                            $studentCourse[$i]['cordinator'] = $studentCourseName[$j]['cordinator'];
                            $studentCourse[$i]['dmi_faculty'] = $studentCourseName[$j]['dmi_faculty'];
                            $studentCourse[$i]['visiting_faculty'] = $studentCourseName[$j]['visiting_faculty'];
                        }
                    }
                }
                echo json_encode($studentCourse);
                exit;
            }
            echo (0);
            exit;
        }
    }

    public function check1($date, $course_id, $batch, $class, $term) {
        $class_alotment = new Application_Model_Classalotment();



        $result = $class_alotment->check($course_id, $date, $class, $batch, $term);
        return $result;
    }

    public function getCourseNames1(array $courses, $start_date, $end_date, $batch_id, $term_id,$section, $version, $limit = '3') {

        $course_name = new Application_Model_TermMaster();
        $result = array();
        $i = 0;
        foreach ($courses as $key) {
            $result[$key['course_id']] = $course_name->getCourseName($key['course_id']);
        }

        foreach ($result as $key => $value) {
            $result['course_count'][$key] = (int) $course_name->getCourseReport($value['course_code'], $batch_id, $term_id, $section,$version);
        }

        return $result;
    }

    public function ajaxGetBatchSceduleSessions1Action() {
        $term_id = '';
        $term_id = $this->_getParam('term_id');
        $academic_year_id = $this->_getParam('batch_id');
        $limit = (int) $this->_getParam('top_id');
        $section = $this->_getParam('section');
        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $course_details = new Application_Model_Attendance();
        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnDat1($term_id, $academic_year_id);


        $term_start = explode("/", $result[0]['start_date']);
        $term_end = explode("/", $result[0]['end_date']);
        $start = date_create($term_start[2] . "-" . $term_start[1] . "-" . $term_start[0]);
        $end = date_create($term_end[2] . "-" . $term_end[1] . "-" . $term_end[0]);

        //getting all the courses 

        $courses = $course_details->getCourseDetails($term_id, $academic_year_id);
        //print_r($courses);exit;
        $version_id = $term->getMaxVersion(date_format($start, "d-m-Y"));


        if (count(Courses) > 0) {
            $result['course_details'] = $this->getCourseNames1($courses, date_format($start, "Y-m-d"), date_format($end, "Y-m-d"), $academic_year_id, $term_id, $section, $version_id['version'], $limit);
            // print_r($result['course_count']);exit;
        } else {
            echo 'No Courses';
            exit;
        }
        foreach ($result['course_details'] as $key => $value) {
            $result['course_details'][$key]['course_count'] = $result['course_details']['course_count'][$key];
        }
        echo json_encode($result['course_details']);
        exit;
    }

    public function ajaxGetBirthDayAction() {


        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            $term_id = $this->_getParam("term_id");
            $month = $this->_getParam('date');

            /*             * **$studentCourseName['date'] = date('l d M Y', strtotime($date));
              $bDates = $this->getBirthDay($batch_id, $term_id, $date);
              $studentCourseName['dob'] = $bDates;*** */

            $studentInfo = new Application_Model_BatchSchedule();
            $studentDateB = $studentInfo->getDoB($batch_id, $term_id, $month);
            $studentDB['dob'] = array();
            $arr = array();
            for ($i = 0; $i < count($studentDateB); $i++) {
                if (count($arr) > 0) {
                    if ($arr[$i] != $studentDateB[$i]['student_id']) {
                        $arr[$i] = $studentDateB[$i]['student_id'];
                    }
                } else {
                    $arr[$i] = $studentDateB[$i]['student_id'];
                }
            }
            $arr = array_unique($arr);

            for ($i = 0; $i < count($arr); $i++) {
                $studentDB['dob'][$i] = $studentDateB[$i];
            }
            echo json_encode($studentDB['dob']);
            die;
        }
        echo 0;
        exit;
    }

    public function programdesignviewAction() {
        //echo "hello";exit;
        $this->view->action_name = 'ProgramDesign';
        $this->view->sub_title_name = 'ProgramDesignView';
        $pd_view_form = new Application_Form_ProgramDesignViewStudent();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $pd_view_form;
    }

    // PROGRAM DESIGN VIEW AJAX
    public function getProgramViewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {


            $recent_batch = $this->getRecentBatch();
            if (!$recent_batch['recent']['batch_id']) {
                $recent_batch['recent']['batch_id'] = 0;
                $recent_batch['recent']['term_id'] = 0;
            }

            $pd_id = $this->_getParam("pm_name");
            $short_id = $recent_batch['recent']['batch_id'];

            if ($pd_id) {

                $programdesign_model = new Application_Model_ProgramDesign();
                $result = $programdesign_model->getProgram($short_id, $pd_id);
                //print_r($result);die;
                $this->view->academicresult = $result;
            }
        }
    }

    public function electiveSelectionAction() {
        $this->view->sub_title_name = 'Elective Courses';
        $this->view->action_name = 'electivecourses';
        $Elective_form = new Application_Form_ElectiveSelection();
        $Electivecourse_model = new Application_Model_ElectiveCourseLearning();
        $this->view->form = $Elective_form;
    }

    public function ajaxGetElectiveCoursesViewAction() {
        $this->_helper->layout->disableLayout();
        $elective_courses = new Application_Model_ElectiveSelection();
        $course_details = new Application_Model_Attendance();
         
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            
            $academic_year_id = $_SESSION['admin_login']['admin_login']->participant_academic;
            $result = $elective_courses->getElectivesByTerm($academic_year_id, $term_id);
            foreach($result as $key => $value){
            $result[$key]['electives'] = $course_details->getCourseDetailsByCourseId($value['electives']);
            }
           //echo "<pre>"; print_r($result);die;
                $this->view->item_result = $result;
        }
    }

    
     public function ajaxGetAcademicTermsAction()
	 {
		 $this->_helper->layout->disableLayout();
		  if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$TermMaster_model = new Application_Model_TermMaster();
			$data = $TermMaster_model->getAcademicTerms($academic_year_id);
			 echo '<option value="">Select</option>';
				 foreach($data as $k => $val){
					 echo '<option value="'.$k.'">'.$val.'</option>';
					 
				 }
		}die;
		 
		 
	 }
    //=====for program calendar
    public function ajaxGetAcademicAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $short_id = $this->_getParam("short_id");
            //print_r($short_code); die;
            $Academic_model = new Application_Model_Academic();
            $result = $Academic_model->getAcademic($short_id);

            /* //echo '<option value="">Select</option>';
              foreach($result as $k => $val){
              echo $val['batch_code'];
              } */
            echo json_encode($result);
        }die;
    }

    public function ajaxGetProgramNameDisplayAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $short_id = $this->_getParam("short_id");
            $recent_batch = $this->getRecentBatch();
            if (!$recent_batch['recent']['batch_id']) {
                $recent_batch['recent']['batch_id'] = 0;
                $recent_batch['recent']['term_id'] = 0;
            }


            //print_r($short_code); die;
            $ProgramMaster_model = new Application_Model_ProgramMaster();
            $result = $ProgramMaster_model->getProgramNameDisplay($recent_batch['recent']['batch_id']);


            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }die;
    }

    public function feeHistoryAction() {
        $this->view->action_name = 'Fee History';
        $this->view->sub_title_name = 'Fee History';
          $assignment_form = new Application_Form_Assignment();
        $this->view->form = $assignment_form;
        $recent_batch = $this->getRecentBatch();
        if (!$recent_batch['recent']['batch_id']) {
            $recent_batch['recent']['batch_id'] = 0;
            $recent_batch['recent']['term_id'] = 0;
        }
        $results = $this->getFullTrans($recent_batch['recent']['batch_id'], $recent_batch['recent']['term_id']);
        $this->view->result = $results;
    }
    public function ajaxGetTransactionViewAction() {
           $this->_helper->layout->disableLayout();
        $this->view->action_name = 'Fee History';
        $this->view->sub_title_name = 'Fee History';
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $term_name = $this->_getParam("term_name");
            
        $academic_year_id = $_SESSION['admin_login']['admin_login']->participant_academic;
        $results = $this->getFullTrans($academic_year_id, $term_id,$term_name);
        $this->view->result = $results;
    }
    }

}
