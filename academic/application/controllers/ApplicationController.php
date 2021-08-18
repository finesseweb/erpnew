<?php

class ApplicationController extends Zend_Controller_Action {

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
        if ($data->role_id == 0)
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
        $this->view->action_name = 'application';
        $this->view->sub_title_name = 'application';
        $this->accessConfig->setAccess('SA_ACAD_APPLICATION_FORM');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        $assignment_model = new Application_Model_Application();
        $assignment_form = new Application_Form_Application();
        $student_assignment_model = new Application_Model_SubmitAssignment();
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
                       
                        $data = array('stu_id' => $this->getRequest()->getPost('stu_id'),
                            'batch_id' => $this->getRequest()->getPost('acad_id'),
                            'stu_name' => $this->getRequest()->getPost('stu_name_id'),
                            'dob' => date('Y-m-d',strtotime($date)),
                            'course_id' => !empty($course)?','.$course.',':null,
                            'course_id_b' => !empty($courseb)?','.$courseb.',':null,
                            'course_fee' => $this->getRequest()->getPost('course'),
                            // 'switch' => $this->getRequest()->getPost('switch'),

                            'course_fee_b' => $this->getRequest()->getPost('course_b'),
                            'term_id' => $this->getRequest()->getPost('term_id'),
                            'term_id_b' => $this->getRequest()->getPost('term_b_id'),
                            'total_fee' => $this->getRequest()->getPost('total_fee'),
                            'updated_date' => date('Y-m-d')
                        );
                        $result =  $assignment_model->getRecordsByBatch($data['stu_id'],$data['batch_id'], $data['term_id'], $data['term_id_b']);
                        if($result['res'] == 0 && $result['res2'] == 0 ){
                        $last_insert_id = $assignment_model->insert($data);
                        $_SESSION['message_class']='alert-success';
                        $message = 'Application form has successfully submitted';
                        }
                        else {
                                    $_SESSION['message_class']='alert-danger';
                                    $message ='Application form already exists';
                                }


                        $this->_flashMessenger->addMessage($message);

                        $this->_redirect('application/index');
                    }
                }


                break;
            case 'edit':
                $result = $assignment_model->getRecord($ec_id);
                $result['academic_year_id'] = $result['batch_id'];
                $result['dob'] = date('d/m/Y',strtotime($result['dob']));
                $_SESSION['application']['course_id'] = $result['course_id'];
                $_SESSION['application']['course_id_b'] = $result['course_id_b'];
                $_SESSION['application']['course'] = $result['course_fee'];
                $_SESSION['application']['course_b'] = $result['course_fee_b']; 
                $_SESSION['application']['term_id'] = $result['term_id'];
                $_SESSION['application']['term_id_b'] = $result['term_id_b'];

                $assignment_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if (isset($_POST)) {
                        $courseb = implode(',', $this->getRequest()->getPost('coursebBox'));
                        $course = implode(',', $this->getRequest()->getPost('courseBox'));
                        $date = str_replace('/', '-', $this->getRequest()->getPost('dob_id'));
                        $data = array('stu_id' => $this->getRequest()->getPost('stu_id'),
                            'batch_id' => $this->getRequest()->getPost('acad_id'),
                            'stu_name' => $this->getRequest()->getPost('stu_name_id'),
                            'dob' => date('Y-m-d',strtotime($date)),
                        'course_id' => !empty($course)?','.$course.',':null,
                            'course_id_b' => !empty($courseb)?','.$courseb.',':null,
                            'course_fee' => $this->getRequest()->getPost('course'),
                            'course_fee_b' => $this->getRequest()->getPost('course_b'),
                            'term_id' => !empty($this->getRequest()->getPost('term_id'))?$this->getRequest()->getPost('term_id'):0,
                            'term_id_b' => !empty($this->getRequest()->getPost('term_b_id'))?$this->getRequest()->getPost('term_b_id'):0,
                            'total_fee' => $this->getRequest()->getPost('total_fee'),
                            'updated_date' => date('Y-m-d')
                        );
                        
                        $assignment_model->update($data, array('application_id=?' => $ec_id));
                    }
                    $_SESSION['message_class']='alert-success';
                    $this->_flashMessenger->addMessage('Application Form has Successfully updated !');
                    $this->_redirect('application/index');
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
                $result = $assignment_model->getRecords();
                $i = 0;
                foreach ($result as $key){
                    
          
                    $result[$i]['batch_id'] = $assignment_model->getAcademic1($key['batch_id']);
                    $term_id = $key['term_id'];
                    $result[$i]['term_id'] = $assignment_model->getTerm($key['term_id']);
                    $result[$i]['term_id_b'] = $assignment_model->getTerm($key['term_id_b']);
                    $result[$i]['dept'] = explode('-', $result[$i]['batch_id'])[0];
                    $result[$i]['total'] = $assignment_model->getTotal($key['batch_id'],$key['term_id'],$key['term_id_b']);
                                  $result1[$i]['result'] =  $assignment_model->getRecordsPdm($key['batch_id'],$term_id);
                                  foreach($result1[$i]['result'] as $key1 => $value1){
                                     $result1[$i]['result'][$key1]['term_id'] = $assignment_model->getTerm($value1['term_id']);
                                     $result1[$i]['result'][$key1]['term_id_b'] = $assignment_model->getTerm($value1['term_id_b']);
                                    
                                  }
                    $i++;
             
                }
                
            // echo "<pre>";print_r($result);exit;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->subtable = $result1 ;
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function ajaxGetStudentDetailsAction() {

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $id = $this->_getParam("stu_id");
            $application = new Application_Model_Application();
            $result = $application->getRecordsByPdmId($id);
            echo json_encode($result);
            die;
        }
    }
    public function ajaxGetCourseFeeAction() {

        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam("batch_id");
            $application = new Application_Model_Coursefee();
            $result = $application->getFee($term_id,$batch_id,1);
            echo $result;
            die;
        }
    }
    public function ajaxGetBackCourseFeeAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $term_id = $this->_getParam("term_id");
            $batch_id = $this->_getParam("batch_id");
            $application = new Application_Model_Coursefee();
            $result = $application->getFee($term_id,$batch_id,2);
            echo $result;
            die;
        }
    }

}
