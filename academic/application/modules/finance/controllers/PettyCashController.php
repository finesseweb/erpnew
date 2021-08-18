<?php


class Finance_PettyCashController extends My_MasterController {

    public function init() {
			$this->globalFunction();
		}
	
		public function indexAction() {
        $this->view->module = "finance";		
        $this->view->action_name = 'index';		
        $this->view->controller = 'petty-cash';
		$this->view->sub_title_name == 'petty-cash';

        $PettyCash_model = new Finance_Model_PettyCash();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$PettyCash_form = new Finance_Form_PettyCash();
		
        switch ($type) {
            case "add":                
                $this->view->type = $type;
                $this->view->form = $PettyCash_form;
                if ($this->getRequest()->isPost()) {
                    if ($PettyCash_form->isValid($this->getRequest()->getPost())) {
                        $data = $PettyCash_form->getValues();
                        $PettyCash_model->insert($data);
                        $this->_flashMessenger->addMessage('Bank Details Successfully added');
                        $this->_redirect('finance/petty-cash/index');
                    }
                }
                break;
            case 'edit':
                $this->view->type = $type;
                $this->view->form = $PettyCash_form;
                $result = $PettyCash_model->getRecord($id);
                $PettyCash_form->populate($result);
                if ($this->getRequest()->isPost()) {
                    if ($PettyCash_form->isValid($this->getRequest()->getPost())) {
                        $data = $PettyCash_form->getValues();
                        $PettyCash_model->update($data, array('petty_cash_transaction_id=?' => $id));
                        $this->_flashMessenger->addMessage('Bank Details Updated Successfully');
                        $this->_redirect('finance/petty-cash/index');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($id)
                    $PettyCash_model->update($data, array('petty_cash_transaction_id=?' => $id));
                $this->_flashMessenger->addMessage('Bank Details Deleted Successfully');
                $this->_redirect('finance/petty-cash/index');
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $PettyCash_model->getRecords();
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
