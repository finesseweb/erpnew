     <?php
class JobAnnouncementController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    Private $_unit_id = null;
        private $cur_datetime;
        public $year_id = array(
            1 => 'First Year',
            2 => 'Second Year'        
        );
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
        if( isset($data) ){
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
        $this->cur_datetime = date('Y-m-d H:i:s');
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
           if($data->role_id == 0)
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
  

 public function jobAnnouncementAction()
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

}