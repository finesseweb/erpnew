<?php


class Finance_CustomerPayoutsController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {
        $this->view->module = "finance";
        $this->view->action_name = 'Finance';
        $this->view->controller = 'customer-payouts';
        $ErpFinanceCustomerPayouts_model = new Finance_Model_ErpFinanceCustomerPayouts();
        $cp_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        switch ($type) {
            case "add":
                $ErpFinanceCustomerPayouts_form = new Finance_Form_ErpFinanceCustomerPayouts();
                $this->view->type = $type;
                $this->view->ErpFinanceCustomerPayouts_form = $ErpFinanceCustomerPayouts_form;
                if ($this->getRequest()->isPost()) {
                    $payment_by = $this->getRequest()->getPost("payment_by");
                    $ErpFinanceCustomerPayouts_form = $this->validateBankDetails($ErpFinanceCustomerPayouts_form, $payment_by);

                    if ($ErpFinanceCustomerPayouts_form->isValid($this->getRequest()->getPost())) {
                        $data = $ErpFinanceCustomerPayouts_form->getValues();
                        $data = $this->bankdetailsObject($data);
                        $data['added_date'] = date("Y-m-d h:i:s");
                        $ErpFinanceCustomerPayouts_model->insert($data);
                        $this->_flashMessenger->addMessage('Finance Successfully added');
                        $this->_redirect('finance/customer-payouts/index');
                    }
                }
                break;
            case 'edit':
                $ErpFinanceCustomerPayouts_form = new Finance_Form_ErpFinanceCustomerPayouts();
                $this->view->type = $type;
                $this->view->ErpFinanceCustomerPayouts_form = $ErpFinanceCustomerPayouts_form;
                $result = $ErpFinanceCustomerPayouts_model->getRecord($cp_id);
                $bankdetails = (array) json_decode($result['bank_details_object']);
                $result = array_merge($result, $bankdetails);
                $data = array();
                if (strlen($result['tyre_invoice_id']) == 1) {
                    $data[$result['tyre_invoice_id']] = @(SALE_INVOICE) . "000" . $result['tyre_invoice_id'];
                } else
                if (strlen($result['tyre_invoice_id']) == 2) {
                    $data[$result['tyre_invoice_id']] = @(SALE_INVOICE) . "00" . $result['tyre_invoice_id'];
                } else
                if (strlen($result['tyre_invoice_id']) == 3) {
                    $data[$result['tyre_invoice_id']] = @(SALE_INVOICE) . "0" . $result['tyre_invoice_id'];
                } else {
                    $data[$result['tyre_invoice_id']] = @(SALE_INVOICE) . "" . $result['tyre_invoice_id'];
                }
                $ErpFinanceCustomerPayouts_form->getElement("tyre_invoice_id")
                        ->setAttrib('readonly', 'readonly')
                        ->setAttrib('class', array('form-control'))
                        ->setMultiOptions($data);
                $ErpFinanceCustomerPayouts_form->populate($result);
                if ($this->getRequest()->isPost()) {
                    $payment_by = $this->getRequest()->getPost("payment_by");
                    $ErpFinanceCustomerPayouts_form = $this->validateBankDetails($ErpFinanceCustomerPayouts_form, $payment_by);
                    if ($ErpFinanceCustomerPayouts_form->isValid($this->getRequest()->getPost())) {

                        $data = $ErpFinanceCustomerPayouts_form->getValues();

                        $data = $this->bankdetailsObject($data);
                        $ErpFinanceCustomerPayouts_model->update($data, array('cp_id=?' => $cp_id));
                        $this->_flashMessenger->addMessage('Finance Updated Successfully');
                        $this->_redirect('finance/customer-payouts/index');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($cp_id)
                    $ErpFinanceCustomerPayouts_model->update($data, array('cp_id=?' => $cp_id));
                $this->_flashMessenger->addMessage('Finance Deleted Successfully');
                $this->_redirect('finance/customer-payouts/index');
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ErpFinanceCustomerPayouts_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    private function bankdetailsObject($data) {
        $payment_by = $data['payment_by'];
        if ($payment_by == 1) {
            unset($data['bank_name']);
            unset($data['check_no']);
            unset($data['dd_no']);
            $data['bank_details_object'] = "";
        }
        if ($payment_by == 2) {
            $bank_details_object = new stdClass();
            $bank_details_object->bank_name = $data['bank_name'];
            $bank_details_object->check_no = $data['check_no'];
            unset($data['bank_name']);
            unset($data['check_no']);
            $data['bank_details_object'] = json_encode($bank_details_object);
            unset($data['dd_no']);
        }
        if ($payment_by == 3) {
            $bank_details_object = new stdClass();
            $bank_details_object->bank_name = $data['bank_name'];
            $bank_details_object->dd_no = $data['dd_no'];
            unset($data['bank_name']);
            unset($data['dd_no']);
            $data['bank_details_object'] = json_encode($bank_details_object);
            unset($data['check_no']);
        }
        return $data;
    }

    private function validateBankDetails($ErpFinanceCustomerPayouts_form, $payment_by) {
        if ($payment_by == 1) {
            $ErpFinanceCustomerPayouts_form->check_no->setRequired(false)->setValidators(array());
            $ErpFinanceCustomerPayouts_form->bank_name->setRequired(false)->setValidators(array());
            $ErpFinanceCustomerPayouts_form->dd_no->setRequired(false)->setValidators(array());
            return $ErpFinanceCustomerPayouts_form;
        }
        if ($payment_by == 2) {
            $ErpFinanceCustomerPayouts_form->dd_no->setRequired(false)->setValidators(array());
            return $ErpFinanceCustomerPayouts_form;
        }
        if ($payment_by == 3) {
            $ErpFinanceCustomerPayouts_form->check_no->setRequired(false)->setValidators(array());
            return $ErpFinanceCustomerPayouts_form;
        }
    }

	public function customerPayoutsViewAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {
			$this->view->module = "finance";
			$this->view->action_name = 'index';
			$this->view->controller = 'journal-ledger';
			$cp_id = $this->_getParam("name");		
			$ErpFinanceCustomerPayouts_model = new Finance_Model_ErpFinanceCustomerPayouts();
			$result = $ErpFinanceCustomerPayouts_model->getRecord($cp_id);
			$bankdetails = (array) json_decode($result['bank_details_object']);
                $result = array_merge($result, $bankdetails);
			//echo '<pre>'; print_r($result); die;
			$this->view->result = $result;
			$this->_helper->viewRenderer("customer-payouts-view");         	
        }
		
		
		
	}
}