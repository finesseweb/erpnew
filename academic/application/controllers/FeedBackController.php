<?php

class FeedBackController extends Zend_Controller_Action {

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
        if($data->role_id == 0)
            $this->_redirect('student-portal/feed-back');
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
        $this->view->action_name = 'FeedBack';
        $this->view->sub_title_name = 'feed-back';
        $this->accessConfig->setAccess('SA_ACAD_FEED_TEMPLATE');
        $FeedBack_model = new Application_Model_FeedBack();
        $FeedBack_form = new Application_Form_FeedBackElement();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $FeedBack_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($FeedBack_form->isValid($this->getRequest()->getPost())) {

                        $data = $FeedBack_form->getValues();
                      // print_r($data);exit;
                        unset($data['Auto_no1']);
                        $data['selected_question'] = implode(",", $this->getRequest()->getPost('questionBox'));
                       
                        $last_insert_id = $FeedBack_model->insert($data);

                        $this->_flashMessenger->addMessage('Evaluation  Components Successfully added');

                        $this->_redirect('feed-back/index');
                    }
                }


                break;
            case 'edit':
                $result = $FeedBack_model->getRecordById($ec_id);
                $result['Auto_no1'] = $result['Auto_no'];


                $FeedBack_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($FeedBack_form->isValid($this->getRequest()->getPost())) {
                        $data = $FeedBack_form->getValues();
                        unset($data['Auto_no1']);
                        $data['selected_question'] = implode(",", $this->getRequest()->getPost('questionBox'));
                        $FeedBack_model->update($data, array('id=?' => $ec_id));
                    }
                    $this->_flashMessenger->addMessage('Rating Successfully updated !');
                    $this->_redirect('feed-back/index');
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($ec_id) {
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
                    $EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
                    $this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
                    $this->_redirect('assignment/index');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $FeedBack_model->getRecords();
                foreach ($result as $key => $value) {


                    if ($value['question_type'] == 1)
                        $result[$key]['question_type'] = 'Course';
                    else
                        $result[$key]['question_type'] = 'Instructor';
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

    public function GetCourse($term_id, $batch_id) {

        $course_details = new Application_Model_Attendance();

        $result = $course_details->getCourseDetails($term_id, $batch_id);

        $body[""] = 'Select';
        foreach ($result as $value) {
            $body[$value['course_id']] = $value['course_code'];
        }
        return $body;
    }

    public function ajaxGetQuestionAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $question_id = $this->_getParam('question_Id');
            $Questionnaire_model = new Application_Model_Questionnaire();
            $result = $Questionnaire_model->getAllQuestionByQuestionType($question_id);
             echo '<option value="">Select </option>';
                foreach ($result as $k => $val) {
                    echo '<option value="' . $val['Auto_no'] . '" >' . $val['text_filed'] . '</option>';
                }die;
            
        }
    }
}
