<?php


class Finance_BankDetailsController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {
        $this->view->module = "finance";
		$this->view->sub_title_name == 'bank-details';
        $this->view->action_name = 'Finance';		
        $this->view->controller = 'bank-details';

        $BankDetails_model = new Finance_Model_BankDetails();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$BankDetails_form = new Finance_Form_BankDetails();
		
        switch ($type) {
            case "add":                
                $this->view->type = $type;
                $this->view->form = $BankDetails_form;
                if ($this->getRequest()->isPost()) {
                    if ($BankDetails_form->isValid($this->getRequest()->getPost())) {
                        $data = $BankDetails_form->getValues();
                        $BankDetails_model->insert($data);
                        $this->_flashMessenger->addMessage('Bank Details Successfully added');
                        $this->_redirect('finance/bank-details/index');
                    }
                }
                break;
            case 'edit':
                $this->view->type = $type;
                $this->view->form = $BankDetails_form;
                $result = $BankDetails_model->getRecord($id);
                $BankDetails_form->populate($result);
                if ($this->getRequest()->isPost()) {
                    if ($BankDetails_form->isValid($this->getRequest()->getPost())) {
                        $data = $BankDetails_form->getValues();
                        $BankDetails_model->update($data, array('fb_id=?' => $id));
                        $this->_flashMessenger->addMessage('Bank Details Updated Successfully');
                        $this->_redirect('finance/bank-details/index');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($id)
                    $BankDetails_model->update($data, array('fb_id=?' => $id));
                $this->_flashMessenger->addMessage('Bank Details Deleted Successfully');
                $this->_redirect('finance/bank-details/index');
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $BankDetails_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	
	public function customerAction() {
        $this->view->module = "finance";
		$this->view->sub_title_name == 'bank-customer';
        $this->view->action_name = 'customer';
        $this->view->controller = 'bank-details';

        $BankDetails_model = new Finance_Model_CustomerBankDetails();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$BankDetails_form = new Finance_Form_CustomerBankDetails();
		
        switch ($type) {
            case "add":                
                $this->view->type = $type;
                $this->view->form = $BankDetails_form;
                if ($this->getRequest()->isPost()) {
                    if ($BankDetails_form->isValid($this->getRequest()->getPost())) {
                        $data = $BankDetails_form->getValues();
                        $BankDetails_model->insert($data);
                        $this->_flashMessenger->addMessage('Bank Details Successfully added');
                        $this->_redirect('finance/bank-details/customer');
                    }
                }
                break;
            case 'edit':
                $this->view->type = $type;
                $this->view->form = $BankDetails_form;
                $result = $BankDetails_model->getRecord($id);
                $BankDetails_form->populate($result);
                if ($this->getRequest()->isPost()) {
                    if ($BankDetails_form->isValid($this->getRequest()->getPost())) {
                        $data = $BankDetails_form->getValues();
                        $BankDetails_model->update($data, array('cus_id=?' => $id));
                        $this->_flashMessenger->addMessage('Bank Details Updated Successfully');
                        $this->_redirect('finance/bank-details/customer');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($id)
                    $BankDetails_model->update($data, array('cus_id=?' => $id));
                $this->_flashMessenger->addMessage('Bank Details Deleted Successfully');
                $this->_redirect('finance/bank-details/customer');
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $BankDetails_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	
	public function vendorAction() {
        $this->view->module = "finance";
		$this->view->sub_title_name == 'bank-vendor';
        $this->view->action_name = 'vendor';
        $this->view->controller = 'bank-details';

        $BankDetails_model = new Finance_Model_VendorBankDetails();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$BankDetails_form = new Finance_Form_VendorBankDetails();
		
        switch ($type) {
            case "add":                
                $this->view->type = $type;
                $this->view->form = $BankDetails_form;
                if ($this->getRequest()->isPost()) {
                    if ($BankDetails_form->isValid($this->getRequest()->getPost())) {
                        $data = $BankDetails_form->getValues();
                        $BankDetails_model->insert($data);
                        $this->_flashMessenger->addMessage('Customer Bank Details Successfully added');
                        $this->_redirect('finance/bank-details/vendor');
                    }
                }
                break;
            case 'edit':
                $this->view->type = $type;
                $this->view->form = $BankDetails_form;
                $result = $BankDetails_model->getRecord($id);
                $BankDetails_form->populate($result);
                if ($this->getRequest()->isPost()) {
                    if ($BankDetails_form->isValid($this->getRequest()->getPost())) {
                        $data = $BankDetails_form->getValues();
                        $BankDetails_model->update($data, array('ven_id=?' => $id));
                        $this->_flashMessenger->addMessage('Customer Bank Details Updated Successfully');
                        $this->_redirect('finance/bank-details/vendor');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($id)
                    $BankDetails_model->update($data, array('ven_id=?' => $id));
                $this->_flashMessenger->addMessage('Customer Bank Details Deleted Successfully');
                $this->_redirect('finance/bank-details/vendor');
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $BankDetails_model->getRecords();
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
