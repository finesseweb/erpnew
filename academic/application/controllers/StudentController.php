<?php

class StudentController extends Zend_Controller_Action {

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
        if ($data->role_id == 0)
            $this->_redirect("student-portal/student-dashboard");
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
        $this->accessConfig->setAccess('SA_ACAD_ENROLLMENT');
        $student_model = new Application_Model_StudentPortal();
        $student_form = new Application_Form_StudentPortal();
        $StudentPortal_model = new Application_Model_StudentPortal();
        $studentFeeDetails = new Application_Model_FeeDetails();
        $participant_model = new Application_Model_ParticipantsLogin();
        $alumni_model = new Application_Model_Alumni();
        $fee_details = new Application_Model_FeeDetails();
        //======[FOR BANK PURPOSE]=======//
        $gl_trans_model = new Application_Model_GlTrans();
        $debit_credit_Acc = $gl_trans_model->getDebitCreditAccount('course-fee');
        $student_id = $this->_getParam("id");
        $type = $this->_getParam("type");

        $this->view->type = $type;
        $this->view->form = $student_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($student_form->isValid($this->getRequest()->getPost())) {
                        $data = $student_form->getValues();
                        $result2 = $StudentPortal_model->getstudentsdetailsForFirstTerm($data['academic_id']);
                        $result2['participants_name'] = $data['stu_fname'] . " " . $data['stu_lname'];
                        $result2['participants_id'] = $data['stu_id'];
                        unset($data['participant_username']);
                        unset($data['participant_pword']);
                        unset($data['confirm_password']);
                        unset($data['linked_in']);
                        unset($data['secondary_mail']);
                        if ($result2 != 0) {
                            $check = $fee_details->checkWithPdmId($result2['participants_id'], $result2['batch_id'], $result2['term_id']);
                            if ($check == 0) {
                                $last_id = $studentFeeDetails->insert($result2);
                                for ($gl_account = 0; $gl_account < 2; $gl_account++) {
                                    $gl_trans['type'] = 110;
                                    $gl_trans['type_no'] = $last_id;
                                    $gl_trans['tran_date'] = date('Y-m-d');
                                    $gl_trans['account'] = $debit_credit_Acc[$gl_account];
                                    $gl_trans['memo_'] = 'Tuition fee';
                                    if ($gl_account == 0) {
                                        $gl_trans['amount'] = $result2['total_fee'] - ((int)$result2['total_fee'] + (int) $result2['total_fee']);
                                    } elseif ($gl_account == 1) {
                                        $gl_trans['amount'] = (double) $result2['total_fee'];
                                    }
                                    $gl_trans['person_type_id'] = 5;
                                  //  $gl_trans_model->insert($gl_trans);
                                }
                            } else
                                $studentFeeDetails->update($result2, array('participants_id=?' => $result2['participants_id'], 'batch_id =?' => $result2['batch_id'], 'term_id =?' => $result2['term_id']));
                        }
                        $display_phone = $this->getRequest()->getPost('display_phone');

                        $data_save = $student_form->getValues();
                        $status = $this->getRequest()->getPost('passing_status');
                        if (empty($status))
                            $status = 0;
                        $data['passing_status'] = $status;
                        $short_code = $studentFeeDetails->getShortCode($data['academic_id']);
                       // echo $short_code[0]; die;
                       // $std_id = $this->auto_generate_pdmid_cells_ex($data['academic_id'], implode('-',$short_code));
                        //$data['stu_id'] = $std_id;
                        $last_insert_id = $student_model->insert($data);


                        // $stu_status = $this->getRequest()->getPost('stu_status');
                        $bool = $student_model->checkRecords($last_insert_id);
                        $result = $student_model->getRecordsById($last_insert_id);
                        $data2['participant_fname'] = $data_save['stu_fname'];
                        $data2['student_id'] = $last_insert_id;
                        $data2['participant_lname'] = $data_save['stu_lname'];
                        $data2['participant_email'] = $data_save['secondary_mail'];
                        $data2['participant_username'] = $data_save['participant_username'];
                        if (!empty($data_save['participant_pword']))
                            $data2['participant_pword'] = $data_save['participant_pword'];
                        $data2['participant_continue'] = $result[0]['stu_status'];
                        $data2['participant_yop'] = $result[0]['passing_year'];
                        $data2['participants_file'] = $data['filename'];
                        $data2['participant_academic'] = $result[0]['academic_id'];
                        $data2['participant_Alumni'] = $status;
                        $data2['linked_in'] = $data_save['linked_in'];
                        $data2['fa_salutation'] = $result[0]['gender'];
                        $data2['alumni_url'] = str_replace(" ", "", $result[0]['stu_fname']) . $result[0]['stu_id'];
                        $data2['roll_no'] = $result[0]['stu_id'];
                        if ($bool != 1) {
                            $last_id = $participant_model->insert($data2);
                        } else {
                            $participant_model->update($data2, array('student_id=?' => $last_insert_id));
                        }
                        if ($status == 1) {
                            $bool = $student_model->checkAlumni($last_insert_id);
                            $data3['al_email_id'] = $result[0]['stu_email_id'];
                            $data3['al_address'] = $result[0]['premanent_addr'];
                            $data3['al_url'] = str_replace(" ", "", $result[0]['stu_fname']) . $result[0]['stu_id'];
                            $data3['student_id'] = $result[0]['student_id'];
                            $data3['al_contact_no'] = $result[0]['stu_mobileno'];
                            if ($display_phone) {
                                $data3['display_phone'] = 1;
                            } else {
                                $data3['display_phone'] = 0;
                            }
                            if ($bool != 1) {

                                // $data3['user_id'] = $last_id;
                                $alumni_model->insert($data3);
                            } else {

                                $alumni_model->update($data3, array('student_id=?' => $last_insert_id));
                            }
                        }
                        // echo "<pre>";print_r($result);echo"</pre>";exit;
                        $dirPath = realpath(APPLICATION_PATH . '/../public/images') . '/' . $last_insert_id . '/student_details/';

                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 755, true);
                        }
                        $file_name = $_FILES["file"]["name"];

                        $tem_name = $_FILES["file"]["tmp_name"];
                        $imageFileType = strtolower(pathinfo($file_name[0],PATHINFO_EXTENSION));
                        
                        if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
                        if (move_uploaded_file($tem_name[0], $dirPath . $file_name[0])) {
                            //echo "File is valid, and was successfully uploaded"; 
                        } else {
                            // echo "Upload failed";  
                        }
                        $file_data['filename'] = "public/images/" . $last_insert_id . "/student_details/" . $file_name[0];

                        $student_model->update($file_data, array('student_id=?' => $last_insert_id));
                        }
                        if ($result2 != 0) {
                            $this->_flashMessenger->addMessage('Student added Successfully');
                            $this->_redirect('student/index');
                        } else
                            $_SESSION['flash_error'] = '0';
                        $this->_flashMessenger->addMessage('Student added Successfully Please create terms and fee structure ');
                        $this->_redirect('student/index');
                    }
                }


                break;
            case 'edit':
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $student_model->getRecord($student_id);
                $alumni_detail = $student_model->getAlumniDetail($student_id);
                $this->view->alumni_detail = $alumni_detail;
                $_SESSION['StuPword'] = $alumni_detail['participant_pword'];
                $student_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($student_form->isValid($this->getRequest()->getPost())) {
                        $data = $student_form->getValues();
                        $result2 = $StudentPortal_model->getstudentsdetailsForFirstTerm($data['academic_id']);
                       //  echo "<pre>";print_r($result2);exit;
                        $result2['participants_name'] = $data['stu_fname'] . " " . $data['stu_lname'];

                        $result2['participants_id'] = $data['stu_id'];
                        //print_r($result2);exit;
                        if ($result2 != 0 && $result2['term_id']!='') {
                            $check = $fee_details->checkWithPdmId($result2['participants_id'], $result2['batch_id'], $result2['term_id']);
                            if ($check == 0) {
                                $last_id = $studentFeeDetails->insert($result2);
                                for ($gl_account = 0; $gl_account < 2; $gl_account++) {
                                    $gl_trans['type'] = 110;
                                    $gl_trans['type_no'] = $student_id;
                                    $gl_trans['tran_date'] = date('Y-m-d');
                                    $gl_trans['account'] = $debit_credit_Acc[$gl_account];
                                    $gl_trans['memo_'] = 'Tuition fee';
                                    if ($gl_account == 0) {
                                        $gl_trans['amount'] = $result2['total_fee'] - ((int) $result2['total_fee'] + (int) $result2['total_fee']);
                                    } elseif ($gl_account == 1) {
                                        $gl_trans['amount'] = $result2['total_fee'];
                                    }
                                    $gl_trans['person_type_id'] = 5;
                                   // $gl_trans_model->insert($gl_trans);
                                }
                            } else {
                                $studentFeeDetails->update($result2, array('participants_id=?' => $result2['participants_id'], 'batch_id =?' => $result2['batch_id'], 'term_id =?' => $result2['term_id']));
                            }
                        }


                        $data_save = $student_form->getValues();

                        $display_phone = $this->getRequest()->getPost('display_phone');
                        $status = $this->getRequest()->getPost('passing_status');
                        if (empty($status))
                            $status = 0;
                        $data['passing_status'] = $status;

                        $delete_arr = array('participant_username',
                            'participant_pword',
                            'confirm_password',
                            'linked_in',
                            'secondary_mail');
                        for ($i = 0; $i < count($delete_arr); $i++) {
                            foreach ($data as $key => $value) {
                                if (($key = $delete_arr[$i])) {
                                    unset($data[$key]);
                                }
                            }
                        }
                        // print_r($data);exit;
                        $dirPath = realpath(APPLICATION_PATH . '/../public/images') . '/' . $student_id . '/student_details/';

                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 755, true);
                        }
                        $file_name = $_FILES["file"]["name"];

                        $tem_name = $_FILES["file"]["tmp_name"];
                        $imageFileType = strtolower(pathinfo($file_name[0],PATHINFO_EXTENSION));
                        
                        if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
                        if (move_uploaded_file($tem_name[0], $dirPath . $file_name[0])) {
                            //echo "File is valid, and was successfully uploaded"; 
                        } else {
                            // echo "Upload failed";  
                        }
                        if (!empty($file_name[0])) {
                            $data['filename'] = "public/images/" . $student_id . "/student_details/" . $file_name[0];
                        } else {
                            $data['filename'] = $result['filename'];
                        }
                        }
                        else
                        {
                            unset($data['filename']);
                        }
                        $student_model->update($data, array('student_id=?' => $student_id));
                        $bool = $student_model->checkRecords($student_id);
                        $result = $student_model->getRecordsById($student_id);
                        // print_r($result);exit;
                        $data2['participant_fname'] = $data_save['stu_fname'];
                        $data2['student_id'] = $student_id;
                        $data2['participant_lname'] = $data_save['stu_lname'];
                        $data2['participant_email'] = $data_save['secondary_mail'];
                        $data2['participant_username'] = $data_save['participant_username'];
                        if (!empty($data_save['participant_pword']))
                            $data2['participant_pword'] = $data_save['participant_pword'];
                        $data2['participant_continue'] = $result[0]['stu_status'];
                        $data2['participant_yop'] = $result[0]['passing_year'];
                        $data2['participants_file'] = $data['filename'];
                        $data2['participant_academic'] = $result[0]['academic_id'];
                        $data2['participant_Alumni'] = $status;
                        $data2['linked_in'] = $data_save['linked_in'];
                        $data2['fa_salutation'] = $result[0]['gender'];
                        $data2['alumni_url'] = str_replace(" ", "", $result[0]['stu_fname']) . $result[0]['stu_id'];
                        $data2['roll_no'] = $result[0]['stu_id'];
                        if ($bool != 1) {
                            $last_id = $participant_model->insert($data2);
                        } else {
                            $participant_model->update($data2, array('student_id=?' => $student_id));
                        }
                        if ($status == 1) {
                            $bool = $student_model->checkAlumni($student_id);
                            $data3['al_email_id'] = $result[0]['stu_email_id'];
                            $data3['al_address'] = $result[0]['premanent_addr'];
                            $data3['al_url'] = str_replace(" ", "", $result[0]['stu_fname']) . $result[0]['stu_id'];
                            $data3['student_id'] = $result[0]['student_id'];
                            $data3['al_contact_no'] = $result[0]['stu_mobileno'];
                            if ($display_phone) {
                                $data3['display_phone'] = 1;
                            } else {
                                $data3['display_phone'] = 0;
                            }
                            if ($bool != 1) {

                                // $data3['user_id'] = $last_id;
                                $alumni_model->insert($data3);
                            } else {

                                $alumni_model->update($data3, array('student_id=?' => $student_id));
                                $message = 'Student Details Updated Successfully';
                            }
                        }
                        if ($result2 != 0) {
                            $this->_flashMessenger->addMessage('Student Details Updated Successfully');
                            $this->_redirect('student/index/type/edit/id/' . $student_id);
                        } else {
                            $_SESSION['admin_login']['admin_login']->flash_error = '0';
                            $this->_flashMessenger->addMessage('Student Details Updated Successfully');
                            $this->_redirect('student/index/type/edit/id/' . $student_id);
                        }
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($student_id) {
                    $student_model->update($data, array('student_id=?' => $student_id));
                    $this->_flashMessenger->addMessage('Student Details Deleted Successfully');
                    $this->_redirect('student/index');
                }
                break;
            case 'download':
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->layout->disableLayout();
                $path_file = realpath(APPLICATION_PATH . '/../public/images') . '/';
                //print_r($path_file);die;
                $id = $this->_getParam("id");
                $uploaded_file = $this->_getParam("file");
                $filename = $id . '/' . 'student_details' . '/' . $uploaded_file; // of course find the exact filename....
                $file = $path_file . $filename;
                $validator = new Zend_Validate_File_Exists();
                $validator->addDirectory($path_file);

                //zfdebug(mime_content_type($file));
                if ($validator->isValid($filename)) {
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Cache-Control: private', false); // required for certain browsers 
                    //header('Content-Type: '.mime_content_type($file));
                    header('Content-Disposition: attachment; filename="' . basename($file) . '";');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($file));
                    readfile($file);
                } else {
                    $this->_flashMessenger->addMessage('File does not exist');
                    $this->_redirect('student/index');
                }
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

    public function auto_generate_pdmid_cells_ex($batch_id, $short_code) {
        $id = 0;

        $id_prefix = explode('-', $short_code);
        $id_prefix_concat = $id_prefix[0] . '01';
        $academic_model = new Application_Model_FeeDetails();
        $max_batch_id = $academic_model->getMaxExistingBatch($id_prefix[0]);
        //echo $batch_id; die;
        $studentid_result = $academic_model->getMaxId($batch_id);

        if (!$studentid_result)
            $id_prefix_inc = (int) substr($studentid_result, 3, strlen($studentid_result) - 6) + 1;
        else
            $id_prefix_inc = (int) substr($studentid_result, 3, strlen($studentid_result) - 6);

        if (strlen($id_prefix_inc) == 1)
            $id_prefix_inc = '0' . $id_prefix_inc;
        else
            $id_prefix_inc = $id_prefix_inc;
       
        $id_prefix_concat = $id_prefix[0] . $id_prefix_inc;
        $last_student = substr($studentid_result, strlen($studentid_result) - 3);
        $student_inc_id = $last_student + 1;
        if (empty($last_student)) {
            $id = $id_prefix_concat . '00' . 1;
        } else {
            if (strlen($student_inc_id) == 1) {
                $id = $id_prefix_concat . '00' . $student_inc_id;
            } else if (strlen($student_inc_id) == 2) {
                $id = $id_prefix_concat . '0' . $student_inc_id;
            } else if (strlen($student_inc_id) == 3) {
                $id = $id_prefix_concat . $student_inc_id;
            } else {
                $id_prefix_inc = (int) substr($studentid_result, 3, strlen($studentid_result) - 6) + 1;
                $id_prefix_concat = $id_prefix[0] . $id_prefix_inc;
                $id = $id_prefix_concat . '00' . 1;
            }
        }
        return $id;
    }

    public function ajaxGetMaxIdAction() {
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            $short_code = $this->_getParam("short_code");
            $id = $this->auto_generate_pdmid_cells_ex($batch_id, $short_code);
            echo $id;
            die;
        }
    }

    public function fetchDiscontinuedStudentDetailAction() {
        $this->_helper->layout->disableLayout();
        $stu_id = $this->_getParam("participant_id");
        $student_model = new Application_Model_StudentPortal();
        $stu_pre_detail = $student_model->fetchDiscontinuedStudentDetailById($stu_id);
        if (is_array($stu_pre_detail) && !empty($stu_pre_detail)) {
            echo json_encode($stu_pre_detail);
        } else {
            echo '0';
        }
        exit;
    }

    //===========[STUDENT DASHBOARD]=================//
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

    //====================[ATTENDANCE COMMAND]============//    

    public function attendanceAction() {
        $this->view->action_name = 'attendance';
        $this->view->sub_title_name = 'Student Attendance';
        $atendence_saver_model = new Application_Model_Attendance();
        $employee_model = new Application_Model_HRMModel();
        $present = 0;
        $absent = 0;
        $leave = 0;
        $result = $atendence_saver_model->getRecordByStudentId($_SESSION['admin_login']['admin_login']->student_id);

        foreach ($result as $key => $value) {
            if ($result[$key]['class_1'] != '0' && $result[$key]['class_1'] != 'Absent' && $result[$key]['class_1'] != 'Leave') {
                $present += 1;
            } else if ($result[$key]['class_1'] == 'Absent') {
                $absent += 1;
            } else if ($result[$key]['class_1'] == 'Leave') {
                $leave += 1;
            }



            if ($result[$key]['class_2'] != '0' && $result[$key]['class_2'] != 'Absent' && $result[$key]['class_2'] != 'Leave') {
                $present += 1;
            } else if ($result[$key]['class_2'] == 'Absent') {
                $absent += 1;
            } else if ($result[$key]['class_2'] == 'Leave') {
                $leave += 1;
            }


            if ($result[$key]['class_3'] != '0' && $result[$key]['class_3'] != 'Absent' && $result[$key]['class_3'] != 'Leave') {
                $present += 1;
            } else if ($result[$key]['class_3'] == 'Absent') {
                $absent += 1;
            } else if ($result[$key]['class_3'] == 'Leave') {
                $leave += 1;
            }


            if ($result[$key]['class_4'] != '0' && $result[$key]['class_4'] != 'Absent' && $result[$key]['class_4'] != 'Leave') {
                $present += 1;
            } else if ($result[$key]['class_4'] == 'Absent') {
                $absent += 1;
            } else if ($result[$key]['class_4'] == 'Leave') {
                $leave += 1;
            }

            if ($result[$key]['class_5'] != '0' && $result[$key]['class_5'] != 'Absent' && $result[$key]['class_5'] != 'Leave') {
                $present += 1;
            } else if ($result[$key]['class_5'] == 'Absent') {
                $absent += 1;
            } else if ($result[$key]['class_5'] == 'Leave') {
                $leave += 1;
            }

            $result[$key]['faculty_1'] = $employee_model->getAllEmployee($result[$key]['faculty_1'])[0]['name'];
            $result[$key]['faculty_2'] = $employee_model->getAllEmployee($result[$key]['faculty_2'])[0]['name'];
            $result[$key]['faculty_3'] = $employee_model->getAllEmployee($result[$key]['faculty_3'])[0]['name'];
            $result[$key]['faculty_4'] = $employee_model->getAllEmployee($result[$key]['faculty_4'])[0]['name'];
            $result[$key]['faculty_5'] = $employee_model->getAllEmployee($result[$key]['faculty_5'])[0]['name'];
        }

        $result[0]['present'] = $present;
        $result[0]['absent'] = $absent;
        $result[0]['leave'] = $leave;
        $result[0]['total'] = $present + $absent + $leave;
        $result[0]['percent'] = round(($present / $result[0]['total']) * 100);
        $this->view->result = $result;
    }

    //==============[ASSIGNMENTS]==============//

    public function assignmentsAction() {
        $this->view->action_name = 'assignments';
        $this->view->sub_title_name = 'Student Assignments';
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
        $this->view->action_name = 'fee-status';
        $this->view->sub_title_name = 'Student Fee Status';
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

    public function feedBackAction() {
        $this->view->action_name = 'FeedBack';
        $this->view->sub_title_name = 'Student FeedBack';
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
                $len = $this->getRequest()->getPost('count');
                $len1 = $this->getRequest()->getPost('count1');
                $bool = $student_feed_model->hasRated($_SESSION['admin_login']['admin_login']->student_id, $data['course'], $data['term'], $data['batch']);
                $bool1 = $instructor_feed->hasRated($_SESSION['admin_login']['admin_login']->student_id, $data['instructor'], $data['term'], $data['batch']);

                if ($bool == 0) {
                    for ($i = 1; $i <= count($len); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_$i"));
                        $data['feed'] = $arr[1];
                        $data['question_no_c'] = $arr[0];
                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        $student_feed_model->insert($data);
                    }
                }

                if ($bool > 0) {
                    for ($i = 1; $i <= count($len); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_$i"));
                        $data['feed'] = $arr[1];
                        $data['question_no_c'] = $arr[0];
                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        $student_feed_model->update($data, array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'course=?' => $data['course'], 'question_no_c=?' => $arr[0], 'term=?' => $data['term'], 'batch=?' => $data['batch']));
                    }
                }


                if ($bool1 > 0) {
                    for ($i = 1; $i <= count($len1); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_I$i"));
                        $data['feed'] = $arr[1];
                        $data['question_no_c'] = $arr[0];
                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        $instructor_feed->update($data, array('student_id =?' => $_SESSION['admin_login']['admin_login']->student_id, 'instructor=?' => $data['instructor'], 'question_no_c=?' => $arr[0], 'term=?' => $data['term'], 'batch=?' => $data['batch']));
                    }
                }


                if ($bool1 == 0) {
                    for ($i = 1; $i <= count($len1); $i++) {
                        $arr = explode("-", $this->getRequest()->getPost("rate_I$i"));
                        $data['feed'] = $arr[1];
                        $data['question_no_c'] = $arr[0];
                        $data['student_id'] = $_SESSION['admin_login']['admin_login']->student_id;
                        $instructor_feed->insert($data);
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
        $term_id = $this->_getParam('term_id');
        $batch_id = $this->_getParam('academic_year_id');
        $course_id = $this->_getParam('course_id');
        $result = $faculty->getFaculty($term_id, $batch_id, $course_id);

        $result[0]['faculty_id'] .= ',' . $result[0]['employee_id'];
        // print_r($course_cordinatior);exit;
        $faculty_id = explode(',', $result[0]['faculty_id']);
        $visiting_faculty = explode(',', $result[0]['visiting_faculty_id']);

        //======[MERGING BOTH FACULTY ARRAY]=======//
        $faculty_arr = array_merge($faculty_id, $visiting_faculty);

        //====={MAKING ARRAY UNIQUE}==============//
        $all_unique_faculty = array_unique($faculty_arr);

        //======[GETTING NAME OF AL THE EMPLYOEE]=======//
        for ($i = 0; $i < count($all_unique_faculty); $i++) {
            if ($all_unique_faculty[$i] != 'NA')
                $empl_name[$i] = $employee_model->getAllEmployee($all_unique_faculty[$i])[0];
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
            $result['instructor_value'] = $instructor_feed->ratedValue($_SESSION['admin_login']['admin_login']->student_id, $instructor, $term_id, $batch_id);


            $result['rating'] = $rating_model->getRecords1();

            $this->view->result = $result;
        }
    }
    
    
        //PROGRAM DESIGN VIEW


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
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $student_detail1['stu_id']);
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
                
                $this->view->gradesheet_number = $gradesheet_number;
            }
            $htmlcontent = $this->view->render('report/secondyeargradesheetreport.phtml');
            print_r($htmlcontent);
            exit;
        }
    }

}
