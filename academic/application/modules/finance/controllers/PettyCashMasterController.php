<?php


class Finance_PettyCashMasterController extends My_MasterController { 
		
		public function init() {
			$this->globalFunction();
		}
	
		public function indexAction() {
        $this->view->module = "finance";		
        $this->view->action_name = 'index';		
        $this->view->controller = 'petty-cash-master';
		$this->view->sub_title_name == 'petty-master-master';
        $PettyCashMaster_model = new Finance_Model_PettyCashMaster();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$form = new Finance_Form_PettyCashMaster();
		
        switch ($type) {
            case "add":                
                $this->view->type = $type;
                $this->view->form = $form;
                if ($this->getRequest()->isPost()) {
                    if ($form->isValid($this->getRequest()->getPost())) {
                        $data = $form->getValues();
                        $PettyCashMaster_model->insert($data);
                        $this->_flashMessenger->addMessage('Successfully added');
                        $this->_redirect('finance/petty-cash-master/index');
                    }
                }
                break;
            case 'edit':
                $this->view->type = $type;
                $this->view->form = $form;
                $result = $PettyCashMaster_model->getRecord($id);
                $form->populate($result);
                if ($this->getRequest()->isPost()) {
                    if ($form->isValid($this->getRequest()->getPost())) {
                        $data = $form->getValues();
                        $PettyCashMaster_model->update($data, array('id=?' => $id));
                        $this->_flashMessenger->addMessage(' Successfully Updated');
                        $this->_redirect('finance/petty-cash-master/index');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($id)
                    $PettyCashMaster_model->update($data, array('id=?' => $id));
                $this->_flashMessenger->addMessage(' Successfully Deleted');
                $this->_redirect('finance/petty-cash-master/index');
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $PettyCashMaster_model->getRecords();
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