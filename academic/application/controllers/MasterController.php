<?php

/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 * 	Authors Kannan and Rajkumar
 */
class MasterController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    private $_auth_id = null;
    private $_role_id = null;
    private $accessConfig = null;

    public function init() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();
        
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $this->_action = $this->getRequest()->getActionName();
        //access role id
        $config_role = $zendConfig->role_administrator->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
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
        $uploadPaths = realpath(APPLICATION_PATH . '/../public/') . '/';
        $this->view->validator = new Zend_Validate_File_Exists();
        $this->view->validator->addDirectory($uploadPaths);

        //$this->_auth_id = $this->_act->auth_id();
        //$this->_role_id = $this->_act->role_id();
        //$this->view->role_id = $this->_act->role_id();
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        //print_r($data); die;
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

    /* 	public function indexAction() { 

      $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
      $config = $zendConfig->mainconfig->toArray();
      $this->view->mainconfig = $config;

      } */

    //Credit Master
    public function creditAction() {
        $this->view->action_name = 'masterdata-addmission';
        $this->view->sub_title_name = 'credit';
        $Credit_model = new Application_Model_Credit();
        $Credit_form = new Application_Form_Credit();
        $credit_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Credit_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($Credit_form->isValid($this->getRequest()->getPost())) {
                        $data = $Credit_form->getValues();
                        //print_r($data);die;
                        $Credit_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/credit');
                    }
                }
                break;
            case 'edit':
                $result = $Credit_model->getRecord($credit_id);

                $Credit_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($Credit_form->isValid($this->getRequest()->getPost())) {
                        $data = $Credit_form->getValues();

                        $Credit_model->update($data, array('credit_id=?' => $credit_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/credit');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($credit_id) {
                    $Credit_model->update($data, array('credit_id=?' => $credit_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/credit');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Credit_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
    
    
 
    
public function sectionAction(){
          $this->view->action_name = 'sectionmaster';
        $this->view->sub_title_name = 'sectionmaster';
        $this->accessConfig->setAccess("SA_ACAD_SECTION");
        $section_model = new Application_Model_Section();
        $section_form = new Application_Form_Section();
        $id = $this->_getParam("id");
       
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $section_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($section_form->isValid($this->getRequest()->getPost())) {
                        $data = $section_form->getValues();
                        //print_r($data);die;
                        $section_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/section');
                    }
                }
                break;
            case 'edit':
                $result = $section_model->getRecord($id);
                
                $section_form->removeElement('term_id');
                    $term_model = new Application_Model_TermMaster();
                    $data = $term_model->getDropDownListAcademic($result['academic_year_id']);
                    $term_id = $section_form->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
                $section_form->addElement($term_id);
                
                
                $section_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($section_form->isValid($this->getRequest()->getPost())) {
                        $data = $section_form->getValues();
                        $section_model->update($data, array('id=?' => $id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/section');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($cc_id) {
                    $section_model->update($data, array('id=?' => $id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/section');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $section_model->getRecords();

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
}
    
    
    public function coursefeeAction() {
        $this->view->action_name = 'coursefee';
        $this->view->sub_title_name = 'coursefee';
       $this->accessConfig->setAccess('SA_ACAD_COURSE_FEE');
        $assignment_model = new Application_Model_Application();
        $assignment_form = new Application_Form_Coursefee();
        $student_assignment_model = new Application_Model_SubmitAssignment();
        $course_fee_model = new Application_Model_Coursefee();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $assignment_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {


                        $courseb = implode(',', $this->getRequest()->getPost('coursebBox'));
                        $course = implode(',', $this->getRequest()->getPost('courseBox'));
                        $date = str_replace('/', '-', $this->getRequest()->getPost('dob_id'));
                            $paper = $this->getRequest()->getPost('paper');
                            $course = !empty($course)?','.$course.',':null;
                            
                        $data = array(
                            'batch' => $this->getRequest()->getPost('batch'),
                            
                            'fee' => $this->getRequest()->getPost('fee'),
                            'course' => $course ,
                            'paper' => $paper,
                            'term' => $this->getRequest()->getPost('term'),
                           // 'updated_date' => date('Y-m-d')
                        );
                        $result =  $course_fee_model->isRecordExists($data['batch'], $data['term'], $data['paper']);
                        if($result == 0){
                        $last_insert_id = $course_fee_model->insert($data);
                        $_SESSION['message_class']='alert-success';
                        $message = 'Application form has successfully submitted';
                        }
                        else {
                                    $_SESSION['message_class']='alert-danger';
                                    $message ='Fee  already exists';
                       }


                        $this->_flashMessenger->addMessage($message);

                        $this->_redirect('master/coursefee');
                    }
                }


                break;
            case 'edit':
                $result = $course_fee_model->getRecord($ec_id);
                $_SESSION['application']['course_id'] = $result['course'];
                $assignment_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {
                       //  $courseb = implode(',', $this->getRequest()->getPost('coursebBox'));
                        $course = implode(',', $this->getRequest()->getPost('courseBox'));
                        $date = str_replace('/', '-', $this->getRequest()->getPost('dob_id'));
                            $paper = $this->getRequest()->getPost('paper');
                           
                            $course = !empty($course)?','.$course.',':null;
                          
                        $data = array(       
                            'batch' => $this->getRequest()->getPost('batch'),
                            
                            'fee' => $this->getRequest()->getPost('fee'),
                            'course' => $course ,
                            'paper' => $paper,
                            'term' => $this->getRequest()->getPost('term'),
                           // 'updated_date' => date('Y-m-d')
                        );
                        $course_fee_model->update($data, array('id=?' => $ec_id));
                    }
                    $_SESSION['message_class']='alert-success';
                    $this->_flashMessenger->addMessage('Application Form has Successfully updated !');
                    $this->_redirect('master/coursefee');
                }



                break;
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('application/index');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result1 = array();
                $result = $course_fee_model->getRecords();
                
                $i = 0;
                foreach ($result as $key=> $value){
                    
          
                    $result[$key]['batch'] = $assignment_model->getAcademic1($value['batch']);
                    $result[$key]['term'] = $assignment_model->getTerm($value['term']);
                    $i++;
             
                }
           
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->subtable = $result ;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }    
    
    
    

    //Course Category Master
    public function coursecategoryAction() {
        $this->view->action_name = 'coursecategory';
        $this->view->sub_title_name = 'coursecategory';
        $this->accessConfig->setAccess('SA_ACAD_COURSE_CAT');
        $Coursecategory_model = new Application_Model_Coursecategory();
        $Coursecategory_form = new Application_Form_Coursecategory();
        $cc_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Coursecategory_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($Coursecategory_form->isValid($this->getRequest()->getPost())) {
                        $data = $Coursecategory_form->getValues();
                        //print_r($data);die;
                        $Coursecategory_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/coursecategory');
                    }
                }
                break;
            case 'edit':
                $result = $Coursecategory_model->getRecord($cc_id);
                $Coursecategory_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($Coursecategory_form->isValid($this->getRequest()->getPost())) {
                        $data = $Coursecategory_form->getValues();

                        $Coursecategory_model->update($data, array('cc_id=?' => $cc_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/coursecategory');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($cc_id) {
                    $Coursecategory_model->update($data, array('cc_id=?' => $cc_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/coursecategory');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Coursecategory_model->getRecords();

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
    public function roommasterAction() {
        $this->view->action_name = 'roommaster';
        $this->view->sub_title_name = 'roommaster';
        $Coursecategory_model = new Application_Model_Coursecategory();
        $Coursecategory_form = new Application_Form_Coursecategory();
        $cc_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Coursecategory_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($Coursecategory_form->isValid($this->getRequest()->getPost())) {
                        $data = $Coursecategory_form->getValues();
                        //print_r($data);die;
                        $Coursecategory_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/coursecategory');
                    }
                }
                break;
            case 'edit':
                $result = $Coursecategory_model->getRecord($cc_id);
                $Coursecategory_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($Coursecategory_form->isValid($this->getRequest()->getPost())) {
                        $data = $Coursecategory_form->getValues();

                        $Coursecategory_model->update($data, array('cc_id=?' => $cc_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/coursecategory');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($cc_id) {
                    $Coursecategory_model->update($data, array('cc_id=?' => $cc_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/coursecategory');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Coursecategory_model->getRecords();

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function getCourseCategoryAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $Coursecategory_model = new Application_Model_Coursecategory();
            $course_name = $this->_getParam("course_name");
            //print_r($course_name);die;
            $result = $Coursecategory_model->getCourseCategory($course_name);
            print_r($result);
            die;
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
     // $faculty_rr = array_merge($faculty_arr);
   
     
    // print_r($result[0]['employee_id']);exit;
        //====={MAKING ARRAY UNIQUE}==============//
    $faculty_arr[count($faculty_arr)] =  $result[0]['employee_id']; 
    
     $all_unique_faculty = array_unique($faculty_arr);
 
 //print_r($all_unique_faculty);exit;
 $i = 0;
 foreach($all_unique_faculty as $key => $value){
     
     if($value!='NA'){
         if($value)
              $empl_name[$i] = $employee_model->getAllEmployee1($value)[0];
     }
     $i++;
     
 }
     //======[GETTING NAME OF AL THE EMPLYOEE]=======//
    
      //   print_r($empl_name);exit;
     //=========[SETTING SELECT BOX]=========//
       
    //  echo '<option value="'.$_SESSION['admin_login']['admin_login']->empl_id.'">'.$_SESSION['admin_login']['admin_login']->real_name.'</option>';
      
        if (count($empl_name) > 0) {
            foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
            }die;
        }
     
     die;
    
}
   

public function ajaxAddTimeAction(){
     $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            
            $time = $this->_getParam('time');
            $hour = $this->_getParam('minutes');
            $time = date('H:i',strtotime($time));
            $time = date('h:i A',strtotime("+$hour minutes", strtotime($time)));
            echo $time ;die;
        }
}



 public function classmasterAction(){
        $this->view->action_name = 'classmaster';
        $this->view->sub_title_name = 'classmaster';
        $this->accessConfig->setAccess('SA_ACAD_CLASS');
        $ClassMaster_model = new Application_Model_ClassMaster();
        $CLassMaster_form = new Application_Form_ClassMaster();
        $class_id = $this->_getParam("id");
        $type = $this->_getParam("type");
       
        $this->view->type = $type;
        $this->view->form = $CLassMaster_form;

        switch ($type) {
            case "add":
                $messages = $this->_flashMessenger->getMessages();
                
               
                if (is_array($_POST)) {
                    if (count($_POST['class_name'])>0) {
                            $class_name = $_POST['class_name'];
                            $status = $_POST['status'];
                            $time = $_POST['time'];
                            $hours = $_POST['hours'];
                           $data['academic_year_id'] =0;
                           $data['term_id'] =0;
                           unset($data['ccl_id']);
                           for($i = 0; $i< count($class_name); $i++){
                            $data['class_name'] = $class_name[$i];
                            $data['time'] = $time[$i];
                            $data['status'] = $status[$i];
                            $data['hours'] = $hours[$i];
                            $ClassMaster_model->insert($data);
                           }
                         
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/classmaster');
                       
                        
                    }
                }
                break;
            case 'edit':
                
                         $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ClassMaster_model->getRecordByTermID(0);

              
                $this->view->classes = $result;
             


                if (is_array($_POST)) {
                    if (count($_POST['class_name'])>0) {
                       unset($_POST['count_val']);
                      // echo "<pre>";print_r($_POST); exit;
                       $data = array();
                      
                           foreach($_POST as $field_name => $field_arr){
                               foreach($field_arr as $key => $value){
                                   $data[$key][$field_name] =$value;
                                   $data[$key]['class_id'] = $key;
                                   $data[$key]['academic_year_id'] = 0;
                                   $data[$key]['term_id'] = 0;
                               }
                               
                           }
                           unset($data['credit_val']);
                     foreach($data as $key => $value){
                          $result = $ClassMaster_model->getRecord($value['class_id']);
                          unset($value['electives']);
                          $class_id = $value['class_id'];
                          if(!empty($result)){
                              unset($value['class_id']);
                            $ClassMaster_model->update($value, array('class_id=?' => $class_id));
                          }
                          else{
                              $ClassMaster_model->insert($value);
                          }
                     }
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                       
                        $this->_redirect('master/classmaster/type/edit');
                    } else {
                        
                    }
                }
                break;
        
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ClassMaster_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }








//Core Course Learning 
    public function corecourselearningAction(){
        $this->view->action_name = 'corecourselearning';
        $this->view->sub_title_name = 'corecourselearning';
        $this->accessConfig->setAccess('SA_ACAD_CORE_COURSE');
        $Corecourselearning_model = new Application_Model_Corecourselearning();
        $Corecourselearning_form = new Application_Form_Corecourselearning();
        $ccl_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Corecourselearning_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($Corecourselearning_form->isValid($this->getRequest()->getPost())) {
                        $data = $Corecourselearning_form->getValues();
                        $data['re_credit'] = $this->getRequest()->getPost('re_credit');
                        $Corecourselearning_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/corecourselearning');
                    }
                }
                break;
            case 'edit':
                $result = $Corecourselearning_model->getRecord($ccl_id);

                $last_id = $ccl_id - 1;
                $last_record_result = $Corecourselearning_model->getRecord($last_id);
                	$Term_model = new Application_Model_TermMaster();
		$data = $Term_model->getTermDropDownList($last_record_result['academic_year_id']);
		//print_r($data); die;
                $Corecourselearning_form->getElement('term_id')->setAttrib('style',array("display","initial"));
                $employee_id = $Corecourselearning_form->createElement('select', 'term_id');
                $employee_id->setAttrib('class', array('form-control', 'chosen-select'));
                $employee_id->setAttrib('required','required');
                //$employee_id->removeDecorator("htmlTag");
                $employee_id->addMultiOptions(array('' => 'Select'));
                $employee_id->setRegisterInArrayValidator(false);
                $employee_id->addMultiOptions($data);
                $Corecourselearning_form->addElement($employee_id);
        
                $this->view->last_result = $last_record_result;
                $Corecourselearning_form->populate($result);
                $this->view->result = $result;
                $course_model = new Application_Model_Course();
                $data = $course_model->getDropDownList();
                $course_result = $course_model->getRecord($result['course_id']);
                $data[''] = "Select ";
                $data[$result['course_id']] = $course_result['course_name'];
               // ksort($data);
                $Corecourselearning_form->getElement("course_id")
                        ->setAttrib('readonly', 'readonly')
                        ->setAttrib('class', array('form-control'))
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
                break;
            case 'delete':
                $data['status'] = 2;
                if ($ccl_id) {
                    $Corecourselearning_model->update($data, array('ccl_id=?' => $ccl_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/corecourselearning');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Corecourselearning_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

//ajax term name
    public function ajaxGetTermNameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            // print_r($academic_year_id); die;
            // $Corecourselearning_model= new Application_Model_Corecourselearning();
            $TermMaster_model = new Application_Model_TermMaster();

            $result = $TermMaster_model->getCoreCourseTerms($academic_year_id);

            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }die;
    }
//ajax academic according to department
    public function ajaxGetAcademicwithdeptAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $department = $this->_getParam("department");
            //echo $department; die;
            $academic_model = new Application_Model_Academic();
           
            $result = $academic_model->getAcademicOnDept($department);
        //   echo "<pre>";print_r($result);exit;
            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {
                echo '<option value="' . $val['academic_year_id'] . '" >' . $val['batch_code'] . '</option>';
            }
        }die;
    }

    public function ajaxGetBatchStudentsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            // print_r($academic_year_id); die;
            // $Corecourselearning_model= new Application_Model_Corecourselearning();
            $student_model = new Application_Model_StudentPortal();

            $result = $student_model->getstudentsdetails($academic_year_id);

            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $val['student_id'] . '" >' . $val['students'] . '</option>';
            }
        }die;
    }

    //ajax elective term name
    public function ajaxGetElectiveTermNameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            // print_r($academic_year_id); die;
            // $Corecourselearning_model= new Application_Model_Corecourselearning();
            $TermMaster_model = new Application_Model_TermMaster();

            $result = $TermMaster_model->getCoreCourseTerms($academic_year_id);

            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }die;
    }

    public function ajaxGetTermAcademicAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");

            $term_id = $this->_getParam("term_id");

            $ccl_id = $this->_getParam("ccl_id");

            $TermMaster_model = new Application_Model_TermMaster();


            //if(!empty($ccl_id))
            //{
            // $result= $TermMaster_model->getRemainingTermCredits($academic_year_id,$term_id,$ccl_id);
            //}else{

            $result = $TermMaster_model->getTermCredits($academic_year_id, $term_id);
            //}

            echo json_encode($result);
            die;
        }
    }

    //CORE COURSE LEARNING VIEW

    public function corecourselearningviewAction() {
        $this->view->action_name = 'corecourselearningview';
        $this->view->sub_title_name = 'corecourselearningview';
        $this->accessConfig->setAccess("SA_ACAD_CORE_COURSE_LEARN");
        $ccl_view_form = new Application_Form_CoreCourseLearningView();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ccl_view_form;
    }

    //CORE COURSE LEARNING VIEW
    //ELECTIVE COURSE LEARNING VIEW

    public function electivecourselearningviewAction() {
        $this->view->action_name = 'electivecourselearningview';
        $this->view->sub_title_name = 'electivecourselearningview';
        $ElectiveCourseLearningView_form = new Application_Form_ElectiveCourseLearningView();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ElectiveCourseLearningView_form;
    }

    //CORE COURSE LEARNING VIEW

    public function gradeallocationviewAction() {
        $this->view->action_name = 'gradealloctionview';
        $this->view->sub_title_name = 'gradealloctionview';
        $ccl_view_form = new Application_Form_GradeAllocationView();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ccl_view_form;
    }

    // PROGRAM DESIGN VIEW AJAX
    public function getGradeAllocationViewAction() {

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $department_id = $this->_getParam("department_id");
            $academic_year_id = $this->_getParam("academic_year_id");


            if ($department_id) {

                $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
                $result = $EmployeeAllotment_model->getEmployeeTerms($academic_year_id, $department_id);
                //	print_r($result);die;
                $this->view->corecourseresult = $result;
            }
        }
    }

    // PROGRAM DESIGN VIEW AJAX
    public function getCoreCourseLearningViewAction() {

        $this->_helper->layout->disableLayout();
        //  $Corecourselearning_model = new Application_Model_Corecourselearning();
        //$result = $Corecourselearning_model->getRecords();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_year_id = $this->_getParam("academic_year_id");
            //print_r($academic_year_id);
            $term_id = $this->_getParam("term_id");
            //print_r($term_id);die;

            if ($academic_year_id) {
                $Corecourselearning_model = new Application_Model_Corecourselearning();
                $result = $Corecourselearning_model->getcorecourselearning($academic_year_id, $term_id);
                //print_r($result);die;
                $this->view->corecourseresult = $result;
            }
        }
    }

    //Course Type Master

    public function coursetypeAction() {
        $this->view->action_name = 'coursetype';
        $this->view->sub_title_name = 'coursetype';
        $this->accessConfig->setAccess('SA_ACAD_COURSE_TYPE');
        $Coursetype_model = new Application_Model_Coursetype();
        $Coursetype_form = new Application_Form_Coursetype();
        $ct_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Coursetype_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($Coursetype_form->isValid($this->getRequest()->getPost())) {
                        $data = $Coursetype_form->getValues();
                        //print_r($data);die;
                        $Coursetype_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/coursetype');
                    }
                }
                break;
            case 'edit':
                $result = $Coursetype_model->getRecord($ct_id);
                $Coursetype_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($Coursetype_form->isValid($this->getRequest()->getPost())) {
                        $data = $Coursetype_form->getValues();

                        $Coursetype_model->update($data, array('ct_id=?' => $ct_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/coursetype');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($ct_id) {
                    $Coursetype_model->update($data, array('ct_id=?' => $ct_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/coursetype');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Coursetype_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //Program Master

    public function programmasterAction() {
        $this->view->action_name = 'Program Master';
        $this->view->sub_title_name = 'Program Master';
        $this->accessConfig->setAccess('SA_ACAD_PROG_DESIGN');
        $ProgramMaster_model = new Application_Model_ProgramMaster();
        $ProgramMaster_form = new Application_Form_ProgramMaster();
        $pm_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ProgramMaster_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($ProgramMaster_form->isValid($this->getRequest()->getPost())) {
                        $data = $ProgramMaster_form->getValues();
                        //print_r($data);die;
                        $ProgramMaster_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/programmaster');
                    }
                }
                break;
            case 'edit':
                $result = $ProgramMaster_model->getRecord($pm_id);
                $ProgramMaster_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($ProgramMaster_form->isValid($this->getRequest()->getPost())) {
                        $data = $ProgramMaster_form->getValues();

                        $ProgramMaster_model->update($data, array('pm_id=?' => $pm_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/programmaster');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($pm_id) {
                    $ProgramMaster_model->update($data, array('pm_id=?' => $pm_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/programmaster');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ProgramMaster_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //Course Type Same Name Action

    public function getCoursetypeSameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $coursetype_model = new Application_Model_Coursetype();
            $coursetype = $this->_getParam("coursetype");
            $result = $coursetype_model->getcoursetype($coursetype);
            print_r($result);
            die;
        }
    }

    //Academic Master
    public function academicAction() {
        $this->view->action_name = 'Academic';
        $this->view->sub_title_name = 'Academic';
        $this->accessConfig->setAccess('SA_ACAD_BATCH');
        $Academic_model = new Application_Model_Academic();

        $Academic_form = new Application_Form_Academic();
        $academic_year_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Academic_form;
        $this->view->increment_id = $Academic_model->getIncrementID();
        $this->view->Academic_form = $Academic_form;


        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($Academic_form->isValid($this->getRequest()->getPost())) {
                        $data = $Academic_form->getValues();
                        $academic_year_id = $data['from_date'] . ' ' . '-' . ' ' . $data['to_date'];
                        //	print_r($data);die;
                        if ($academic_year_id) {
                            $Academic_model = new Application_Model_Academic();
                            $academic_data = $Academic_model->getValidateAcademic($academic_year_id);
                            $this->view->academic_data = $academic_data;

                            /* if(!empty($academic_data)){

                              $this->_flashMessenger->addMessage('This Academic Year is Already Existed');
                              $this->_redirect('master/academic');
                              }
                              else{ */
                            $Academic_model->insert($data);
                            $this->_flashMessenger->addMessage('Details Added Successfully ');
                            $this->_redirect('master/academic');
                        }
                    }
                }
                break;
            case 'edit':
                $result = $Academic_model->getRecord($academic_year_id);
                $Academic_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($Academic_form->isValid($this->getRequest()->getPost())) {
                        $data = $Academic_form->getValues();

                        $Academic_model->update($data, array('academic_year_id=?' => $academic_year_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/academic');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($academic_year_id) {
                    $Academic_model->update($data, array('academic_year_id=?' => $academic_year_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/academic');
                }
                break;
            default:

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Academic_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    // PROGRAM DESIGN MASTER
    public function programdesignAction() {
        
        
        $this->view->action_name = 'ProgramDesign';
        $this->view->sub_title_name = 'ProgramDesign';
        $this->accessConfig->setAccess('SA_ACAD_PROG_CAL_YEAR');
        $programdesign_model = new Application_Model_ProgramDesign();
        $programdesign_form = new Application_Form_ProgramDesign();
        $pd_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $programdesign_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($programdesign_form->isValid($this->getRequest()->getPost())) {
                        $data = $programdesign_form->getValues();
//print_r($data);die;			
                        $data['academic_year_id'] = $data['short_code'];
                       // print_r($data['academic_year_id']);die;                      
                      //Edited by satyam 25-04-2019
                        $data['no_days'] = $this->getRequest()->getPost('no_days');
                        $data['start_date'] = $this->getRequest()->getPost('start_date');
                        
                        $data['end_date'] = $this->getRequest()->getPost('end_date');
                        //print_r($data['start_date'].$data['end_date']); die;
                       // echo "<pre>"; print_r($data); 
//                         echo $v1; exit;
                        $data['no_weeks'] = $this->getRequest()->getPost('no_weeks');
                        $result = $programdesign_model->getRecords();
                        //print_r($data['start_date'].$data['end_date']); echo "<br>";
                        $startdate = $data['start_date'];
                      //  echo "<pre>"; print_r($result[0]['start_date']); 
                        
                            $i=0;
                            while ($i<10)
                            { 
                               
                                if (in_array($startdate, $result[$i++]))
                                  {
                                 // echo "Match found";
                                 // die;
                                  }
                                  else
                                  {
                                   // echo "match not found";
                                    //die;
                                  }
                                                   
                            }
                        // die;
                        
                        
                        
                        
                        $programdesign_model->insert($data);

                        $this->_flashMessenger->addMessage('Program Design Successfully added');

                        $this->_redirect('master/programdesign');
                    }
                }

                break;
            case 'edit':
                $result = $programdesign_model->getRecord($pd_id);
                $programdesign_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($programdesign_form->isValid($this->getRequest()->getPost())) {
                        $data = $programdesign_form->getValues();
                        $data['academic_year_id'] = $data['short_code'];
                        $data['no_days'] = $this->getRequest()->getPost('no_days');
                        $data['no_weeks'] = $this->getRequest()->getPost('no_weeks');

                        $programdesign_model->update($data, array('pd_id=?' => $pd_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/programdesign');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($pd_id) {
                    $programdesign_model->update($data, array('pd_id=?' => $pd_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/programdesign');
                }
                break;
            default:
                
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $programdesign_model->getRecords();
                //==================================Edited by satyam 25-04-2019=========================
//                echo '<pre>'; print_r($result);die;
                $i=0;
                while ($i<10)
                {
               // echo '<pre>';
                GLOBAL $v1; 
                $v1=$result[$i]['academic_year_id'];
                $v2=$result[$i]['start_date'];
                //echo $v1. $v2;
                $i++;                
                }// die;
               // echo $v1; exit;
                
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    // BATCH MASTER
    public function batchAction() {
        $this->view->action_name = 'batch';
        $this->view->sub_title_name = 'batch';
        $batch_model = new Application_Model_Batch();
        $batch_form = new Application_Form_Batch();
        $batch_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $batch_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($batch_form->isValid($this->getRequest()->getPost())) {
                        $data = $batch_form->getValues();
                        $batch_no = $data['batch_no'];
                        if ($batch_no) {
                            $Batch_model = new Application_Model_Batch();
                            $group_data = $Batch_model->getValidateBatchNo($batch_no);
                            $this->view->group_data = $group_data;
                            if (!empty($group_data)) {
                                $this->_flashMessenger->addMessage('This Batch Number is Already Existed');
                                $this->_redirect('master/batch');
                            } else {
                                $batch_model->insert($data);

                                $this->_flashMessenger->addMessage('Batch Master Successfully added');

                                $this->_redirect('master/batch');
                            }
                        }
                    }
                }

                break;
            case 'edit':
                $result = $batch_model->getRecord($batch_id);
                $batch_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($batch_form->isValid($this->getRequest()->getPost())) {
                        $data = $batch_form->getValues();

                        $batch_model->update($data, array('batch_id=?' => $batch_id));
                        $this->_flashMessenger->addMessage('Batch Master Updated Successfully');
                        $this->_redirect('master/batch');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($batch_id) {
                    $batch_model->update($data, array('batch_id=?' => $batch_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/batch');
                }
                break;
            default:

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $batch_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break; 
        }
    }
    public function ajaxGetStateAction()  {
        $placement = new Application_Model_placement();
         $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $country_id = $this->_getParam("country_id");
            $result =$placement->getState($country_id);
            echo "<option>--Select--</option>";
            foreach($result as  $value){
                 echo "<option value ='".$value['id']."'>".$value['name']."</option>";
            }die;
        }
    }
     public function ajaxGetCityAction()  {
        $placement = new Application_Model_placement();
         $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $state_id = $this->_getParam("state_id");
            $result =$placement->getCity($state_id);
            echo "<option>--Select--</option>";
            foreach($result as $key => $value){
                 echo "<option value ='".$value['id']."'>".$value['name']."</option>";
            }die;
        }
    }
     public function placementRegisterationFormAction() {

        $this->view->action_name = 'placement';
        $this->view->sub_title_name = 'placement';
        $this->accessConfig->setAccess('SA_ACAD_MASTER_PLACEMENT');
        $placement_model = new Application_Model_placement();
        $placement_form = new Application_Form_placement();

        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $placement_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($placement_form->isValid($this->getRequest()->getPost())) {
                        $data = $placement_form->getValues();

                        $id = $data['registration_id'];
                        if ($id) {

                          
                            $group_data = $placement_model->getValidateRegistrationNo($id);
                            $this->view->group_data = $group_data;
                            if (!empty($group_data)) {
                                $this->_flashMessenger->addMessage('This Registeration Number is Already Existed');
                                $this->_redirect('master/placement-registeration-form');
                            } else {
                                $placement_model->insert($data);

                                $this->_flashMessenger->addMessage('Details Added Successfully');

                                $this->_redirect('master/placement-registeration-form');
                            }
                        }
                    }
                }

                break;
            case 'edit':
                $result = $placement_model->getRecord($id);
                $placement_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($placement_form->isValid($this->getRequest()->getPost())) {
                        $data = $placement_form->getValues();

                        $placement_model->update($data, array('id=?' => $id));
                        $this->_flashMessenger->addMessage('Registeration Master Updated Successfully');
                        $this->_redirect('master/placement-registeration-form');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = Inactive;

                if (@$id) {
                    $placement_model->update($data, array('id=?' => $id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/placement-registeration-form');
                }
                break;
            default:

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $placement_model->getRecords_selection_process();
           //  echo "<pre>"; print_r($result);exit;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
      
    } 
    public function placementSelectionProcessAction() {
       
        $this->view->action_name = 'placement';
        $this->view->sub_title_name = 'MasterSelectionProcess';
        $this->accessConfig->setAccess('SA_ACAD_MASTER_SELECTION_PROCESS');
        $placement_models = new Application_Model_MasterSelectionProcess();
        $placement_form = new Application_Form_SelectionProcess();


        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $placement_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($placement_form->isValid($this->getRequest()->getPost())) {
                        $data = $placement_form->getValues();                     
                        $id = $data['selection_id'];
                        if ($id) {

                          
                           // $group_data = $placement_models->getValidateRegistrationNo($id);
                            //$this->view->group_data = $group_data;
                            if (!empty($group_data)) {
                                $this->_flashMessenger->addMessage('This Registeration Number is Already Existed');
                                $this->_redirect('master/placement-selection-process');
                            } else {
                                   
                                $placement_models->insert($data);
                               
                                $this->_flashMessenger->addMessage('Details Added Successfully');

                                $this->_redirect('master/placement-selection-process');
                            }
                        }
                    }
                }

                break;
            case 'edit':
                $result = $placement_models->getRecord($id);
                $placement_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($placement_form->isValid($this->getRequest()->getPost())) {
                        $data = $placement_form->getValues();

                        $placement_models->update($data, array('id=?' => $id));
                        $this->_flashMessenger->addMessage('Details Added Successfully');
                        $this->_redirect('master/placement-selection-process');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = Inactive;

                if ($id) {
                    $placement_models->update($data, array('id=?' => $id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/placement-selection-process');
                }
                break;
            default:

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $placement_models->getRecords_selection_process();
              
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
      
    }
     public function configurationSelectionProcessAction()
      {
         
          $this->view->action_name = 'placement';
        $this->view->sub_title_name = 'MasterConfigureSelectionProcess';
        $this->accessConfig->setAccess('SA_ACAD_MASTER_CONFIGURE_SELETION_PROCESS');
        $placement_models = new Application_Model_ConfigureSelectionProcess();
        $placement_form = new Application_Form_ConfigurationSelectionProcess();


        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $placement_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($placement_form->isValid($this->getRequest()->getPost())) {
                        $data = $placement_form->getValues();                     
                        $id = $data['selection_id'];
                        if ($id) {

                          
                           // $group_data = $placement_models->getValidateRegistrationNo($id);
                            //$this->view->group_data = $group_data;
                            if (!empty($group_data)) {
                                $this->_flashMessenger->addMessage('This Registeration Number is Already Existed');
                                $this->_redirect('master/configuration-selection-process');
                            } else {
                                   
                                $placement_models->insert($data);
                               
                                $this->_flashMessenger->addMessage('Details Added Successfully');

                                $this->_redirect('master/configuration-selection-process');
                            }
                        }
                    }
                }

                break;
            case 'edit':
                $result = $placement_models->getRecord($id);
                $placement_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($placement_form->isValid($this->getRequest()->getPost())) {
                        $data = $placement_form->getValues();

                        $placement_models->update($data, array('id=?' => $id));
                        $this->_flashMessenger->addMessage('Details Added Successfully');
                        $this->_redirect('master/configuration-selection-process');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = Inactive;

                if ($id) {
                    $placement_models->update($data, array('id=?' => $id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/configuration-selection-process');
                }
                break;
            default:

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $placement_models->getRecords_selection_process();
              
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
   
     public function placementJobAnnouncementAction() {
          $this->view->action_name = 'placement';
        $this->view->sub_title_name = 'MasterJobAnnouncement';
        $this->accessConfig->setAccess('SA_ACAD_MASTER_JOB_ANNOUNCEMENT');
        $placement_models = new Application_Model_jobAnnouncement();
        $placement_form = new Application_Form_JobAnnouncement();


        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $placement_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($placement_form->isValid($this->getRequest()->getPost())) {
                        $data = $placement_form->getValues();                     
                        $id = $data['job_announcement_id'];
                        if ($id) {

                          
                           // $group_data = $placement_models->getValidateRegistrationNo($id);
                           //  $this->view->group_data = $group_data;
                            if (!empty($group_data)) {
                                $this->_flashMessenger->addMessage('This Registeration Number is Already Existed');
                                $this->_redirect('master/placement-job-announcement');
                            } else {
                                   
                                $placement_models->insert($data);
                               
                                $this->_flashMessenger->addMessage('Details Added Successfully');

                                $this->_redirect('master/placement-job-announcement');
                            }
                        }
                    }
                }

                break;
            case 'edit':
                $result = $placement_models->getRecord($id);
                $placement_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($placement_form->isValid($this->getRequest()->getPost())) {
                        $data = $placement_form->getValues();

                        $placement_models->update($data, array('id=?' => $id));
                        $this->_flashMessenger->addMessage('Details Added Successfully');
                        $this->_redirect('master/placement-job-announcement');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = Inactive;

                if ($id) {
                    $placement_models->update($data, array('id=?' => $id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/placement-job-announcement');
                }
                break;
            default:

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $placement_models->getRecords_selection_process();
              
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
     public function jobannouncementAction()
    {
            $this->view->action_name = 'MasterSelectionProcess';
            $this->view->sub_title_name = 'MasterSelectionProcess';
            $placement_models = new Application_Model_jobAnnouncement();
     

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $placement_models->getRecords_selection_process_user_and_faculty();
              
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
        
    } 


    public function gradeAction() {
        $this->view->action_name = 'Masterdata';
        $this->view->sub_title_name = 'Compound';
        //unit id
//	$unit_id = $this->_unit_id;
//	$ErpCompoundMaster_model		 = new Application_Model_ErpCompoundMaster();
        $Grade_model = new Application_Model_Grade();
        $ErpItem_model = new Application_Model_ErpItemsMaster();
//	$this->view->items_name	 = $ErpItem_model->getDropDownLists($unit_id);
        $com_id = $this->_getParam("id");
        $type = $this->_getParam("type");

        switch ($type) {

            case "add":
                $Grade_form = new Application_Form_Grade();
                $this->view->type = $type;
                $this->view->Grade_form = $Grade_form;
                if ($this->getRequest()->isPost()) {
                    if ($Grade_form->isValid($this->getRequest()->getPost())) {
                        //		$data			 = $Grade_form->getValues();
                        //		$data['unit_id'] = $unit_id;
                        //		$last_id		 = $ErpCompoundMaster_model->insert($data);
                        $items = $this->_getParam("items");
                        //		
                        //print_r($items['letter_grade']); die;
                        $data = array(
                            'letter_grade' => $items['letter_grade'],
                            'number_grade' => $items['number_grade'],
                        );
                        //	print_r($data); die;
                        //		foreach ( array_filter($items['grade_id']) as $key=>$grade_id) {
                        //			$data = array(
                        //	'erp_com_id'		 => $last_id,
                        //			'grade_id'	 => $grade_id,
                        //			'letter_grade'	 => $items['letter_grade'][$key]				
                        //	    'number_grade'	 => $items['number_grade'][$key]				
                        //		    );
                        //	print_r($data); die;
                        $Grade_model->insert($data);
                        //		}

                        $this->_flashMessenger->addMessage('Compound Master Added Successfully ');
                        $this->_redirect('master/grade');
                    }
                }

                break;
            /*    case 'edit':
              $ErpCompoundMaster_form		 = new Application_Form_ErpCompoundMaster();
              $this->view->type		 = $type;
              $this->view->ErpCompoundMaster_form	 = $ErpCompoundMaster_form;
              $result				 = $ErpCompoundMaster_model->getRecord($com_id);
              //print_r($result);die;
              $ErpCompoundMaster_form->populate($result);
              $results_view			 = $ErpCompoundItemMaster_model->findall($result['id']);
              //print_r($results_view);die;
              $this->view->results_view	 = $results_view;

              if ($this->getRequest()->isPost()) {
              if ($ErpCompoundMaster_form->isValid($this->getRequest()->getPost())) {
              $data = $ErpCompoundMaster_form->getValues();
              $ErpCompoundMaster_model->update($data, array('id=?' => $com_id));
              $ErpCompoundItemMaster_model->delete(array('erp_com_id=?'=>$com_id));
              //echo $com_id;die;
              $items = $this->_getParam("items");
              foreach ( array_filter($items['item_master_id']) as $key=>$item_master_id) {
              $data = array(
              'erp_com_id'		 => $com_id,
              'item_master_id'	 => $item_master_id,
              'item_quantity'	 => $items['item_quantity'][$key]
              );
              $ErpCompoundItemMaster_model->insert($data);
              }
              $this->_flashMessenger->addMessage('Compound Master  Updated Successfully');
              $this->_redirect('Compound/master');
              }
              }
              break; */
            case 'delete':
                $data['status'] = 2;
                if ($grade_id)
                    $Grade_model->update($data, array('grade_id=?' => $grade_id));
                $this->_flashMessenger->addMessage('Grade Master Deleted Successfully');
                $this->_redirect('master/grade');
                break;

            default:

                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Grade_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    // PROGRAM DESIGN SAME NAME 

    public function getProgramDesignSameNameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $programdesign_model = new Application_Model_ProgramDesign();
            $pd = $this->_getParam("pd");
            $result = $programdesign_model->getProgramDesign($pd);
            //print_r($result);die;
        }
    }

// PROGRAM Master SAME NAME 

    public function getProgramMasterSameNameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $ProgramMaster_model = new Application_Model_ProgramMaster();
            $pd = $this->_getParam("pd");
            $result = $ProgramMaster_model->getProgramMaster($pd);
            //print_r($result);
           // die;
        }
    }

//Based On Short Code need to display Academic Year

    /* public function ajaxGetAcademicAction(){
      $this->_helper->layout->disableLayout();
      if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
      $short_id = $this->_getParam("short_id");
      //print_r($short_code); die;
      $Academic_model= new Application_Model_Academic();
      $result= $Academic_model->getAcademic($short_id);


      //echo '<option value="">Select</option>';
      foreach($result as $k => $val){

      echo $val;


      }

      }die;
      } */

    public function ajaxGetAcademicAction() {
            
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $short_id = $this->_getParam("short_id");
            //print_r($short_code); die;
            $Academic_model = new Application_Model_Academic();
            $result = $Academic_model->getAcademic($short_id);
            echo json_encode($result);
            //===========================================================================
         //   $ProgramDesign_model = new Application_Model_Academic();
          //  $lastdate_record = $ProgramDesign_model->getlastdateRecord($short_id);
           // echo $val_my=json_encode($lastdate_record);
           // $this->view->$val_my = $val_my;
            
            
            //===========================================================================
          
            /* //echo '<option value="">Select</option>';
              foreach($result as $k => $val){
              echo $val['batch_code'];
              } */
            
        }die;
    }
    //==================================================Edited=============================================================
    public function ajaxGetProgramDesignAction() {
        //display_error(); die;
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $short_id = $this->_getParam("short_id");
           
            //print_r($short_code); die;
            $ProgramDesign_model = new Application_Model_ProgramMaster();
            $result = $ProgramDesign_model->getAcademic($short_id);

            /* //echo '<option value="">Select</option>';
              foreach($result as $k => $val){
              echo $val['batch_code'];
              } */
            echo json_encode($result);
        }die;
    }
    
     public function ajaxGetTermMaxDateAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_year_id");
            //print_r($academic_year_id);die;
            $ProgramDesign_model = new Application_Model_ProgramMaster();
           $lastdate_record = $ProgramDesign_model->getlastdateRecord($academic_id);
            
            echo json_encode($lastdate_record);
        }die;
    }
    //==================================satyam 29-04-2019 for experiential learnng=============================================
            public function ajaxGetFirstLastDateAction() {
               // echo "dsfdsf:"; exit;
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $terms_id = $this->_getParam("terms_id");
            
            //print_r($terms_id);
           // print_r($academic_year_id);
            $experientiallearning_model = new Application_Model_ExperientialLearning();
           $first_last_date = $experientiallearning_model->getfirstlastdateRecord($academic_year_id,$terms_id);
            //print_r($first_last_date); die;
            echo json_encode($first_last_date);
        }die;
    }
    //=============================================================================
//===================================================================================================================
//Based On Short Code need to display Program Name	

    public function ajaxGetProgramNameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $short_id = $this->_getParam("short_id");
            //print_r($short_code); die;
            $ProgramMaster_model = new Application_Model_ProgramMaster();
            $result = $ProgramMaster_model->getProgramName($short_id);


            //echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }die;
    }

    public function ajaxGetProgramNameDisplayAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $short_id = $this->_getParam("short_id");
            //print_r($short_code); die;
            $ProgramMaster_model = new Application_Model_ProgramMaster();
            $result = $ProgramMaster_model->getProgramNameDisplay($short_id);


            echo '<option value="">Select</option>';
            foreach ($result as $k => $val) {

                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }die;
    }

    //PROGRAM DESIGN VIEW

    public function programdesignviewAction() {
        $this->view->action_name = 'ProgramDesign';
        $this->view->sub_title_name = 'ProgramDesignView';
        $this->accessConfig->setAccess('SA_ACAD_PROG_CAL_VIEW');
        $pd_view_form = new Application_Form_ProgramDesignView();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $pd_view_form;
    }

    // PROGRAM DESIGN VIEW AJAX
    public function getProgramViewAction() {

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $pd_id = $this->_getParam("pm_name");
            $short_id = $this->_getParam("short_id");

            if ($pd_id) {

                $programdesign_model = new Application_Model_ProgramDesign();
                $result = $programdesign_model->getProgram($short_id, $pd_id);
                //print_r($result);die;
                $this->view->academicresult = $result;
            }
        }
    }

    //Term Master View	

    public function termmasterviewAction() {
        $this->view->action_name = 'Termmasterview';
        $this->view->sub_title_name = 'Termmasterview';
        $this->accessConfig->setAccess('SA_ACAD_TERM_VIEW');
        $TermMasterView_form = new Application_Form_TermMasterView();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $TermMasterView_form;
    }

    //Term Master View
    public function getTermViewAction() {

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $term_id = $this->_getParam("academicid");



            if ($term_id) {

                $TermMasterView_model = new Application_Model_TermMasterView();
                $result = $TermMasterView_model->getprogram($term_id);
              // echo "<pre>";print_r($result);die;
                $this->view->academicresult = $result;
            }
        }
    }

    //PROGRAM SAME NAME 
    public function getProgramAction() {

        $pd_id = $this->_getParam("id");

        $type = $this->_getParam("type");
        if ($type) {

            $programdesign_model = new Application_Model_ProgramDesign();
            $result = $programdesign_model->getRecord($pd_id);
            $this->view->result = $result;

            $htmlcontent = $this->view->render('Master/ajax-get-employee-print.phtml');
            //print_r($htmlcontent); die;

            $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Employee Details");
        }
    }

//Elective Course Learning Master

    public function electivecourselearningAction() {
        $this->view->action_name = 'electivecourselearning';
        $this->view->sub_title_name = 'electivecourselearning';
        $this->accessConfig->setAccess("SA_ACAD_EC_LEARN");
        $electivecourselearning_model = new Application_Model_ElectiveCourseLearning();
        $electivecourselearning_form = new Application_Form_ElectiveCourseLearning();
        $ecrl_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $electivecourselearning_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($electivecourselearning_form->isValid($this->getRequest()->getPost())) {
                        $data = $electivecourselearning_form->getValues();
                        $electivecourselearning_model->insert($data);

                        $this->_flashMessenger->addMessage('Details Successfully added');

                        $this->_redirect('master/electivecourselearning');
                    }
                }


                break;
            case 'edit':
                $result = $electivecourselearning_model->getRecord($ecrl_id);
                $electivecourselearning_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($electivecourselearning_form->isValid($this->getRequest()->getPost())) {
                        $data = $electivecourselearning_form->getValues();

                        $electivecourselearning_model->update($data, array('ecrl_id=?' => $ecrl_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/electivecourselearning');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($ecrl_id) {
                    $electivecourselearning_model->update($data, array('ecrl_id=?' => $ecrl_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/electivecourselearning');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $electivecourselearning_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //COURSE Master	
    public function courseAction() {
        $this->view->action_name = 'course';
        $this->view->sub_title_name = 'course';
        $this->accessConfig->setAccess("SA_ACAD_COURSE");
        $course_model = new Application_Model_Course();
        $course_form = new Application_Form_Course();
        $course_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $course_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($course_form->isValid($this->getRequest()->getPost())) {
                        $data = $course_form->getValues();
                        $course_model->insert($data);
                        //echo '<pre>';  print_r($data); die;
                        $this->_flashMessenger->addMessage('Course Successfully added');

                        $this->_redirect('master/course');
                    }
                }


                break;
            case 'edit':
                $result = $course_model->getRecord($course_id);
                $course_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($course_form->isValid($this->getRequest()->getPost())) {
                        $data = $course_form->getValues();

                        $course_model->update($data, array('course_id=?' => $course_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/course');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($course_id) {
                    $course_model->update($data, array('course_id=?' => $course_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/course');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $course_model->getRecords();
                //echo '<pre>'; print_r($result);die;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //Course Name same 


    public function getCourseCodeSameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $coursename_model = new Application_Model_Course();
            $course_code = $this->_getParam("course_code");
            $result = $coursename_model->getCourseCode($course_code);
          //  print_r($result);
          //  die;
        }
    }

    //Experiential Learning Components Master	
    public function experientiallearningcomponentsAction() {
        $this->view->action_name = 'experientiallearningcomponents';
        $this->view->sub_title_name = 'experientiallearningcomponents';
        $this->accessConfig->setAccess('SA_ACAD_E_LEARN_C');
        $ExperientialLearningComponents_model = new Application_Model_ExperientialLearningComponents();
        $ExperientialLearningComponents_form = new Application_Form_ExperientialLearningComponents();
        $elc_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ExperientialLearningComponents_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($ExperientialLearningComponents_form->isValid($this->getRequest()->getPost())) {
                        $data = $ExperientialLearningComponents_form->getValues();

                        $ExperientialLearningComponents_model->insert($data);

                        $this->_flashMessenger->addMessage('Details Successfully added');

                        $this->_redirect('master/experientiallearningcomponents');
                    }
                }


                break;
            case 'edit':
                $result = $ExperientialLearningComponents_model->getRecord($elc_id);
                $ExperientialLearningComponents_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($ExperientialLearningComponents_form->isValid($this->getRequest()->getPost())) {
                        $data = $ExperientialLearningComponents_form->getValues();

                        $ExperientialLearningComponents_model->update($data, array('elc_id=?' => $elc_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/experientiallearningcomponents');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($elc_id) {
                    $ExperientialLearningComponents_model->update($data, array('elc_id=?' => $elc_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/experientiallearningcomponents');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ExperientialLearningComponents_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //same name validation
    public function getComponentSameAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $ExperientialLearningComponents_model = new Application_Model_ExperientialLearningComponents();
            $componentname = $this->_getParam("componentname");
            $result = $ExperientialLearningComponents_model->getcomponenetname($componentname);
            //print_r($result);die;
        }
    }

    
    //Experiential Learning Master	
    public function experientiallearningAction() {
        $this->view->action_name = 'experientiallearning';
        $this->view->sub_title_name = 'experientiallearning';
        $this->accessConfig->setAccess('SA_ACAD_E_LEARN');
        $ExperientialLearning_model = new Application_Model_ExperientialLearning();
        $ExperientialLearning_form = new Application_Form_ExperientialLearning();
        $el_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ExperientialLearning_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($_POST) {
                        $data = $_POST;
                       
                        $start_date = explode('/', $data['start_date']);
                        $end_date = explode('/', $data['end_date']);
                        $data['start_date_type'] = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];
                        $data['end_date_type'] = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];						
                        $ExperientialLearning_model->insert($data);

                        $this->_flashMessenger->addMessage('Details Successfully added');

                        $this->_redirect('master/experientiallearning');
                    }
                }


                break;
            case 'edit':
                $result = $ExperientialLearning_model->getRecord($el_id);
                $ExperientialLearning_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($_POST) {
                        $data = $_POST;
                        $start_date = explode('/', $data['start_date']);
                        $end_date = explode('/', $data['end_date']);
                        $data['start_date_type'] = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];
                        $data['end_date_type'] = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];

                        $ExperientialLearning_model->update($data, array('el_id=?' => $el_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/experientiallearning');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($el_id) {
                    $ExperientialLearning_model->update($data, array('el_id=?' => $el_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/experientiallearning');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ExperientialLearning_model->getRecords();

                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //Experiential Learning View
    public function experientiallearningdesignviewAction() {
        $this->view->action_name = 'experientiallearningview';
        $this->view->sub_title_name = 'experientiallearningview';
        $this->accessConfig->setAccess('SA_ACAD_E_LEARN_DESIGN_VIEW');
        $el_view_form = new Application_Form_ExperientialLearningView();
        //$academic_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $el_view_form;
    }

    //Experiential Learning View Ajax
    public function ajaxExperientialViewAction() {

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $el_id = $this->_getParam("academicid");



            if ($el_id) {

                $ExperientialLearning_model = new Application_Model_ExperientialLearning();
                $result = $ExperientialLearning_model->getprogram($el_id);
                //print_r($result);die;
                $this->view->academicresult = $result;
            }
        }
    }

    //Evaluation Components
    public function evaluationcomponentsAction() {
        $this->view->action_name = 'evaluationcomponents';
        $this->view->sub_title_name = 'evaluationcomponents';
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
        $EvaluationComponents_form = new Application_Form_EvaluationComponents();
        $ec_id = $this->_getParam("id");
        //print_r($ec_id);die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $EvaluationComponents_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($EvaluationComponents_form->isValid($this->getRequest()->getPost())) {
                        $data = $EvaluationComponents_form->getValues();

                        $last_insert_id = $EvaluationComponents_model->insert($data);



                        $courses = $this->getRequest()->getPost('courses'); //Get AddMore Fields From View
                       // echo '<pre>';
                       // print_r($courses);
                        foreach (array_filter($courses['eci_name']) as $key => $eci_name) {
                            //print_r($eci_name); die;
                            $employee_data = array("ec_id" => $last_insert_id,
                                "eci_name" => $eci_name, //Employee Field Name
                            );
                            //print_r($employee_data); die;
                            $EvaluationComponentsItems_model->insert($employee_data); //insert data into sub model table	
                        }

                        $this->_flashMessenger->addMessage('Details Successfully added');

                        $this->_redirect('master/evaluationcomponents');
                    }
                }


                break;
            case 'edit':
                $result = $EvaluationComponents_model->getRecord($ec_id);
                $item_result = $EvaluationComponentsItems_model->getRecords($ec_id);
                //print_r($item_result);die;
                $this->view->item_result = $item_result;
                $EvaluationComponents_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($EvaluationComponents_form->isValid($this->getRequest()->getPost())) {
                        $data = $EvaluationComponents_form->getValues();

                        $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));

                        $courses = $this->getRequest()->getPost('courses');
                        //print_r($courses);die;
                        $EvaluationComponentsItems_model->trashItems($ec_id); //Delete Fields in Company						

                        foreach (array_filter($courses['eci_name']) as $key => $eci_name) {

                            $employee_data = array("ec_id" => $ec_id,
                                "eci_name" => $eci_name, //employee Field Name
                            );
                            //print_r($employee_data);die;
                            $EvaluationComponentsItems_model->insert($employee_data);
                        }


                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/evaluationcomponents');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/evaluationcomponents');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $EvaluationComponents_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //Empolyee Allotment
    public function employeeallotmentAction() {
        $this->view->action_name = 'employeeallotment';
        $this->view->sub_title_name = 'employeeallotment';
        $this->accessConfig->setAccess('SA_ACAD_FACULTY_ALLOTMENT');
        $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
        $EmployeeAllocationItems_model = new Application_Model_EmployeeAllocationItems();
        $EmployeeAllotment_form = new Application_Form_EmployeeAllotment();

        $ea_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $EmployeeAllotment_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($EmployeeAllotment_form->isValid($this->getRequest()->getPost())) {
                        $data = $EmployeeAllotment_form->getValues();

                        $last_insert_id = $EmployeeAllotment_model->insert($data);

                        $employee_allocation = $this->getRequest()->getPost('employee');
                        //print_r($employee_allocation);die;
                        foreach ($employee_allocation['term_id'] as $key => $term_id) {
                            $item_data['ea_id'] = $last_insert_id;
                            $item_data['term_id'] = $term_id;
                            $item_data['cc_id'] = $employee_allocation['cc_id'][$key];
                            $item_data['course_id'] = $employee_allocation['course_id'][$key];
                            $item_data['credit_id'] = $employee_allocation['credit_id'][$key];
                            $item_data['department_id'] = $employee_allocation['department_id'][$key];
                            $item_data['employee_id'] = $employee_allocation['employee_id'][$key];
                            $course_id = $item_data['course_id'];
                            $emp_ids = trim(implode(',', $employee_allocation['faculty_id'][$course_id]), ',');
                            $emp_ids1 = trim(implode(',', $employee_allocation['visiting_faculty_id'][$course_id]), ',');
                            $item_data['faculty_id'] = $emp_ids;
                            $item_data['visiting_faculty_id'] = $emp_ids1;
                            $item_data['remarks'] = $employee_allocation['remarks'][$key];

                            //print_r($item_data['employee_id']);die;
                            $EmployeeAllocationItems_model->insert($item_data);

                            //print_r($item_data);die;
                        }

                        $this->_flashMessenger->addMessage('Employee Successfully added');

                        $this->_redirect('master/employeeallotment');
                    }
                }


                break;
            case 'edit':
                $result = $EmployeeAllotment_model->getRecord($ea_id);
                $this->view->emp_allotment_id = $ea_id;
                $item_result = $EmployeeAllocationItems_model->getRecords($ea_id);
                //print_r($item_result);die;
                $this->view->item_result = $item_result;
                $EmployeeAllotment_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($EmployeeAllotment_form->isValid($this->getRequest()->getPost())) {
                        $emp_allotment_ids = $this->getRequest()->getPost('ead_id');
                        //print_r($emp_allotment_ids);exit;
                        //$data = $EmployeeAllotment_form->getValues();
                        //$EmployeeAllotment_model->update($data, array('ea_id=?' => $ea_id));
                        //$ea_id = $EmployeeAllotment_model->insert($data);
                        $employee_allocation = $this->getRequest()->getPost('employee');
                        //print_r($employee_allocation);exit;
                        //print_r($employee_allocation);die;
                        $EmployeeAllocationItems_model->trashItems($ea_id);
                        foreach ($emp_allotment_ids as $key => $ead_id) {
                            $item_data['ea_id'] = $ea_id;
                            $item_data['term_id'] = $employee_allocation['term_id'][$key];
                            $item_data['cc_id'] = $employee_allocation['cc_id'][$key];
                            $item_data['course_id'] = $employee_allocation['course_id'][$key];
                            $item_data['credit_id'] = $employee_allocation['credit_id'][$key];
                            $item_data['department_id'] = $employee_allocation['department_id'][$key];
                            $item_data['employee_id'] = $employee_allocation['employee_id'][$key];
                            $course_id = $item_data['course_id'];
                            $emp_ids = trim(implode(',', $employee_allocation['faculty_id'][$course_id]), ',');
                            $emp_ids1 = trim(implode(',', $employee_allocation['visiting_faculty_id'][$course_id]), ',');

                            $item_data['faculty_id'] = $emp_ids;
                            $item_data['visiting_faculty_id'] = $emp_ids1;
                            $item_data['remarks'] = $employee_allocation['remarks'][$key];
                            //print_r($item_data['employee_id']);die;
                            //print_r($item_data);
                            $error = $EmployeeAllocationItems_model->insert($item_data);
                            //print_r($item_data);die;
                        }
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/employeeallotment');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($ea_id) {
                    $EmployeeAllotment_model->update($data, array('ea_id=?' => $ea_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/employeeallotment');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $EmployeeAllotment_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function ajaxStudentListViewAction() {
        $student_list = new Application_Model_Attendance();
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam('batch_id');
            $course_id = $this->_getParam("course_id");
            $date_val = $this->_getParam("date_val");
            // print_r($date_val);exit;
            //$date_str = date("d-m-Y",strtotime($date_val));
            $attendance_details = $student_list->getAttendanceResult($term_id, $batch_id, $course_id, $date_val);

            $no_of_classes = $student_list->connectBatchSheduler($term_id, $batch_id, $course_id, $date_val);
            $result = $student_list->getStudentList($term_id, $batch_id, $course_id);
            //print_r($result);exit;
            $result[count($result) - 1]['class_no'] = $no_of_classes;
            $result[count($result) - 1]['course_id'] = $course_id;
            $result[count($result) - 1]['attendance_details'] = $attendance_details;
            $this->view->result = $result;
        }
    }

    public function classalotmentAction() {
        $this->view->action_name = 'Class Allotment';
        $this->view->sub_title_name = 'Class Allotment';
        $atendence_saver_model = new Application_Model_Attendance();
        $attendance_form = new Application_Form_Attendance();
        $employee_model = new Application_Model_HRMModel();
        $mobile_message = new Application_Model_Mobile();
        $classalotment = new Application_Model_Classalotment();
        $termDetails = new Application_Model_TermMaster();
        $batchDetailc = new Application_Model_Academic();
        $course_code = new Application_Model_Course();
        $ec_id = $this->_getParam("id");
        //print_r($ec_id);die;


        $type = $this->_getParam("type");
        $this->view->type = $type;
        // $this->view->result = "hello";
        $this->view->form = $attendance_form;
        switch ($type) {
            case "add":

                if ($this->getRequest()->isPost()) {
                    if ($attendance_form->isValid($this->getRequest()->getPost())) {
                        $parrents_number = array('mother_no', 'father_no');

                        $batch_id = $this->getRequest()->getPost('academic_year_id');
                        $term_id = $this->getRequest()->getPost('term_id');
                        $date = $this->getRequest()->getPost('date');
                        $course_id = $this->getRequest()->getPost('course_id');
                        $class_no = $this->getRequest()->getPost('class_no');
                        $faculty = $this->getRequest()->getPost('faculty');

                        $data = array(
                            'class_no' => $class_no,
                            'term_id' => $term_id,
                            'batch_id' => $batch_id,
                            'course_id' => $course_id,
                            'date' => date('Y-m-d', strtotime($date)),
                            'faculty_id' => $faculty
                        );
                       $bool = $this->check1($date, $course_id, $batch_id, $class_no, $term_id);
                       if(count($bool) !=0){
                           $empl_name = $employee_model->getAllEmployee1($bool[0]['faculty_id'])[0]['name'];
                           if($bool[0]['faculty_id']==$_SESSION['admin_login']['admin_login']->empl_id){
                                $this->_flashMessenger->addMessage("You are already Registered !");
                           }
                           else{
                            $this->_flashMessenger->addMessage("$empl_name is already Registered !");
                           }
                       }
                       else{
                        $classalotment->insert($data);
                        $this->_flashMessenger->addMessage('Class has Alotted Successfully !');
                       }
                    }
                    $this->_redirect('master/classalotment');
                }

                break;
            case 'edit':
                $result = $classalotment->getRecordById($ec_id);
                $result['academic_year_id'] = $result[0]['batch_id'];
                $result['term_id'] = $result[0]['term_id'];
                $_SESSION['classalotment']['course_id'] = $result[0]['course_id'];
                $_SESSION['classalotment']['date'] = date('d-m-Y', strtotime($result[0]['date']));
                $_SESSION['classalotment']['faculty'] = $result[0]['faculty_id'];
                $_SESSION['classalotment']['class_no'] = $result[0]['class_no'];

                $academic_year_id = $result['academic_year_id'];
                $TermMaster_model = new Application_Model_TermMaster();
                $term_result = $TermMaster_model->getTermDropDownList($academic_year_id);

                $attendance_form->getElement('term_id')->setAttrib('style', array('display:initial'));
                $employee_id = $attendance_form->createElement('select', 'term_id');
                $employee_id->setAttrib('class', array('form-control', 'chosen-select'));
                $employee_id->removeDecorator("htmlTag");
                $employee_id->addMultiOptions(array('' => 'Select'));
                $employee_id->addMultiOptions($term_result);
                $employee_id->setRegisterInArrayValidator(false);
                $attendance_form->addElement($employee_id);


                $attendance_form->populate($result);
                $this->view->result = $result;

                if ($this->getRequest()->isPost()) {
                    if ($this->getRequest()->getPost()) {
                        $batch_id = $this->getRequest()->getPost('academic_year_id');
                        $term_id = $this->getRequest()->getPost('term_id');
                        $date = $this->getRequest()->getPost('date');
                        $course_id = $this->getRequest()->getPost('course_id');
                        $class_no = $this->getRequest()->getPost('class_no');
                        $faculty = $this->getRequest()->getPost('faculty');
                        $data = array(
                            'class_no' => $class_no,
                            'term_id' => $term_id,
                            'batch_id' => $batch_id,
                            'course_id' => $course_id,
                            'date' => date('Y-m-d', strtotime($date)),
                            'faculty_id' => $faculty
                        );

                        $classalotment->update($data, array('id=?' => $ec_id));


                        $this->_flashMessenger->addMessage('Class Updated Successfully');
                        $this->_redirect('master/classalotment');
                    }
                }



                //print_r($result);exit;
                //print_r($result);exit;
                //  if ($this->getRequest()->isPost()){
                //  if ($attendance_form->isValid($this->getRequest()->getPost())) {
                //  $data = $EvaluationComponents_form->getValues();
                // $this->_flashMessenger->addMessage('Evaluation Components Updated Successfully');
                //$this->_redirect('evaluation-components/index');

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
                $result = $classalotment->getRecords();

                $i = 0;
                foreach ($result as $key => $value) {
                    // print_r($value['faculty_id']);exit;
                    $empl_name = $employee_model->getAllEmployee1($value['faculty_id']);
                    $result[$i]['faculty_id'] = $empl_name[0]['name'];
                    $result[$i]['term_id'] = $termDetails->getTermName($value['term_id'])['term_name'];
                    $result[$i]['batch_id'] = $batchDetailc->getBatchDetails($value['batch_id'])[0]['short_code'];
                    $result[$i]['course_id'] = $course_code->getCourseCodeById($value['course_id'])['course_code'];

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

    public function check() {
        $class_alotment = new Application_Model_Classalotment();
        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            
            
            $date = $this->_getParam("date");
            $course_id = $this->_getParam("course_id");
            $batch = $this->_getParam("batch_id");
            $class = $this->_getParam('class_no');
            $term = $this->_getParam('term_id');
            
            
            $result = $class_alotment->check($course_id, $date, $class, $batch, $term);
            echo $result;
            exit;
        }
    }
    
    
    
    
      public function check1($date,$course_id,$batch,$class,$term) {
        $class_alotment = new Application_Model_Classalotment();
            $result = $class_alotment->check($course_id, $date, $class, $batch, $term);
            return $result;
         
       
    }

    //ajax employee allotment
    public function ajaxEmployeeAllotmentAction() {

        $this->_helper->layout->disableLayout();

        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $academic_id = $this->_getParam("academic_year_id");

            $Course_model = new Application_Model_Course();
            $data = $Course_model->getDropDownList();

            $this->view->DropDownCourse = $data;
            $Course_model = new Application_Model_Course();
            $termwisecourses = $Course_model->getDropDownList1($academic_id);
            echo '<div class="row" style="">';

            echo '<div class="col-sm-2" style="height: 100px;overflow-y: scroll;margin-top:20px;">';
            echo '<label  class="control-label">Term</label></br>';
            foreach ($termwisecourses as $k => $DropDownCourse) {
                echo '<label class="label-checkbox" style="padding-left:50px; margin:0px;" >';

                echo '<span class="custom-checkbox" style="float:left;"></span> </label>' . $k . '';

                foreach ($DropDownCourse as $key => $value) {
                    echo '<label class="label-checkbox" style="padding-left:50px; margin:0px;" >';
                    echo '<input type="checkbox" name="course_name[]" id="course_name" class="checkbox1" 
										value=' . $key . ' /> ';
                    echo '<span class="custom-checkbox" style="float:left;"></span> </label> ' . $value . '';
                }
            }
            echo '</div></div> ';
        } die;
    }


    //Employee Details View Ajax
    public function ajaxEmployeeDetailsViewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam("term_id");
            $emp_allotment = $this->_getParam("emp_allotment");
            $EmployeeAllocationItems_model = new Application_Model_EmployeeAllocationItems();
            //echo $emp_allotment;die;
            if (!empty($emp_allotment)) {

                $item_result = $EmployeeAllocationItems_model->getItemsRecords($emp_allotment);
                
                $term_id = $item_result[0]['term_id'];
                $this->view->item_result = $item_result;
                $Corecourselearning_model = new Application_Model_Corecourselearning();
                
                $result = $Corecourselearning_model->getprogramByYearTerm($academic_year_id, $term_id);
                $this->view->employee_result = $result;
                //$HRMModel_model = new Application_Model_HRMModel();
                //$department = $HRMModel_model->getDepartments();
                //print_r($department);die;
                // $this->view->department = $department;
                $HRMModel_model = new Application_Model_HRMModel();
                $employee = $HRMModel_model->getOrgFacultyIds();
                //print_r($department);die;
                $this->view->employee = $employee;

                $visitingemployee = $HRMModel_model->getVisitingEmployeeIds();
                $this->view->visitingemployees = $visitingemployee;
            } else {
                if ($academic_year_id && $term_id) {
                    $Corecourselearning_model = new Application_Model_Corecourselearning();
                    $result = $Corecourselearning_model->getprogramByYearTerm($academic_year_id, $term_id);
                    //print_r($result);die;
                    $this->view->employee_result = $result;
                    // $HRMModel_model = new Application_Model_HRMModel();
                    // $department = $HRMModel_model->getDepartments();
                    //print_r($department);die;
                    // $this->view->department = $department;
                    $HRMModel_model = new Application_Model_HRMModel();
                    $employee = $HRMModel_model->getEmployeeIds();
                    //print_r($department);die;
                    $this->view->employee = $employee;

                    $visitingemployee = $HRMModel_model->getVisitingEmployeeIds();
                    $this->view->visitingemployees = $visitingemployee;
                }
            }
        }
    }

    public function ajaxEmployeeDataViewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam('term_id');
            //$year_id = $this->_getParam("year_id");
            //echo $academic_year_id;die;
            $EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
            $grade_result = $EmployeeAllotment_model->getValidFacultyRecord($academic_year_id, $term_id);
            $counts = count($grade_result['ea_id']);
            //print_r($counts);die;
            echo json_encode($counts);
            die;
            $this->view->grade_result = $grade_result;
        }
    }

    public function ajaxGetBatchTermsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            if ($batch_id) {
                $Term_Model = new Application_Model_TermMaster();
                $term_data = $Term_Model->getBatchTerms($batch_id);
                //print_r($SubProgram);die;
                echo '<option value="">Select </option>';
                foreach ($term_data as $k => $val) {
                    echo '<option value="' . $k . '" >' . $val . '</option>';
                }
            }
        }die;
    }

    //Term Master
   public function termmasterAction(){
        $this->view->action_name = 'Term';
        $this->view->sub_title_name = 'Termmaster';
        $this->accessConfig->setAccess('SA_ACAD_TERM_MASTER');
        $Term_model = new Application_Model_TermMaster();
        $Term_form = new Application_Form_TermMaster();
        //$Termdate_model = new Application_Model_Termdate();
        $term_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Term_form;
        //$getyearid = $Term_model->getYear_Master();
        //echo "<pre>";
        //print_r($result1);die;

        switch ($type) {
            case "add":

                $this->view->type = $type;
                $this->view->form = $Term_form;

                if ($this->getRequest()->isPost()) {
                    //echo '<pre>';print_r($_POST);die;
                    if ($Term_form->isValid($this->getRequest()->getPost())) {
                        $data = $Term_form->getValues();
                        $term_name = $data['term_name'];
                        $start_date = explode('/', $data['start_date']);
                        $end_date = explode('/', $data['end_date']);
                        $data['start_date_type'] = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];
                        $data['end_date_type'] = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];
                        $academic_model = new Application_Model_TermMaster();
                        $academic_result = $academic_model->getRecordByAcademicId($data['academic_year_id']);
                        //	print_r($data); die; 
                        if ($term_name) {
                            $Term_model = new Application_Model_TermMaster();
                            $term_data = $Term_model->getValidateTermName($term_name);
                            $this->view->term_data = $term_data;
                            
                            $electives_credits = $this->getRequest()->getPost('electives_credits');
                            if (!empty($electives_credits)) {
                                $data['electives_credits'] = $electives_credits;
                            } else {
                                $data['electives_credits'] = 0;
                            }
                            /* if(!empty($term_data)){

                              $this->_flashMessenger->addMessage('This Term Name is Already Existed');
                              $this->_redirect('master/termmaster');
                              } */

                            //else{
                           
                             $Term_model->insert($data);
                            
                            $this->_flashMessenger->addMessage(' Added Successfully ');
                            $this->_redirect('master/termmaster');
                            //}
                        }
                    }
                }

                break;
            case "edit":

                $this->view->type = $type;
                $this->view->form = $Term_form;
                //echo "hi";die;
                $result = $Term_model->getRecord($term_id);
                //print_r($result);die;

                $this->view->result = $result;
                $Term_form->populate($result);


                if ($this->getRequest()->isPost()) {
                    if ($Term_form->isValid($this->getRequest()->getPost())) {
                        //echo 'dsd'; die;
                        $data = $Term_form->getValues();
                        $start_date = explode('/', $data['start_date']);
                        $end_date = explode('/', $data['end_date']);
                        $data['start_date_type'] = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];
                        $data['end_date_type'] = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];
                        //print_r($data);die;
                        $data['electives_credits'] = $this->getRequest()->getPost('electives_credits');
                        //print_r($data['electives_credits']); die;
                        $Term_model->update($data, array('term_id=?' => $term_id));
                        $this->_flashMessenger->addMessage(' Updated Successfully');
                        $this->_redirect('master/termmaster');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($term_id) {
                    $Term_model->update($data, array('term_id=?' => $term_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/termmaster');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Term_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function getTermAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_model = new Application_Model_TermMaster();
            $term = $this->_getParam("term");
            $result = $term_model->getTerm($term);
            //	print_r($result);die;
        }
    }

    //Credit Master
    public function creditmasterAction() {
        $this->view->action_name = 'Credit';
        $this->view->sub_title_name = 'Creditmaster';
        $this->accessConfig->setAccess("SA_ACAD_CREDIT");
        $Credit_model = new Application_Model_CreditMaster();
        $Credit_form = new Application_Form_CreditMaster();
        //$Termdate_model = new Application_Model_Termdate();
        $credit_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $Credit_form;

        switch ($type) {
            case "add":

                $this->view->type = $type;
                $this->view->form = $Credit_form;

                if ($this->getRequest()->isPost()) {
                    //echo '<pre>';print_r($_POST);die;
                    if ($Credit_form->isValid($this->getRequest()->getPost())) {
                        $data = $Credit_form->getValues();
                        $credit_value = $data['credit_value'];
                        if ($credit_value) {
                            $Credit_model = new Application_Model_CreditMaster();
                            $term_data = $Credit_model->getValidateCreditValue($credit_value);
                            $this->view->term_data = $term_data;

                            if (!empty($term_data)) {

                                $this->_flashMessenger->addMessage('This Credit value is Already Existed');
                                $this->_redirect('master/creditmaster');
                            } else {

                                $Credit_model->insert($data);
                                //print_r($data); die; 
                                $this->_flashMessenger->addMessage(' Added Successfully ');
                                $this->_redirect('master/creditmaster');
                            }
                        }
                    }
                }

                break;
            case "edit":

                $this->view->type = $type;
                $this->view->form = $Credit_form;
                //echo "hi";die;
                $result = $Credit_model->getRecord($credit_id);
                //print_r($result);die;

                $this->view->result = $result;
                $Credit_form->populate($result);
                //$data['credit_value'] = $result['credit_value'];

                $Credit_form->getElement("credit_value")
                        ->setAttrib('readonly', 'readonly')
                        ->setAttrib('class', array('form-control'))
                        ->setValue($result['credit_value']);
                //->setMultiOptions($data);
                if ($this->getRequest()->isPost()) {
                    if ($Credit_form->isValid($this->getRequest()->getPost())) {
                        //echo 'dsd'; die;
                        $data = $Credit_form->getValues();



                        //print_r($data);die;
                        $Credit_model->update($data, array('credit_id=?' => $credit_id));
                        $this->_flashMessenger->addMessage(' Updated Successfully');
                        $this->_redirect('master/creditmaster');
                    }
                }

                break;
            case 'delete':
                $data['status'] = 2;
                if ($credit_id) {
                    $Credit_model->update($data, array('credit_id=?' => $credit_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/creditmaster');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $Credit_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    //Fee Head Master
    public function feeheadAction() {
        $this->view->action_name = 'feehead';
        $this->view->sub_title_name = 'Feehead';
        $FeeHead_model = new Application_Model_FeeHead();
        $FeeHead_form = new Application_Form_FeeHead();
        $feehead_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $FeeHead_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($FeeHead_form->isValid($this->getRequest()->getPost())) {
                        $data = $FeeHead_form->getValues();
                        //print_r($data);die;
                        $FeeHead_model->insert($data);
                        $this->_flashMessenger->addMessage('Details Added Successfully ');
                        $this->_redirect('master/feehead');
                    }
                }
                break;
            case 'edit':
                $result = $FeeHead_model->getRecord($feehead_id);
                $FeeHead_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($FeeHead_form->isValid($this->getRequest()->getPost())) {
                        $data = $FeeHead_form->getValues();

                        $FeeHead_model->update($data, array('feehead_id=?' => $feehead_id));
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('master/feehead');
                    } else {
                        
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($feehead_id) {
                    $FeeHead_model->update($data, array('feehead_id=?' => $feehead_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('master/feehead');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $FeeHead_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function getCourseTermsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $year_id = $this->_getParam("year_id");
            $academic_year_id = $this->_getParam("academic_year_id");
            if ($year_id) {
                $Term_Model = new Application_Model_TermMaster();
                $term_data = $Term_Model->getYearTerms($year_id, $academic_year_id);
                //print_r($term_data);die;
                echo '<option value="">Select </option>';
                foreach ($term_data as $k => $val) {
                    echo '<option value="' . $k . '" >' . $val . '</option>';
                }
            }
        }die;
    }

    public function ajaxGetCoursesAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $cc_id = $this->_getParam("cc_id");
            //$academic_year_id = $this->_getParam("academic_year_id");
            if ($cc_id) {
                $Course_model = new Application_Model_Course();
                $course_data = $Course_model->getCoursesDropDownList($cc_id);
                echo '<option value="">Select</option>';
                foreach ($course_data as $k => $val) {
                    echo '<option value="' . $k . '">' . $val . '</option>';
                }
            }
        }die;
    }

    public function ajaxGetElectiveCoursesAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $course_category_id = $this->_getParam('course_category_id');
            if ($course_category_id) {
                $course_model = new Application_Model_Course();
                $elective_course_data = $course_model->getElectiveCoursesDropDownList($course_category_id);
                echo '<option value="">Select</option>';
                foreach ($elective_course_data as $k => $val) {
                    echo '<option value="' . $k . '">' . $val . '</option>';
                }
            }
        }die;
    }

    public function ajaxGetTermCoursesAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam('term_id');
            if ($term_id) {
                $course_model = new Application_Model_Corecourselearning();
                $elective_course_data = $course_model->getcorecourses($academic_year_id, $term_id);
                //print_r($elective_course_data);
                echo '<option value="">Select</option>';
                foreach ($elective_course_data as $k => $val) {
                    echo '<option value="' . $val['course_id'] . '">' . $val['course_name'] . '</option>';
                }
            }
        }die;
    }

    public function ajaxFacultyTermCoursesAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $term_id = $this->_getParam('term_id');
            $employee_id = $this->_getParam("employee_id");
            $department_id = $this->_getParam('department_id');
            if ($term_id) {
                $course_model = new Application_Model_Corecourselearning();
                $elective_course_data = $course_model->getcorecourses($academic_year_id, $term_id);

                $employeeAllotmentModel = new Application_Model_EmployeeAllotment();
                $emp_courses = $employeeAllotmentModel->getEmployeeTerms($academic_year_id, $department_id, $employee_id, $term_id);
                $course_ids = array();
                foreach ($emp_courses as $row) {
                    $course_ids[] = $row['course_id'];
                }
                $filtered_emp_courses = array();
                foreach ($elective_course_data as $key => $row) {
                    if (in_array($row['course_id'], $course_ids)) {
                        $filtered_emp_courses[] = $row;
                    }
                }
                //print_r($filtered_emp_courses);
                echo '<option value="">Select</option>';
                foreach ($filtered_emp_courses as $k => $val) {
                    echo '<option value="' . $val['course_id'] . '">' . $val['course_name'] . '</option>';
                }
            }
        }die;
    }

    public function ajaxGetCourseDataAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $cc_id = $this->_getParam("cc_id");
            $course_id = $this->_getParam("course_id");
            $Corecourselearning_model = new Application_Model_Corecourselearning();
            $data = $Corecourselearning_model->getCourseRecord($academic_year_id, $cc_id, $course_id);
            echo json_encode($data);
        }die;
    }

    public function ajaxGetCreditidValueAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $credit_id = $this->_getParam("credit_id");
            $CreditMaster_model = new Application_Model_CreditMaster();
            $credit_record = $CreditMaster_model->getRecord($credit_id);
            echo json_encode($credit_record);
        }die;
    }

    public function ajaxGetBatchCodeAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            $Academic_model = new Application_Model_Academic();
            $batch_record = $Academic_model->getBatchCodeRecord($academic_id);
            echo json_encode($batch_record);
        }die;
    }

    public function ajaxGetTotalCreditAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            $term_id = $this->_getParam("term_id");
            if ($academic_id && $term_id) {
                $TermMaster_model = new Application_Model_TermMaster();
                $result = $TermMaster_model->getTotalAllotedCredits($academic_id, $term_id);
                echo json_encode($result);
            }die;
        }
    }

    public function ajaxGetResultAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            $course_id = $this->_getParam("course_id");
            if ($academic_id && $course_id) {
                $ElectiveCourseLearning_model = new Application_Model_ElectiveCourseLearning();
                $data = $ElectiveCourseLearning_model->GetCourseCount($academic_id, $course_id);

                echo json_encode($data);
            }
        }die;
    }

// ramesh  date disable	  
    public function ajaxGetTermLastdateAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            //print_r($academic_id);die;
            $term_model = new Application_Model_TermMaster();
            $lastdate_record = $term_model->getlastdateRecord($academic_id);
            echo json_encode($lastdate_record);
        }die;
    }

//Sailaja date edit	 
    public function ajaxGetTermLastdateEditAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            $term_id = $this->_getParam("term_id");
            //print_r($academic_id);die;
            $term_model = new Application_Model_TermMaster();
            $lastdate_record = $term_model->getPreviousidRecord($academic_id, $term_id);
            echo json_encode($lastdate_record);
        }die;
    }

    public function ajaxGetAcademicYearAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_id");
            $Academic_model = new Application_Model_Academic();
            $batch_year = $Academic_model->getyearRecord($academic_id);
            echo json_encode($batch_year);
        }die;
    }

//Reference Grade Master
    public function referenceGradeAction() {
        $this->view->action_name = 'Reference Grade Master';
        $this->view->sub_title_name = 'Reference Grade Master';
        $this->accessConfig->setAccess('SA_ACAD_REF_GRADE');
        $ReferenceGradeMaster_model = new Application_Model_ReferenceGradeMaster();
        $ReferenceGradeMaster_form = new Application_Form_ReferenceGradeMaster();
        $ReferenceGradeMasterItems_model = new Application_Model_ReferenceGradeMasterItems();
        $reference_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ReferenceGradeMaster_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($ReferenceGradeMaster_form->isValid($this->getRequest()->getPost())) {
                        $data = $ReferenceGradeMaster_form->getValues();
                        $last_insert_id = $ReferenceGradeMaster_model->insert($data);
                        $grade = $this->getRequest()->getPost('grade');

                        foreach (array_filter($grade['letter_grade']) as $k => $letter_grade) {
                            $grade_data['reference_id'] = $last_insert_id;
                            $grade_data['letter_grade'] = $letter_grade;
                            $grade_data['number_grade'] = $grade['number_grade'][$k];

                            $ReferenceGradeMasterItems_model->insert($grade_data);
                        }
                        $this->_flashMessenger->addMessage('Grades Added Successfully ');
                        $this->_redirect('master/reference-grade');
                    }
                }
                break;
            case 'edit':
                $result = $ReferenceGradeMaster_model->getRecord($reference_id);
                $ReferenceGradeMaster_form->populate($result);
                $this->view->result = $result;
                $item_result = $ReferenceGradeMasterItems_model->getRecords($reference_id);
                $this->view->item_result = $item_result;
                if ($this->getRequest()->isPost()) {
                    if ($ReferenceGradeMaster_form->isValid($this->getRequest()->getPost())) {
                        $data = $ReferenceGradeMaster_form->getValues();

                        $ReferenceGradeMaster_model->update($data, array('reference_id=?' => $reference_id));
                        $grade = $this->getRequest()->getPost('grade');
                        $ReferenceGradeMasterItems_model->trashItems($reference_id);
                        foreach (array_filter($grade['letter_grade']) as $k => $letter_grade) {
                            $grade_data['reference_id'] = $reference_id;
                            $grade_data['letter_grade'] = $letter_grade;
                            $grade_data['number_grade'] = $grade['number_grade'][$k];
                            $ReferenceGradeMasterItems_model->insert($grade_data);
                        }
                        $this->_flashMessenger->addMessage('Grades Updated Successfully');
                        $this->_redirect('master/reference-grade');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($reference_id) {
                    $ReferenceGradeMaster_model->update($data, array('reference_id=?' => $reference_id));
                    $this->_flashMessenger->addMessage('Grades Deleted Successfully');
                    $this->_redirect('master/reference-grade');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ReferenceGradeMaster_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function ajaxGetExistingYearAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_id = $this->_getParam("academic_year_id");
            $ReferenceGradeMaster_model = new Application_Model_ReferenceGradeMaster();
            $result = $ReferenceGradeMaster_model->getExitstingRecord($academic_id);
            echo json_encode($result);
        }die;
    }

    public function ajaxGetTermsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $TermMaster_modelss = new Application_Model_TermMaster();
            $term_result = $TermMaster_modelss->getTermDropDownList($academic_year_id);
            echo '<option value="">Select </option>';
            foreach ($term_result as $k => $val) {
                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }
        exit;
    }
    public function ajaxGetCreditValueAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $credit_id = $this->_getParam("credit_id");
            $CreditMaster_model = new Application_Model_CreditMaster();
            $data = $CreditMaster_model->getCourseCreditById($credit_id);
            echo '<option value="">Select </option>';
            foreach ($data as $k => $val) {
                echo '<option value="' . $val['credit_id'] . '" >' . $val['credit_value'] . '</option>';
            }
        }
        exit;
    }

    public function ajaxGetTermsDefaultAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $TermMaster_model = new Application_Model_TermMaster();
            $term_result = $TermMaster_model->getTermDropDownList($academic_year_id);
            echo "<option value =''>--Select--</option>";
            foreach ($term_result as $k => $val) {
                echo '<option value="' . $k . '" >' . $val . '</option>';
            }
        }
        exit;
    }

    public function ajaxGetExperientialLearningAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $ExperientialLearning_model = new Application_Model_ExperientialLearning();
            $el_result = $ExperientialLearning_model->getProgram($academic_year_id);
            echo '<option value="">Select </option>';
            foreach ($el_result as $k => $val) {
                echo '<option value="' . $val['elc_id'] . '" >' . $val['elc_name'] . '</option>';
            }
        }
        exit;
    }

    public function ajaxIsGradeAllocatedAction() {
        $this->_helper->layout->disableLayout();
        $batch_id = $this->_getParam("batch_id");
        $term_id = $this->_getParam("term_id");
        $course_id = $this->_getParam("course_id");
        $faculty_id = $this->_getParam("faculty_id");
        $GradeAllocation = new Application_Model_GradeAllocation();
        $isGradeAllocated = $GradeAllocation->isGradeAllocated($batch_id, $faculty_id, $term_id, $course_id);
        if ($isGradeAllocated) {
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }

    public function ajaxIsElGradeAllocatedAction() {
        $this->_helper->layout->disableLayout();
        $batch_id = $this->_getParam("batch_id");
        $course_id = $this->_getParam("course_id");
        $faculty_id = $this->_getParam("faculty_id");
        $ELAllocation_model = new Application_Model_ExperientialGradeAllocation();
        $isGradeExist = $ELAllocation_model->isGradeAllocated($batch_id, $faculty_id, $course_id);
        if ($isGradeExist) {
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }

    public function ajaxGetTermYearBatchAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $batch_id = $this->_getParam("batch_id");
            $year_id = $this->_getParam("year_id");
            $Term_model = new Application_Model_TermMaster();
            $result = $Term_model->getTermRecordsByYear($batch_id, $year_id);
            echo '<option value="">Select </option>';
            foreach ($result as $row) {
                echo '<option value="' . $row['term_id'] . '" >' . $row['term_name'] . '</option>';
            }
        }
        exit;
    }

}
