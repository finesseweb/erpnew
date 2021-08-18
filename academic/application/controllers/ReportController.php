<?php

/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class ReportController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    private $_checkUser = null;
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

        $this->_checkUser = $user = $_POST['username'];
        $password = $_POST['password'];


        if ($user != 'admin' && $password != 'admin@123') {
            $this->authonticate();
        }
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        if($data->role_id==0)
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

    //follow up
    public function reportAction() {

        $this->view->action_name = 'enquiry';
        $this->view->sub_title_name = 'report';
        $Report_model = new Application_Model_Report();
        $Report_form = new Application_Form_Report();
        //$Followup_id = $this->_getParam("id");
        $type = $this->_getParam("type");


        $date = new Zend_Date();
        $currentDate = $date->toString('Y-MM-d');
        $this->view->currentDate = $currentDate;
        $startYear = $date->toString('Y');
        $nextYear = $startYear + 1;
        $AcademicYear = $startYear . '-' . $nextYear;

        $NextYear1 = $nextYear + 1;
        $NextYear = $nextYear . '-' . $NextYear1;

        $this->view->AcademicYear = $AcademicYear;
        $this->view->NextYear = $NextYear;
        switch ($type) {
            case "search":
                $this->view->type = $type;
                $this->view->Report_form = $Report_form;
                // $data = $this->getRequest()->getPost();

                if ($this->getRequest()->isPost()) {
                    if ($Report_form->isValid($this->getRequest()->getPost())) {
                        $data = $Report_form->getValues();
                        $export = $this->getRequest()->getPost('export');

                        $branch = '';
                        $search_type = $data['search_type'];
                        $country = $data['country_id'];
                        $state = $data['state_id'];
                        $city = $data['city_id'];
                        $location = $data['location_id'];
                        $branch_id = $data['branch_id'];
                        if (is_array($branch_id)) {
                            $branch = implode(',', $branch_id);
                        }



                        $from_date = $data['from_date'];

                        $to_date = $data['to_date'];

                        $academic_year = $data['academic_year'];

                        $program = $data['program_id'];

                        $subprogram = $data['subprogram_id'];

                        $frequency = $data['frequency_id'];

                        $counselor = $data['counselor_id'];

                        $why_esperanza = $data['why_esperanza'];

                        $how_know_esperanza = $data['how_know_esperanza'];

                        $type_of_enquiry = $data['type_of_enquiry'];

                        $source_of_enquiry = $data['source_of_enquiry'];

                        $enquiry_mindset = $data['enquiry_mind_set'];

                        $enquiry_date = $data['enquiry_date'];

                        $occupation_id = $data['occupation_id'];

                        $company_id = $data['company_id'];

                        $reference = $data['reference'];



                        //if (!empty($search_type) || !empty($country) || !empty($state) || !empty($city) || !empty($location) ||!empty($branch) || !empty($from_date) || !empty($to_date) || !empty($academic_year) || !empty($program) || !empty($subprogram) || !empty($frequency) ||  !empty($counselor) || !empty($whyus)  || !empty($howdoyouknowesp) || !empty($type_of_enquiry)|| !empty($source_of_enquiry) || !empty($enquiry_mindset) || !empty($enquiry_date) ||  !empty($occupation_id) || !empty($company_id)  || !empty($reference)) {
                        //$data = $_POST;
                        //$how_know_esperanza = $this->getRequest()->getPost('how_know_esperanza');	
                        //$mindset = $this->getRequest()->getPost('enquiry_mind_set');
                        //$search_type = $this->getRequest()->getPost('search_type');
                        //print_r($search_type);die;
                        //$searchResult = $Report_model->getReportSearchRecords($how_know_esperanza,$mindset,$search_type);
                        $searchResult = $Report_model->getSearchRecords($search_type, $country, $state, $city, $location, $branch, $from_date, $to_date, $academic_year, $program, $subprogram, $frequency, $counselor, $why_esperanza, $how_know_esperanza, $type_of_enquiry, $source_of_enquiry, $enquiry_mindset, $enquiry_date, $occupation_id, $company_id, $reference);
                        $val = $Report_form->populate($data);

                        $this->view->searchResult = $searchResult;

                        //}

                        if (isset($export)) {
                            $exportResult = $Report_model->getExportSearchRecords($search_type, $country, $state, $city, $location, $branch, $from_date, $to_date, $academic_year, $program, $subprogram, $frequency, $counselor, $why_esperanza, $how_know_esperanza, $type_of_enquiry, $source_of_enquiry, $enquiry_mindset, $enquiry_date, $occupation_id, $company_id, $reference);

                            //echo "<pre>";print_r($exportResult);die;
                            $heading = array("Country", "State", "City", "Location", "Branch", "Counselor", "Academic Year", "Enquiry ID", "Enquiry Date & Time", "Next Followed Up Date", "Child Name", "Father Name", "Mother Name", "Fathers Mobile", "Mothers Mobile", "Fathers Email", "Mothers Email", "Subprogram", "Frequency", "Fathers Company", "Mothers Company", "Father Occupation Name", "Mother Occupation Name", "Type of Enquiry", "Source of Enquiry", "Enquiry Mindset", "Why Us", "How do you know about us", "Reference", "Comments");
                            $exceldata = $exportResult;


                            $this->_act->createExcel($heading, $exceldata, "Enquiry Details");
                        }
                    }
                } else {
                    $this->_redirect('report/report');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $this->view->Report_form = $Report_form;
                $result = $Report_model->getRecords();

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function getEnquiryVewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $id = $this->_getParam("id");
            if ($id) {
                $Enquiry_Model = new Application_Model_Enquiry();
                $EnquiryItems_model = new Application_Model_EnquiryItems();
                $result = $Enquiry_Model->getEnquiryInfoForReport($id);
                $EnquiryItems_info = $EnquiryItems_model->getItemRecord($result['enquiry_id']);
                //$fees_type=$Fees_type_model->getRecords();	

                $this->view->enquiry_info = $result;
                $this->view->item_info = $EnquiryItems_info;
                //$this->view->fees_type = $fees_type;
            }
        }
    }

    public function studentreportAction() {
        $this->view->action_name = 'studentreport';
        $this->view->sub_title_name = 'studentreport';
        $this->accessConfig->setAccess('SA_ACAD_FINAL_GRADE');
        $student_report_form = new Application_Form_StudentReport();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }

    public function studentTermReportAction() {
        $this->view->action_name = 'student-term-report';
        $this->view->sub_title_name = 'student-term-report';
        $this->accessConfig->setAccess('SA_ACAD_TERM_GRADE_SHEET');
        $student_report_form = new Application_Form_StudentReport();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }

    public function ajaxGetStudentNamesAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            if ($academic_year_id) {
                $StudentPortal_model = new Application_Model_StudentPortal();
                $student_data = $StudentPortal_model->getStudentNames($academic_year_id);
                //print_r($SubProgram);die;
                echo '<option value="">Select </option>';
                foreach ($student_data as $k => $val) {
                    echo '<option value="' . $k . '" >' . $val . '</option>';
                }
                die;
            }
        }
    }

    public function ajaxValidateDirectGradeAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $course_id = $this->_getParam("course_id");
            $student_id = $this->_getParam("student_id");
            if ($academic_year_id && $term_id && $course_id && $student_id) {
                $DirectFinalGrade_model = new Application_Model_DirectFinalGrade();
                echo $DirectFinalGrade_model->isGradeExist($academic_year_id, $term_id, $course_id, $student_id);

                die;
            }
        }
    }

    public function getStudentReportAction() {
        $this->_helper->layout->disableLayout();
        $studentreport_model = new Application_Model_StudentReport();
        $result = $studentreport_model->getRecords();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            $year_id = $this->_getParam("year_id");
            $term_id = $this->_getParam("term_id");
            $this->view->term_id = $term_id;
            $this->view->year_id = $year_id;
            $stu_id = $this->_getParam("stu_id");

            if ($academic_id) {
                $Studentreport_model = new Application_Model_StudentPortal();
                $result = $Studentreport_model->getStudentPCRecord($academic_id, $stu_id);
                $this->view->corecourseresult = $result;
            }
        }
    }

    public function studentreportPdfAction() {
        $this->view->close = '';
        $st_id = $this->_getParam("id");
        $academic_id = $this->_getParam("academic_id");
        $studentreport_model = new Application_Model_StudentReport();
        $result = $studentreport_model->getRecord($st_id);
        $this->view->result = $result;
        $Academic_model = new Application_Model_Academic();
        $academic_result = $Academic_model->getRecord($academic_id);
        $this->view->academic_result = $academic_result;

        $pdfheader = $this->view->render('report/pdfheader.phtml');
        $pdffooter = $this->view->render('report/pdffooter.phtml');
        $htmlcontent = $this->view->render('report/studentreport-pdf.phtml');
        $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Student Provisional Report");
    }

    public function studentgradesheetreportPdfAction() {
        $this->view->close = '';
        $stu_id = $this->_getParam("id");
        $this->view->stu_id = $stu_id;
        $academic_id = $this->_getParam("acd_id");
        $this->view->academic_id = $academic_id;
        $year_id = $this->_getParam("year");
        $this->view->year_id = $year_id;
        $CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
        $result = $CourseGradeAfterpenalties_model->getGradeSheetRecord($academic_id, $year_id, $stu_id);
        //echo'<pre>';print_r($result);die;
        $this->view->grade_result = $result;

        $Ele_result = $CourseGradeAfterpenalties_model->getGradeSheetElectivesRecord($academic_id, $year_id, $stu_id);
        //echo'<pre>';print_r($Ele_result);die;
        $this->view->Ele_result = $Ele_result;

        $studentreport_model = new Application_Model_StudentReport();
        $stu_result = $studentreport_model->getRecord($stu_id);
        $this->view->stu_result = $stu_result;

        $Academic_model = new Application_Model_Academic();
        $academic = $Academic_model->getRecord($academic_id);
        $this->view->academic_data = $academic;

        $ExprCourseGradeAftrPenalties_model = new Application_Model_ExprCourseGradeAftrPenalties();
        $ExprCourseGradeAftrPenaltyItems_model = new Application_Model_ExprCourseGradeAftrPenaltyItems();

        $ExperientialLearning_model = new Application_Model_ExperientialLearning();
        $Experiential_data = $ExperientialLearning_model->getExperRecords($academic_id, $year_id);
        $this->view->Experiential_data = $Experiential_data;

        // $ExprCourse_data = $ExprCourseGradeAftrPenalties_model->getGradeSheetRecords($academic_id,$year_id,$stu_id);
        //echo'<pre>';print_r($ExprCourse_data);die;
        // $this->view->ExprCourse_data = $ExprCourse_data;
        //$pdfheader = $this->view->render('report/pdfheader.phtml');
        //$pdffooter = $this->view->render('report/pdffooter.phtml');				
        $htmlcontent = $this->view->render('report/studentgradesheetreport-pdf.phtml');
        $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Student Report Details");
    }

    /* public function studentreportAction(){
      $this->view->action_name = 'studentreport';
      $this->view->sub_title_name = 'studentreport';
      $Studentreport_model = new Application_Model_StudentReport();
      $result = $Studentreport_model->getRecords();
      //	print_r($result);die;
      $type=$this->_getParam('type');
      //print_r($result);die;
      $messages = $this->_flashMessenger->getMessages();
      $this->view->messages = $messages;
      $result = $Studentreport_model->getRecords();
      $page = $this->_getParam('page', 1);

      $paginator_data = array(
      'page' => $page,
      'result' => $result
      );
      $this->view->paginator = $this->_act->pagination($paginator_data);
      switch($type){
      case 'print':
      $Studentreport_model = new Application_Model_StudentReport();
      $result = $Studentreport_model->getRecord();
      //print_r($result);die;
      $messages = $this->_flashMessenger->getMessages();
      $this->view->messages = $messages;
      $result = $Studentreport_model->getRecord();
      $page = $this->_getParam('page', 1);
      $paginator_data = array(
      'page' => $page,
      'result' => $result
      );
      $this->view->paginator = $this->_act->pagination($paginator_data);
      $this->view->result = $data;
      $pdfheader = $this->view->render('index/pdfheader.phtml');
      $pdffooter = $this->view->render('index/pdffooter.phtml');
      $htmlcontent = $this->view->render('hr/staff-report-pdf.phtml');
      //print_r($htmlcontent);die;
      $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Marketing Employee Report");
      }

      } */

    public function directFinalGradeAction() {
        $this->view->action_name = 'direct-final-grade';
        $this->view->sub_title_name = 'direct-final-grade';
        $this->accessConfig->setAccess('SA_ACAD_DIRECT_FINAL_GRADE');
        $DirectFinalGrade_model = new Application_Model_DirectFinalGrade();
        $Corecourselearning_form = new Application_Form_DirectFinalGrade();
        $ccl_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Corecourselearning_form;
        $cur_datetime = date('Y-m-d H:i:s');
        $user_id = $this->login_storage->id;
        $ip = $_SERVER['REMOTE_ADDR'];
        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($Corecourselearning_form->isValid($this->getRequest()->getPost())) {
                        $final_grade = $this->getRequest()->getPost('final_grade');
                        $data = $Corecourselearning_form->getValues();
                        $Corecourselearning_model = new Application_Model_Corecourselearning();
                        $course_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($data['academic_year_id'], $data['term_id'], $data['course_id']);
                        //print_r($data);exit;
                        $data['final_grade'] = $final_grade;
                        $data['credit_value'] = $course_detail['credit_value'];
                        $data['grade_credit_multiplied'] = $final_grade * $course_detail['credit_value'];
                        $data['added_on'] = $cur_datetime;
                        $data['added_by'] = $user_id;
                        $data['updated_on'] = $cur_datetime;
                        $data['updated_by'] = $user_id;
                        $data['added_by_ip'] = $ip;
                        $data['updated_by_ip'] = $ip;

                        $DirectFinalGrade_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('report/direct-final-grade');
                    }
                }
                break;
            case 'edit':
                $result = $DirectFinalGrade_model->getRecordDetail($ccl_id);
                $this->view->final_grade_detail = $result;
                if ($this->getRequest()->isPost()) {
                    $data['final_grade'] = $this->getRequest()->getPost('final_grade');
                    $Corecourselearning_model = new Application_Model_Corecourselearning();
                    $course_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($result['academic_year_id'], $result['term_id'], $result['course_id']);
                    $data['credit_value'] = $course_detail['credit_value'];
                    $data['grade_credit_multiplied'] = $final_grade * $course_detail['credit_value'];
                    $data['updated_on'] = $cur_datetime;
                    $data['updated_by'] = $user_id;
                    $data['updated_by_ip'] = $ip;
                    //print_r($data);echo $ccl_id;exit;
                    $DirectFinalGrade_model->update($data, array('id=?' => $ccl_id));

                    $this->_flashMessenger->addMessage('Details Updated Successfully');
                    $this->_redirect('report/direct-final-grade');
                }
                /*
                  $last_id = $ccl_id-1;
                  $last_record_result =$Corecourselearning_model->getRecord($last_id);
                  $this->view->last_result = $last_record_result;
                  $Corecourselearning_form->populate($result);
                  $this->view->result = $result;
                  $course_model = new Application_Model_Course();
                  $data = $course_model->getDropDownList();
                  $course_result = $course_model->getRecord($result['course_id']);
                  $data['']="Select ";
                  $data[$result['course_id']]= $course_result['course_name'];
                  ksort($data);
                  $Corecourselearning_form ->getElement("course_id")
                  ->setAttrib('readonly','readonly')
                  ->setAttrib('class',array('form-control'))
                  ->setMultiOptions($data);


                  if ($this->getRequest()->isPost()) {
                  if ($Corecourselearning_form->isValid($this->getRequest()->getPost())) {
                  $data = $Corecourselearning_form->getValues();
                  $data['re_credit'] = $this->getRequest()->getPost('re_credit');
                  $Corecourselearning_model->update($data, array('ccl_id=?' => $ccl_id));
                  $this->_flashMessenger->addMessage('Details Updated Successfully');
                  $this->_redirect('master/corecourselearning');
                  } else {
                  }
                  }
                 * */
                break;
            case 'delete':
                $data['deleted'] = 1;
                if ($ccl_id) {
                    $DirectFinalGrade_model->update($data, array('id=?' => $ccl_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('report/direct-final-grade');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $DirectFinalGrade_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function firstyeargradesheetreportAction() {
        $this->view->close = '';
        $stu_id = $this->_getParam("id");
        $academic_id = $this->_getParam("acd_id");
        $year_id = $this->_getParam("year");
        $term_id = $this->_getParam("term");
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;
        $mode = $this->_getParam("mode");

        if (empty($term_id)  && $mode != 'view') {//only create a gradesheet number if the gradesheet is not generating for only one term.
            //Genering New Number/Counter of Gradesheet to be added on top right corner of gradesheet
            $student_model1 = new Application_Model_StudentPortal();
            $student_detail1 = $student_model1->getRecord($stu_id);
            $GradeSheet_model = new Application_Model_GradeSheet();
            $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $student_detail1['stu_id']);
            $this->view->gradesheet_number = $gradesheet_number;
        }



        /**
         * If this student was discontinued in past, we have to fetch student's grades from more than one batches. Else, from only one batch
         *  
         */
        //If this student was discontinued in past
        $student_model = new Application_Model_StudentPortal();
        $stu_pre_details = $student_model->fetchDiscontinuedBatchesOfStudent($stu_id);
        //print_r($stu_pre_details);exit;
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

            ############################# [END] Fetching Core Courses and grades #################################### 

            $Academic_model = new Application_Model_Academic();
            $academic = $Academic_model->getRecord($student_first_batch['academic_id']);
            $this->view->academic_data = $academic;



            $this->view->stu_id = $student_first_batch['stu_id'];
            $this->view->academic_id = $student_first_batch['academic_id'];
            $this->view->term_result = $term_result;
            $this->view->stu_result = $student_first_batch;

            $this->view->expr_result = $exp_course_result;

            $htmlcontent = $this->view->render('report/firstyeargradesheetreport_discontinued_student.phtml');
            if($mode == 'view'){
                echo $htmlcontent;exit;
            }
            //$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Student First Year Grade Report //Details");	
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $student_first_batch['stu_fname'] . '-' . $academic['short_code'] . '-First Year Grade Report Details');
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


            $htmlcontent = $this->view->render('report/firstyeargradesheetreport.phtml');
            // $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Student First Year Grade Report //Details");	
            if($mode == 'view'){
                echo $htmlcontent;exit;
            }
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $stu_result['stu_fname'] . '-' . $academic['short_code'] . '-First Year Grade Report Details');
        }
    }

    public function secondyeargradesheetreportAction() {

        $this->view->close = '';
        $check = $_POST['username'];
        $stu_id = $this->_getParam("id");

        $academic_id = $this->_getParam("acd_id");

        $year_id = $this->_getParam("year");
        $mode = $this->_getParam("mode");
        $term_id = $this->_getParam("term");
        $this->view->term_id = $term_id;
        $this->view->year_id = $year_id;


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
            if ($check != 'admin' && empty($this->view->term_id) && $mode != 'view') {
                //Genering New Number/Counter of Gradesheet to be added on top right corner of gradesheet
                $student_model1 = new Application_Model_StudentPortal();
                $student_detail1 = $student_model1->getRecord($stu_id);
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $student_detail1['stu_id']);
                $this->view->gradesheet_number = $gradesheet_number;
            }
            $htmlcontent = $this->view->render('report/secondyeargradesheetreport_discontinued_student.phtml');

            if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }
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
            if ($check != 'admin' && empty($this->view->term_id) && $mode != 'view') {
                //Genering New Number/Counter of Gradesheet to be added on top right corner of gradesheet
                $student_model1 = new Application_Model_StudentPortal();
                $student_detail1 = $student_model1->getRecord($stu_id);
                $GradeSheet_model = new Application_Model_GradeSheet();
                $gradesheet_number = $GradeSheet_model->getGradeSheetNumber($academic_id, $year_id, $student_detail1['stu_id']);
                $this->view->gradesheet_number = $gradesheet_number;
            }
            $htmlcontent = $this->view->render('report/secondyeargradesheetreport.phtml');

            if ($check == 'admin' || $mode == 'view') {
                echo $htmlcontent;
                exit;
            }
            //$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Student Second Year Grade Report //Details");
            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, $stu_result['stu_fname'] . '-' . $academic['short_code'] . '- Second Year Grade Report Details');
        }
    }

    public function ajaxGetTodoListReportAction() {
        $empl_id = $this->_getParam('empl_id');
        $toDo_model = new Application_Model_ToDo();
        $not_started_in_percent = $in_progress_in_percent = $completed_in_percent = 0;


        if ($empl_id) {
            $not_started = $toDo_model->getTodoData($empl_id, 1);
            $completed = $toDo_model->getTodoData($empl_id, 3);
            $in_progress = $toDo_model->getTodoData($empl_id, 2);

            $total = $not_started + $completed + $in_progress;
            //coverting in percentage 
            $not_started_in_percent = round(($not_started / $total) * 100);
            $in_progress_in_percent = round(($in_progress / $total) * 100);
            $completed_in_percent = round(($completed / $total) * 100);
        } else {
            $not_started = $toDo_model->getTodoDataByStatus(1);
            $completed = $toDo_model->getTodoDataByStatus(3);
            $in_progress = $toDo_model->getTodoDataByStatus(2);
            $total = $not_started + $completed + $in_progress;
            $not_started_in_percent = round(($not_started / $total) * 100);
            $in_progress_in_percent = round(($in_progress / $total) * 100);
            $completed_in_percent = round(($completed / $total) * 100);
        }
        echo json_encode(array('in_progress' => $in_progress_in_percent,
            'not_started' => $not_started_in_percent,
            'completed' => $completed_in_percent,
            'not_started_in_no' => $not_started,
            'in_progress_in_no' => $in_progress,
            'completed_in_no' => $completed, 'total' => $total));

        exit;
    }

    public function studentCgpaReportAction() {
        $this->view->action_name = 'student-cgpa-report';
        $this->view->sub_title_name = 'Participants GPA/CGPA Report';
        $this->accessConfig->setAccess('SA_ACAD_CGPA_GPA');
        $student_report_form = new Application_Form_StudentReport();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $student_report_form;
    }

    private function filterGPA($grades, $exp_grades, $c_type, $term_id, $student_id, $is_gpa) {
        $rs = 0;
        if ($c_type == 'term') {//If current grade is term
            //echo $batch_id. $c_type. $term_id. $student_id. $is_gpa;exit;
            //print_r($grades);
            //print_r($exp_grades);exit;
            foreach ($grades as $row) {
                if (($term_id == $row['term_id']) && $student_id == $row['student_id']) {
                    if ($is_gpa) {
                        $rs = $row['final_grade'];
                    } else {
                        $rs = $row['cgpa'];
                    }
                    break;
                }
            }
        } elseif ($c_type == 'el') {

            foreach ($exp_grades as $row) {
                if (($term_id == $row['course_id']) && $student_id == $row['student_id']) {
                    if ($is_gpa) {
                        $rs = $row['final_grade_point'];
                    } else {
                        $rs = $row['cgpa'];
                    }
                    break;
                }
            }
        }
        return $rs;
    }

    public function ajaxStudentCgpaReportAction() {
        $this->_helper->layout->disableLayout();
        $academic_year_id = $this->_getParam("academic_id");
        $academic_model = new Application_Model_Academic();
        $batch_map = $academic_model->getAcademicDesignOrderByDate($academic_year_id);
        $this->view->batch_map = $batch_map;
        //print_r($batch_map);exit;

        $CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
        $grades = $CourseGradeAfterpenalties_model->getAllGradesByBatch($academic_year_id);

        $expGradeAllocation_model = new Application_Model_ExperientialGradeAllocation();
        $exp_grades = $expGradeAllocation_model->getAllGradesByBatch($academic_year_id);


        $StudentPortal_model = new Application_Model_StudentPortal();
        $students = $StudentPortal_model->getStudentsSortByName($academic_year_id);




        $student_grades = array();
        foreach ($students as $student) {
            $student_grade = array();
            //$student_grade['student_id'] = $student['student_id'];
            $student_grade['stu_id'] = $student['stu_id'];
            $student_grade['stu_name'] = $student['stu_fname'] . ' ' . $student['stu_lname'];
            foreach ($batch_map as $map) {//GPA
                $student_grade[$map['c_type'] . $map['id']] = $this->filterGPA($grades, $exp_grades, $map['c_type'], $map['id'], $student['student_id'], TRUE);
            }
            foreach ($batch_map as $map) {//CGPA
                $student_grade[$map['c_type'] . $map['id'] . 'cgpa'] = $this->filterGPA($grades, $exp_grades, $map['c_type'], $map['id'], $student['student_id'], FALSE);
            }
            $student_grades[] = $student_grade;
        }
        $this->view->student_grades = $student_grades;
    }

    public function ajaxGetRecentTermAndBatchAction() {

        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $my_date = $this->_getParam('my_date');
        $date = date_create($my_date);
        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnDate($my_date);

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


        echo json_encode($result['recent']);
        exit;
    }

// get batchscheduleSessions
    public function ajaxGetBatchSceduleSessionsAction() {
        $term_id = '';
        $term_id = $this->_getParam('term_id');
        $academic_year_id = $this->_getParam('batch_id');
        $limit = (int) $this->_getParam('top_id');
        $academic_name = '';
        $start_date = '';
        $end_date = '';
        $course_details = new Application_Model_Attendance();
        
        $term = new Application_Model_TermMaster();
        $result = $term->getTermOnDat1($term_id, $academic_year_id);


        $term_start = explode("/", $result[0]['start_date']);
        $term_end = explode("/", $result[0]['end_date']);
        $start = $term_start[2] . "-" . $term_start[1] . "-" . $term_start[0];
        $end = $term_end[2] . "-" . $term_end[1] . "-" . $term_end[0];

        //getting all the courses 

        $courses = $course_details->getCourseDetails($term_id, $academic_year_id);
        
        $version_id = $term->getMaxVersion(date('d-m-Y',strtotime($start)));

    //   print_r($start); exit;
        if (count(Courses) > 0) {
            $result['course_details'] = $this->getCourseNames($courses, $start, $end, $academic_year_id, $term_id, $version_id['version'], $limit);
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

    //function to get course names
    public function getCourseNames(array $courses, $start_date, $end_date, $batch_id, $term_id, $version, $limit = '3') {
        $details = new Application_Model_Attendance();
        $course_name = new Application_Model_TermMaster();
        $class_master = new Application_Model_ClassMaster();
        $result = array();
        $i = 0;
        $no_of_classes = $class_master->getRecordByTermIdAndBatch($term_id, $batch_id);
        //========[CLASS FIELD NAME FROM DATABASE ATTENDANCE]=========//
        $join_arr[] = 'class';
        $join_arr[] = 'faculty';
        foreach($join_arr as $key => $value){
            for($dcl = 1; $dcl <= $no_of_classes; $dcl++)
        $class_arr[] = $value."_$dcl";
        $faculty_arr[] =  $value."_$dcl";
        }
        $all_arr = array();
        foreach ($courses as $key) {
            $result[$key['course_id']] = $course_name->getCourseName($key['course_id']);
            $result1[$key['course']] = $details->getCourseCoordinator($key['course_id'], $batch, $term_id);
            foreach ($class_arr as $key1 => $value) {
                if ($details->getClass($value, $key['course_id']) > 0) {
                    $all_arr[$key['course_id']]['course_name'] = $result[$key['course_id']]['course_code'];
                    $all_arr[$key['course_id']]['DMI_Faculty'] += $details->getFacultyId($faculty_arr[$key1], 'EMP-F', $value, $result1[$key['course']]['employee_id'], $key['course_id']);
                    $all_arr[$key['course_id']]['Visiting_Faculty'] += $details->getFacultyId($faculty_arr[$key1], 'VF', $value, '', $key['course_id']);
                    $all_arr[$key['course_id']]['course_coodinator'] += $details->getFacultyId($faculty_arr[$key1], $result1[$key['course']]['employee_id'], $value, '', $key['course_id']);
                    $all_arr[$key['course_id']]['average'] = ((int) $all_arr[$key['course_id']]['DMI Faculty'] + (int) $all_arr[$key['course_id']]['Visiting Faculty']) + (int) $all_arr[$key['course_id']]['course_coodinator'] / 2;
                } else {
                    $all_arr[$key['course_id']]['course_name'] = $result[$key['course_id']]['course_code'];
                    $all_arr[$key['course_id']]['DMI_Faculty'] += 0;
                    $all_arr[$key['course_id']]['Visiting_Faculty'] += 0;
                    $all_arr[$key['course_id']]['course_coodinator'] += 0;
                }
            }
        }


        //==========[REPORT FOR PANALTIES]===========//
        $penalities_student = array();
        $Suffled_penalty_value = array();
        $result_sorted = array();
        $max_penalties_value = array();
        foreach ($courses as $key) {
            $result[$key['course_id']] = $course_name->getCourseName($key['course_id']);
            $penalties_student = $course_name->getPenalties($key['course_id'], $term_id);
            foreach ($penalties_student as $penalty_key => $penalty) {
                $temp = array_map('floatVal', explode(',', $penalty['academic_grades']));
                $Suffled_penalty_value[$key['course_id']][$penalty_key] = $temp[$penalty['courses_position'] - 1];
                asort($Suffled_penalty_value[$key['course_id']]);
                $Suffled_penalty_value[$key['course_id']] = array_reverse($Suffled_penalty_value[$key['course_id']]);
                $result_sorted[$key['course_id']]['top'] = array_slice(array_unique($Suffled_penalty_value[$key['course_id']]), 0, $limit);
                $result_sorted[$key['course_id']]['course_name'] = $result[$key['course_id']]['course_code'];
            }
        }
        // 
        
        foreach($result_sorted as $key  => $value){
           
            if(count($value['top']) < $limit)
            {
                for($i=count($value['top']); $i <= ((int)$limit - count($value['top'])); $i++){
                $result_sorted[$key]['top'][$i] = 0; 
                }
            }
            
        }
        
       // echo "<pre>"; print_r($result_sorted);exit;
        
        $result['faculty_sessions'] = $all_arr;
        $result['student_penalties'] = $result_sorted;
        return $result;
    }

    public function ajaxGetBatchSceduleSessions1Action() {
        $term_id = '';
        $term_id = $this->_getParam('term_id');
        $academic_year_id = $this->_getParam('batch_id');
        $empl_id = $this->_getParam('empl');
        $limit = (int) $this->_getParam('top_id');
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

        $courses = $course_details->getCourseDetails($term_id, $academic_year_id,$empl_id);
        //print_r($courses);exit;
        $version_id = $term->getMaxVersion(date_format($start, "d-m-Y"));


        if (count(Courses) > 0) {
            $result['course_details'] = $this->getCourseNames1($courses, date_format($start, "Y-m-d"), date_format($end, "Y-m-d"), $academic_year_id, $term_id, $version_id['version'], $limit);
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

    //function to get course names
    public function getCourseNames1(array $courses, $start_date, $end_date, $batch_id, $term_id, $version, $limit = '3') {

        $course_name = new Application_Model_TermMaster();
        $result = array();
        $i = 0;
        foreach ($courses as $key) {
            $result[$key['course_id']] = $course_name->getCourseName($key['course_id']);
        }
        
        foreach($result as $key => $value){
        $result['course_count'][$key] = (int)$course_name->getCourseReport1($value['course_code'],$batch_id, $term_id, $version);
        }
        
        return $result;
    }

    public function getCourseNameOnlyAction() {
        $course_id = $this->_getParam('course_id');
        $result[$course_id] = $course_name->getCourseName($course_id);
        echo json_encode($course_id);
        exit;
    }

    public function ajaxGetRatingInstructorAction() {
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

            $result['rating_five_no'] = $rating_five = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor,$course_id, 5);
            $result['rating_four_no'] = $rating_four = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor,$course_id, 4);
            $result['rating_three_no'] = $rating_three = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor,$course_id, 3);
            $result['rating_two_no'] = $rating_two = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor,$course_id, 2);
            $result['rating_one_no'] = $rating_one = $instructor_feed->getRatingCount($term_id, $batch_id, $instructor,$course_id, 1);
            
            $result['rating_five_label'] = ucfirst($student_feed_model->getRatingLabel(5));
            $result['rating_four_label'] = ucfirst($student_feed_model->getRatingLabel(4));
            $result['rating_three_label'] = ucfirst($student_feed_model->getRatingLabel(3));
            $result['rating_two_label'] = ucfirst($student_feed_model->getRatingLabel(2));
            $result['rating_one_label'] =  ucfirst($student_feed_model->getRatingLabel(1));

            //==[toal number of ratings]=======//
            $result['total_no'] = $total_rating = $rating_one + $rating_two + $rating_three + $rating_four + $rating_five;
            //=====[finding percentage]=======//
            $result['rating_five_percent'] = $rating_five_percent = round(($rating_five / $total_rating) * 100)."%";
            $result['rating_four_percent'] = $rating_four_percent = round(($rating_four / $total_rating) * 100)."%";
            $result['rating_three_percent'] = $rating_three_percent = round(($rating_three / $total_rating) * 100)."%";
            $result['rating_two_percent'] = $rating_two_percent = round(($rating_two / $total_rating) * 100)."%";
            $result['rating_one_percent'] = $rating_one_percent = round(($rating_one / $total_rating) * 100)."%";
          
            echo json_encode($result);
            exit;
        }
    }

    public function ajaxGetRatingAction() {
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


            $result['rating_five_no'] = $rating_five = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 5);
            $result['rating_four_no'] = $rating_four = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 4);
            $result['rating_three_no'] = $rating_three = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 3);
            $result['rating_two_no'] = $rating_two = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 2);
         
            $result['rating_one_no'] = $rating_one = $student_feed_model->getRatingCount($term_id, $batch_id, $course_id, 1);
            
            $result['rating_five_label'] = ucfirst($student_feed_model->getRatingLabel(5));
            $result['rating_four_label'] = ucfirst($student_feed_model->getRatingLabel(4));
            $result['rating_three_label'] = ucfirst($student_feed_model->getRatingLabel(3));
            $result['rating_two_label'] =  ucfirst($student_feed_model->getRatingLabel(2));
            $result['rating_one_label'] = ucfirst($student_feed_model->getRatingLabel(1));
            //==[toal number of ratings]=======//
            $result['total_no'] = $total_rating = $rating_one + $rating_two + $rating_three + $rating_four + $rating_five;
            //=====[finding percentage]=======//
            $result['rating_five_percent'] = $rating_five_percent = round(($rating_five / $total_rating) * 100)."%";
            $result['rating_four_percent'] = $rating_four_percent = round(($rating_four / $total_rating) * 100)."%";
            $result['rating_three_percent'] = $rating_three_percent = round(($rating_three / $total_rating) * 100)."%";
            $result['rating_two_percent'] = $rating_two_percent = round(($rating_two / $total_rating) * 100)."%";
            $result['rating_one_percent'] = $rating_one_percent = round(($rating_one / $total_rating) * 100)."%";
           
            echo json_encode($result);
            exit;
            $this->view->result = $result;
        }
    }
    
    
     public function ajaxGetCourseAction() {
        $course_details = new Application_Model_Attendance();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('academic_year_id');
            $result = $course_details->getCourseDetails($term_id, $batch_id);
            //  print_r($result);exit;
            foreach ($result as $value) {
                echo '<option value="' . $value['course_id'] . '" >' . $value['course_code'] . '</option>';
            }
        }die;
    }
    
    
     public function ajaxGetQuestionAction(){
                $ratings1 = new Application_Model_RatingMaster();
                
               $questions = new  Application_Model_Questionnaire();
               if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');
            $course_id = $this->_getParam('course');
            $empl_id = $this->_getParam('empl');
           
               $ratings = $ratings1->getRecords1();
               $result['question_array']=array();
               $result['feed_question'] = $questions->getAllQuestionByEmpl($batch_id, $term_id, $course_id, $empl_id);
            
               
                  foreach($ratings as $rating_key => $rating_value){
                        foreach($result['feed_question'] as $ques_key => $ques_value){
                            $question = $ques_value['question'];
                            $result['question_array'][$questions->getAllQuestionName($question)][0] = $questions->getAllQuestionName($question);
                            
                        }
                  }
           
                    foreach($ratings as $rating_key => $rating_value){
                        $feed = $rating_value['rating_value'];
                        foreach($result['feed_question'] as $ques_key => $ques_value){
                            $question = $ques_value['question'];
                            
                            $result['feed'][$ratings1->getRecordsByRatings($feed)][$questions->getAllQuestionName($question)] = $questions->getAllQuestionByEmplFeed($batch_id, $term_id, $course_id, $empl_id, $feed, $question);  
                            
                        }
                    }
                    
                    foreach($result['feed'] as $key => $value){
                        foreach($value as $key1 => $value1){
                            foreach($result['question_array'] as $ques_key => $ques_value){
                                if($key1 === $ques_key){
                                    $result['question_array'][$ques_key][count($result['question_array'][$ques_key])] =  $value1;
                                }
                            }
                        }
                       
                    }
                    
                    
                    foreach($result['question_array'] as $key => $value){
                        foreach($value as $key1 => $value1){
                            if($key1==count($result['question_array'][$key])-1)
                                $result['question_array'][$key][count($result['question_array'][$key])] = '';
                        }
                    }
                    
                 //   echo "<pre>";print_r($result['question_array']);exit;
               $result['all_question'] = $questions->getAllQuestionByQuestionType(2);
              echo json_encode($result);exit;
               }
    }
    
  
    public function ajaxGetFacultyAction() {
        $employee_model = new Application_Model_HRMModel();
        $faculty = new Application_Model_Attendance();
        $class_master = new Application_Model_ClassMaster();
        $term_id = $this->_getParam('term_id');
        $batch_id = $this->_getParam('academic_year_id');
        $course_id = $this->_getParam('course_id');
        /*$result = $faculty->getFaculty($term_id, $batch_id, $course_id);*/
        $no_of_classes = $class_master->getRecordByTermIdAndBatch($term_id, $batch_id);
        
       
        $result = $faculty->getFeedFaculty($term_id, $batch_id, $course_id, $no_of_classes);
       $index = 0;
        $facultyFeed = array();
        foreach($result as $key => $value){
            foreach($value as $new_key => $new_value){
                if($new_value==$course_id){
                        $fac = explode('_',$new_key);
                        $facultyFeed[$index] = $value['faculty_'.$fac[1]];
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
        
 
    
    

  /*=======start 07-01-2018 public function ajaxGetFacultyAction() {
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

        if (count($empl_name) > 0) {
            foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
            }die;
        }

        die;
    }*///=======End 07-01-2018 by ashutosh 

}
