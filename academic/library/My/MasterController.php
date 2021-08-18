<?php

class My_MasterController extends Zend_Controller_Action {

    protected $_params = "";
    protected $_module = "";
    protected $_controller = "";
    protected $_action = "";
    protected $_db = null;
    protected $_flashMessenger = null;
    protected $_authontication = null;
    protected $_act = null;

    public function globalFunction() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;
        $this->_params = $this->getRequest()->getParams();
        $this->_module = $this->_params['module'];
        $this->_controller = $this->_params['controller'];
        $this->_action = $this->_params['action'];

        if ($this->_module == "login") {
            $this->_helper->layout->setLayout("loginlayout");
        } else {
            $this->_helper->layout->setLayout("layout");
        }
        //$this->_act = new Application_Model_Adminactions();
        $this->authonticate();
        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->view->authontication = $this->_authontication;
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        if (!$data && $this->_module != 'login') {
            $this->_redirect('index/login');
            return;
        }
        $this->_authontication = $data;
    }

}
