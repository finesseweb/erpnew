<?php

class ParticipantAssignmentController extends Zend_Controller_Action {

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
	

        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();

        $this->_act = new Application_Model_Adminactions();

        $this->_db = Zend_Db_Table::getDefaultAdapter();

    }
    protected function authonticate() {

        $storage = new Zend_Session_Namespace("admin_login");

        $data = $storage->admin_login;
        if($data->role_id == 0)
            $this->_redirect('student-portal/assignments');
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
		$this->view->action_name = 'participant assignment';
        $this->view->sub_title_name = 'participant assignment';
        $this->accessConfig->setAccess('SA_ACAD_ASSIGNMENT');
 
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
		$ParticipantAssignment_form = new Application_Form_ParticipantAssignment();
                $student_assignment_model = new Application_Model_SubmitAssignment();
        $type = $this->_getParam("type");
		$this->view->type = $type;
        $this->view->form = $ParticipantAssignment_form;
		
        

    }

    
	public function ajaxGetAssignmentViewAction()
	{  $student_assignment_model = new Application_Model_SubmitAssignment();
            $student_info = new Application_Model_StudentPortal();
		$this->_helper->layout->disableLayout();
		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
                    
                    $term_id = $this->_getParam('term_id');
                    $batch_id = $this->_getParam('batch');
                    $course_id = $this->_getParam("course_id");
                    if($this->login_storage->empl_id){
                             $result = $student_assignment_model->getstudentsbyEmpl($batch_id, $term_id, $course_id);     
                    }
                    else
                    {
                         $result = $student_assignment_model->getstudentsWithoutEmpl($batch_id, $term_id, $course_id);    
                    }
                    
                    foreach($result as $key => $value){
                        $file_arr = explode('/', $value['upload_file']);
            $file_name = explode(".", $file_arr[count($file_arr) - 1]);
            $result[$key]['filename1'] = $file_name[0];
                        $result[$key]['student_id'] = $student_info->getStudenInfo($value['student_id'])['stu_id'];
                        $result[$key]['stu_name'] = $student_info->getStudenInfo($value['student_id'])['stu_fname'];
                    }
                  // echo "<pre>"; print_r($result);exit;
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
            echo '<option value="">Select</option>';
            foreach ($result as $value) {
                echo '<option value="' . $value['course_id'] . '" >' . $value['course_code'] . '</option>';
            }
        }die;
    }

        
        
        

}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

