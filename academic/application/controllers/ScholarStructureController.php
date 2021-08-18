<?php

class ScholarStructureController extends Zend_Controller_Action {

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

    public function indexAction() {
        $this->view->action_name = 'Scholar Structure';
        $this->view->sub_title_name = 'Scholar Structure';
        $this->accessConfig->setAccess('SA_ACAD_SCHOLAR_SHIP');
      $FeeHeads_model = new Application_Model_ScholarStructure();
        $FeeHeads_form = new Application_Form_ScholarStructure();
       $Scholarship_model = new Application_Model_ScholarStructureItem();
        $feehead_id = $this->_getParam("id");
        //print_r($feehead_id);die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $FeeHeads_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($FeeHeads_form->isValid($this->getRequest()->getPost())) {
                        $data = $FeeHeads_form->getValues();
                       // print_r($data);exit;
                        //$last_insert_id = $FeeStructure_model->insert($data);
                       $scholar_ship_data = array(
                           'batch_id' => $data['batch_id'],
                           'term_id' =>0,
                           'gpa_from' => $data['gpa_from'],
                           'gpa_to' => $data['gpa_to'],
                           'scholarship_fee_wavier' => $data['scholarship_fee_wavier'] , 
                           'status' => 0
                        );
                        $Scholarship_model->insert($scholar_ship_data);
                        $this->_flashMessenger->addMessage('Fee Head Successfully added');

                        $this->_redirect('scholar-structure/index');
                    }
                }


                break;
            case 'edit':
                $result = $FeeHeads_model->getRecordById($feehead_id);
                $item_result = $Scholarship_model->getItemRecords($feehead_id);
                $this->view->item_result = $item_result;
                $FeeHeads_form->populate($result);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($FeeHeads_form->isValid($this->getRequest()->getPost())) {
                        $data = $FeeHeads_form->getValues();
                       // print_r($data);exit;
                        $Scholarship_model->update($data, array('id=?' => $feehead_id));

                        $Fee = $this->getRequest()->getPost('Fee');
                        //print_r($Fee);die;
                       // $Scholarship_model->trashItems($feehead_id); //Delete Fields in Company	
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('scholar-structure/index');
                    } else {
                        //$this->_redirect('fee-heads/index');						
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($feehead_id) {
                    $Scholarship_model->update($data, array('id=?' => $feehead_id));
                    //$FeeHeadItems_model->update($data, array('feehead_id=?' => $feehead_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('scholar-structure/index');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $FeeHeads_model->getRecords();
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
