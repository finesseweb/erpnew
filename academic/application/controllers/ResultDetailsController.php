<?php
//ini_set('display_errors', '1');
class ResultDetailsController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    Private $_unit_id = null;
    private $login_storage = NULL;
    private $roleConfig = NULL;
    private $accessConfig =NULL;

    public function init() {
        // echo "hello"; die;
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
            $this->_redirect('student-portal/student-dashboard');

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
        $this->view->action_name = 'details';
        $this->view->sub_title_name = 'Result Details';
        $this->accessConfig->setAccess('SA_ACAD_PART_SCHOLAR_SHIP');
        $GradeAllocation_model = new Application_Model_GradeAllocation();
        $GradeAllocation_form = new Application_Form_ResultDetails();
        $GradeAllocatonItems_model = new Application_Model_GradeAllocationItems();
        $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
        $studentFeeDetails = new Application_Model_FeeDetails();
         $gl_trans_model = new Application_Model_GlTrans();
        $debit_credit_Acc = $gl_trans_model->getDebitCreditAccount('course-fee');
        
        $batch_id = $this->_getParam("batch_id");
        $this->view->batch_id = $batch_id;
        $term_id = $this->_getParam("term_id");
        $this->view->term_id = $term_id;
        //print_r($feehead_id);die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $GradeAllocation_form;


        switch ($type) {
            case "add":
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocation_form->getValues();
                        $participants_name = $this->getRequest()->getPost('participants_name');
                        $participants_id = $this->getRequest()->getPost('participants_id');
                        $gpa = $this->getRequest()->getPost('gpa');
                        $fee = $this->getRequest()->getPost('fee');
                        $fee_discount = $this->getRequest()->getPost('fee_discount');
                        $tuition_fee = $this->getRequest()->getPost('tuition_fee');
                        $service_fee = $this->getRequest()->getPost('service_fee');
                        $other_annual_charges = $this->getRequest()->getPost('other_annual_charges');
                        $total_fee = $this->getRequest()->getPost('total_fee');
                        $students_id = $this->getRequest()->getPost('student_id');
                        $term_id = $this->getRequest()->getPost('term_id');
                        $batch_id = $this->getRequest()->getPost('academic_id');
                        $remarks = $this->getRequest()->getPost('remarks');
                        $t1_duedate = date("Y-m-d",strtotime($this->getRequest()->getPost('t1_due_date')[0]));
                         $t2_duedate = date("Y-m-d",strtotime($this->getRequest()->getPost('t2_due_date')[0]));
                          $t3_duedate = date("Y-m-d",strtotime($this->getRequest()->getPost('t3_due_date')[0]));
                           $t4_duedate = date("Y-m-d",strtotime($this->getRequest()->getPost('t4_due_date')[0]));
                            $t5_duedate = date("Y-m-d",strtotime($this->getRequest()->getPost('t5_due_date')[0]));
                        for ($i = 0; $i < count($participants_name); $i++) {
                            $bool = $studentFeeDetails->checkDetails($students_id[$i], $term_id, $batch_id);
                            $data = array(
                                'participants_name' => $participants_name[$i],
                                'participants_id' => $participants_id[$i],
                                'gpa' => $gpa[$i],
                                'fee' => $fee [$i],
                                'fee_discount' => $fee_discount[$i],
                                'tuition_fee' => $tuition_fee[$i],
                                'service_fee' => $service_fee[$i],
                                'other_annual_charges' => $other_annual_charges[$i],
                                'total_fee' => $total_fee[$i],
                                'student_id' => $students_id[$i],
                                'term_id' => $term_id,
                                'batch_id' => $batch_id,
                                't1_date' => $t1_duedate,
                                't2_date' => $t2_duedate,
                                't3_date' => $t3_duedate,
                                't4_date' => $t4_duedate,
                                't5_date' => $t5_duedate,
                                'Remarks' => ''
                            );
                            if ($bool != 1) {
                               $last_id = $studentFeeDetails->insert($data);
                                
                            // === $debit_credit_Acc[1]
                            $gl_trans['type'] = 110;
                            $gl_trans['type_no']  = $last_id;
                            $gl_trans['tran_date'] = date('Y-m-d');
                            $gl_trans['account'] = $debit_credit_Acc[1];
                            $gl_trans['memo_'] = 'Tuition fee';
                           
                               
                                $gl_trans['amount'] = (double)$total_fee[$i];
                           
                            $gl_trans['person_type_id'] = 5;
                            $gl_trans['person_id'] = $last_id;
                            $gl_trans_model -> insert($gl_trans);
                       
                                
                                
                                
                            } else {
                                $this->_flashMessenger->addMessage('Fee details is already added');
                                $this->_redirect('fee-details/index');
                                //  $studentFeeDetails->update($data, array('student_id=?' => $students_id[$i], 'term_id=?' => $term_id,'batch_id=?'=>$batch_id));
                            }
                        }
                        $bool = $studentFeeDetails->checkDetails1( $term_id, $batch_id);
                         if ($bool != 1) {
                          $gl_trans['type'] = 110;
                            $gl_trans['type_no']  = ($last_id + 1);
                            $gl_trans['tran_date'] = date('Y-m-d');
                            $gl_trans['account'] = $debit_credit_Acc[0];
                            $gl_trans['memo_'] ="TOTAL AMOUNT";
                           
                                $sum = $studentFeeDetails->getAllSum($term_id, $batch_id);
                               
                                $total_fees_credit = $sum - ($sum+$sum);
                                 $gl_trans['person_id'] = null;
                           $gl_trans['amount'] = $total_fees_credit;
                            $gl_trans['person_type_id'] = 5;
                            
                            $gl_trans_model -> insert($gl_trans);
                         }

                        $this->_flashMessenger->addMessage('Fee details Successfully added');
                        $this->_redirect('fee-details/index');
                        //$last_insert_id = $FeeStructure_model->insert($data);
                    }
                }
                break;
            case 'edit':

               
                $result = $studentFeeDetails->getRecordsByBatchTerm1($term_id, $batch_id);
                
                $edit_element['academic_id'] = $batch_id;
                $edit_element['term_id'] = $term_id;
                $GradeAllocation_form->populate($edit_element);
                $this->view->results = $result;

                break;
            case 'delete':
                $data['status'] = 2;
                if ($grade_id) {
                    $GradeAllocation_model->update($data, array('grade_id=?' => $grade_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('grade-allocation/index');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $studentFeeDetails->getRecords();
                    foreach($result as $key => $batch_term_info)
                    {
                    
                       $result[$key]['term_name'] = $studentFeeDetails->termInfo($batch_term_info['term_id'])['term_name'];
                         $result[$key]['batch_name'] = $studentFeeDetails->batchInfo($batch_term_info['batch_id'])['short_code'];
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

    public function ajaxGetGradeDetailsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getEmployeeTerms($academic_year_id, $department_id, $employee_id);
            $this->view->result = $result;
        }
    }

    public function ajaxGetGradeDetailsPrintAction() {

        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
        $grade_report_id = $this->_getParam("id");
        $this->view->grade_report_id = $grade_report_id;
        //print_r($grade_report_id); die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $GradeAllocationReport_form;
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
            $result = $GradeAllocationReport_model->getRecord($grade_report_id);
            //print_r($result);die;
            $department_id = $result['department_id'];
            // print_r($department_id);die;
            $employee_id = $result['employee_id'];
            $academic_year_id = $result['academic_id'];
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result1 = $EmployeeAllotment_model->getEmployeeTerms($academic_year_id, $department_id, $employee_id);
            //print_r($result1);die;
            $this->view->result1 = $result1;
        }
    }

    //pdf Print

    public function gradePdfAction() {

        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
        $grade_report_id = $this->_getParam("id");
        $this->view->grade_report_id = $grade_report_id;
        //print_r($grade_report_id); die;
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $result = $GradeAllocationReport_model->getRecord($grade_report_id);
        //print_r($result);die;
        $department_id = $result['department_id'];
        // print_r($department_id);die;
        $employee_id = $result['employee_id'];
        $academic_year_id = $result['academic_id'];
        $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
        $result1 = $EmployeeAllotment_model->getEmployeeTerms($academic_year_id, $department_id, $employee_id);
        //print_r($result1);die;
        $this->view->result1 = $result1;

        $pdfheader = $this->view->render('grade-allocation/pdfheader.phtml');
        $pdffooter = $this->view->render('grade-allocation/pdffooter.phtml');
        $htmlcontent = $this->view->render('grade-allocation/grade-pdf.phtml');
        $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Grade Allocation Report Details");
    }

    public function ajaxGetGradeAllocationAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $year = $this->_getParam("year");
            $GradeAllocation_model = new Application_Model_GradeAllocation();
            $TermMaster_model = new Application_Model_TermMaster();
            $Corecourselearning_model = new Application_Model_Corecourselearning();
            $StudentPortal_model = new Application_Model_StudentPortal();
            if ($academic_year_id) {
                $Category_data = $StudentPortal_model->getstudentsyearwise($academic_year_id, $year);
                $this->view->Category_data = $Category_data;
                $Term_data = $TermMaster_model->getGradeTermName($academic_year_id);
                //print_r($Term_data); die;
                //$this->view->Category_data = $Category_data;
                /*  foreach($Term_data as $k=>$val){
                  $term_id = $val['term_id'];
                  $Course_data=$Corecourselearning_model->getGradeCourseName($academic_year_id,$term_id);
                  //  print_r($Course_data);die;
                  } */
                //print_r($Course_data); die;
                $this->view->Term_data = $Term_data;
            }
        }
    }

    public function ajaxGetStudentDetailsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            // print_r($term_id);die;
            $Academic_model = new Application_Model_Academic();
            $result = $Academic_model->getAcademicDesignOrderByDate($academic_year_id);
           
            $prev_index = '';
            $prev_index_details = array();
            // echo "<pre>";print_r($result);exit;

            foreach ($result as $key => $value) {
                if ($term_id == $value['id'] && $key != 0) {
                    $prev_index = $key - 1;
                    $prev_index_details = array('id' => $result[$prev_index]['id'],
                        'c_type' => $result[$prev_index]['c_type'],
                        'term_name' => $result[$prev_index]['term_name']);
                }
            }

            $StudentPortal_model = new Application_Model_StudentPortal();
            $Category_data = $StudentPortal_model->getstudentsdetailsByTerm_id($academic_year_id, $term_id, $prev_index_details);
          
        // echo "<pre>";   print_r($Category_data);exit;
            if ($Category_data == 0) {
                echo "0";
                die;
            } else if ($Category_data == 3) {
                echo "3";
                die;
            } else {
                $this->view->category_data = $Category_data;
            }
        }
    }

    public function ajaxGetTermsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $grade_allocation_id = $this->_getParam("grade_allocate_id");

            if ($grade_allocation_id) {

                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
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
                if ($k == $grade_allocate_result['term_id']) {

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
            $grade_allocation_id = $this->_getParam("grade_allocate_id");
            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getComponentCourses($academic_year_id, $department_id, $employee_id, $term_id);
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control">';
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

    public function ajaxGetCoursesEditAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $grade_allocation_id = $this->_getParam("grade_allocate_id");
            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $result = $EmployeeAllotment_model->getEditComponentCourses($academic_year_id, $department_id, $employee_id, $term_id);
            echo '<div class="col-sm-3 employee_class">';
            echo '<div class="form-group">';
            echo '<label class="control-label">Courses</label>';
            echo '<select type="text" name="course_id" id="course_id" class="form-control">';
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                $selected = '';
                if ($k == $grade_allocate_result['course_id']) {

                    $selected = "selected";
                }
                echo '<option value="' . $k . '" ' . $selected . '>' . $val . '</option>';
            }
            echo '</select>';
            echo '</div></div>';
        }die;
    }

/// ramesh  //	
    public function ajaxGetEvaluationComponentsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");
            $grade_allocation_id = $this->_getParam("grade_allocate_id");
            if ($grade_allocation_id) {
                $GradeAllocation_model = new Application_Model_GradeAllocation();
                $grade_allocate_result = $GradeAllocation_model->getRecord($grade_allocation_id);
            }
            $EvaluationComponents_model = new Application_Model_EvaluationComponents();
            $result = $EvaluationComponents_model->getComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id);
        }die;
    }

    public function ajaxGetGradeEvaluationComponentsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");

            $EvaluationComponents_model = new Application_Model_EvaluationComponents();
            $result = $EvaluationComponents_model->getGradeAddComponents($academic_year_id, $department_id, $employee_id, $term_id, $course_id);
        }die;
    }

/// ramesh  //		
    public function ajaxGetEmployeeAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department_id = $this->_getParam("department_id");
            // print_r($academic_id); die;
            $HRMModel_model = new Application_Model_HRMModel();
            $result = $HRMModel_model->getEmployee($department_id);


            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }die;
    }

    public function gradeAllocationReportAction() {

        $this->view->action_name = 'Grade Allocation Report';
        $this->view->sub_title_name = 'grade-allocation-report';
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
        $GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
        $GradeAllocationReportItems_model = new Application_Model_GradeAllocationReportItems();
        //$GradeAllocatonReportItems_model = new Application_Model_GradeAllocationReportItems();
        $grade_report_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $GradeAllocationReport_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocationReport_form->getValues();

                        $last_insert_id = $GradeAllocationReport_model->insert($data);
                        $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
                        $employee_result = $EmployeeAllotment_model->getEmployeeTerms($data['academic_id'], $data['department_id'], $data['employee_id']);
                        for ($i = 0; $i < count($employee_result); $i++) {
                            $grade = $_POST['grade'];

                            for ($k = 0; $k < count($_POST['grade']['student_id_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']]); $k++) {
                                $item_data['report_id'] = $last_insert_id;
                                $item_data['term_id'] = $employee_result[$i]['term_id'];
                                $item_data['course_id'] = $employee_result[$i]['course_id'];
                                $item_data['student_id'] = $grade['student_id_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']][$k];
                                $item_data['component_grades'] = $grade['grades_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']][$k];
                                $item_data['component_weightages'] = $grade['weightages_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']][$k];
                                $item_data['grade_point'] = $grade['grade_point_' . $employee_result[$i]['term_id'] . '_' . $employee_result[$i]['course_id']][$k];

                                $GradeAllocationReportItems_model->insert($item_data);
                            }
                        }

                        //echo '<pre>'; print_r($grade); die;
                        /* $data['term_id'] = $this->getRequest()->getPost('term_id');
                          $data['course_id'] = $this->getRequest()->getPost('course_id');
                          $data['component_id'] = $this->getRequest()->getPost('component_id');
                          $last_insert_id = $GradeAllocation_model->insert($data);
                          $grade = $this->getRequest()->getPost('grade');
                          foreach(array_filter($grade['student_id']) as $k => $student_id){
                          $grade['grade_allocation_id'] = $last_insert_id;
                          $grade['student_id'] = $student_id;
                          $grade['grade_value'] = $grade['grade_value'][$k];
                          $GradeAllocatonItems_model->insert($grade);
                          } */

                        $this->_flashMessenger->addMessage('Grade Report Successfully added');

                        $this->_redirect('grade-allocation/grade-allocation-report');
                    }
                }


                break;
            /* case 'edit': 
              $result = $GradeAllocationReport_model->getRecord($grade_report_id);
              $GradeAllocationReport_form->populate($result);
              //$this->view->grade_allocate_id = $grade_report_id;
              $this->view->result = $result;
              if ($this->getRequest()->isPost()) {
              if ($GradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
              $data = $GradeAllocationReport_form->getValues();
              //$data['term_id'] = $this->getRequest()->getPost('term_id');
              //$data['course_id'] = $this->getRequest()->getPost('course_id');
              //$data['component_id'] = $this->getRequest()->getPost('component_id');
              $GradeAllocationReport_model->update($data, array('grade_report_id=?' => $grade_report_id));

              $grade = $this->getRequest()->getPost('grade');
              $GradeAllocatonItems_model ->trashItems($grade_id);
              foreach(array_filter($grade['student_id']) as $k => $student_id){
              $grade['grade_allocation_id'] = $grade_id;
              $grade['student_id'] = $student_id;
              $grade['grade_value'] = $grade['grade_value'][$k];
              $GradeAllocatonItems_model->insert($grade);
              }

              $this->_flashMessenger->addMessage('Grade Report Updated Successfully');
              $this->_redirect('grade-allocation/grade-allocation-report');
              } else {
              }
              }
              break;
              case 'delete':
              $data['status'] = 2;
              if ($structure_id){
              $GradeAllocation_model->update($data, array('grade_id=?' => $grade_id));
              $this->_flashMessenger->addMessage('Details Deleted Successfully');
              $this->_redirect('grade-allocation/index');
              }
              break; */
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                //echo $empl_id;exit;
                if (in_array($role_id, $this->roleConfig)) {
                    $result = $GradeAllocationReport_model->getRecords();
                } else {
                    $result = $GradeAllocationReport_model->getRecordsByFacultyId($empl_id);
                }
                //$result = $GradeAllocationReport_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function ajaxGetEmployeeValidationAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $department_id = $this->_getParam("department_id");
            $employee_id = $this->_getParam("employee_id");
            //print_r($academic_id); die;
            $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
            $result = $GradeAllocationReport_model->getGradeAllocateCount($academic_year_id, $department_id, $employee_id);
            // echo'<pre>';print_r(count($result)); die;
            $counts = $result['employee_count'];
            // print_r($counts);die;
            echo json_encode($counts);
            die;
            //echo '<pre>'; print_r($counts); die;
            // $this->view->result = $result;
        }
    }

    public function ajaxPromotedAction() {
        $this->_helper->layout->disableLayout();

        $studentFeeDetails = new Application_Model_FeeDetails();
       
        $fee_id=$_POST['id'];
        $promoted=$_POST['value'];

        if($promoted==0){
            $p_val=1;
            $msg='Promoted Successfully';
        }else{
            $p_val=0;
            $msg='Promotion Cancel Successfully';
        }
        $data = array(
            'promoted' => $p_val
            
        );
        $studentFeeDetails->update($data, array('fee_details_id=?' => $fee_id));
        echo strip_tags($msg);

    }

    public function ajaxGetFeeAction() {
        $studentFeeDetails = new Application_Model_FeeDetails();
        $studentFeeLogs = new Application_Model_FeeDetailLogs();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $fee_id = $this->_getParam("fee_id");
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam("batch_id");
            $data = array(
                'fee_discount' => $this->_getParam("discount"),
                'total_fee' => $this->_getParam("cal_fee"),
                'Remarks' => $this->_getParam("remarks")
            );


            $studentFeeDetails->update($data, array('fee_details_id=?' => $fee_id));
            unset($data['Remarks']);
            $data['participants_id'] = $fee_id;
            $data['updated_date'] = date('Y-m-d h:s:i');
            $studentFeeLogs->insert($data);
        //    echo"<pre>";print_r($term_id."===".$batch_id); exit;
            $result = $studentFeeDetails->getRecordsByBatchTerm1($term_id, $batch_id);
            
            echo' <div class="col-md-12 table-responsive">  <table class="table table-striped table-bordered mb30 jambo_table bulk_action" id="dataTable">


                                                <thead>
                                                    <tr>

                                                        <th >S.  No.</th>
                                                        <th >Participants Name</th>
                                                        <th>Participants ID</th>
                                                        <th >CGPA</th>
                                                        <th >Total Fee</th>
                                                        <th>Tuition Fee</th>
                                                        <th>Service Fee</th>
                                                        <th>Other Annual Charges</th>
                                                        <th>Fee Discount on(tuition fee)</th>
                                                        <th>Total Fee<br/>(After Discounting on Tuition Fee)</th>
                                                        <th>Remarks</th>
                                                        <th>Action</th>
                                                </thead>
                                                <tbody>';
            foreach ($result as $key => $value):




                echo ' <tr>
                                                            <td>' . ++$key . '</td>
                                                               
                                                            <td id="participants_name_' . $key . '">' . $value['participants_name'] . '</td>
                                                            <td id="participants_id_' . $key . '">' . $value['participants_id'] . '</td>
                                                            <td id="gpa_' . $key . '">' . $value['gpa'] . '</td>
                                                            <td id="total_' . $key . '">' . $value['fee'] . '</td>
                                                            <td id="tuition_fee_' . $key . '">' . $value['tuition_fee'] . '</td>
                                                            <td id="service_fee' . $key . '">' . $value['service_fee'] . '</td>
                                                            <td id="other_annual_charges' . $key . '">' . $value['other_annual_charges'] . '</td>
                                                            <td><div id="x_box">
                                                                    <div id="text_box' . $key . '" style="display:none;"><input size="3" maxlength="3" onkeyup="myCalcFun(this)" type="text" style="position:absolute;" id="discount_' . $key . '" value="' . $value['fee_discount'] . '" /></div>
                                                                    <label id="label_box' . $key . '" style="position:absolute;">' . $value['fee_discount'] . "%" . ' </label>
                                                                </div>
                                                            </td>
                                                            <td id="total_fee' . $key . '">' . $value['total_fee'] . "</td>";
                echo "<td><textarea rows='1' col='2' name='remarks[]' id ='remarks_'" . $key . " placeholder='Remarks...'>". $value['Remarks'] . "</textarea></td>";
                $func = 'onclick="myfunc(this,' . $value["fee_details_id"] . ', &quot;text_box' . $key . '&quot;, &quot;label_box' . $key . '&quot;)"';
                echo '<td><a href="javascript:0;" class="btn btn-primary" ' . $func . ' data-id="edit" id="edit_save_' . $key . '">Edit</a></td>';
                echo " </tr>";
            endforeach;

            echo "</tbody>";
            echo "</table></div>";
            die;
        }
    }

}

//