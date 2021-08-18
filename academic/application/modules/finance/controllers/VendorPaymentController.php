<?php

class Finance_VendorPaymentController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {
        $this->view->module = "finance";
		$this->view->sub_title_name = "vendorpayment";
        $this->view->action_name = 'Finance';
        $this->view->controller = 'vendor-payment';
        $ErpFinanceVendorPayment_model = new Finance_Model_ErpFinanceVendorPayment();
        $vendor_payment_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        switch ($type) {
            case "add":
                $ErpFinanceVendorPayment_form = new Finance_Form_ErpFinanceVendorPayment();
                $this->view->type = $type;
                $this->view->ErpFinanceVendorPayment_form = $ErpFinanceVendorPayment_form;
                if ($this->getRequest()->isPost()) {
                    $payment_by = $this->getRequest()->getPost("payment_by");
                    $ErpFinanceVendorPayment_form = $this->validateBankDetails($ErpFinanceVendorPayment_form, $payment_by);
                    if ($ErpFinanceVendorPayment_form->isValid($this->getRequest()->getPost())) {
                        $data = $ErpFinanceVendorPayment_form->getValues();
                        $data = $this->bankdetails($data);
                        $data['added_date'] = date("Y-m-d h:i:s");
                       $result = $ErpFinanceVendorPayment_model->insert($data);
						if($result)
						{
							$check_id = $data['check_id'];
							$purchase_invoice_id = $data['purchase_invoice_id'];
							$details['check_status'] = 2;		
							//echo '<pre>'; print_r($data);die;
							$ErpBankCheckMaster_model = new Application_Model_ErpBankChequeMaster;	
								if($purchase_invoice_id)
							$ErpBankCheckMaster_model->update($details, array('check_id=?' => $check_id));
						}
                        $this->_flashMessenger->addMessage('Finance Successfully added');
                        $this->_redirect('finance/vendor-payment/index');
                    }
                }
                break;
            case 'edit':
                $ErpFinanceVendorPayment_form = new Finance_Form_ErpFinanceVendorPayment();
                $this->view->type = $type;
                $this->view->ErpFinanceVendorPayment_form = $ErpFinanceVendorPayment_form;
                $result = $ErpFinanceVendorPayment_model->getRecord($vendor_payment_id);
                $data = array();
                if (strlen($result['purchase_invoice_id']) == 1) {
                    $data[$result['purchase_invoice_id']] = @(PI_PREFIX) . "000" . $result['purchase_invoice_id'];
                } else
                if (strlen($result['purchase_invoice_id']) == 2) {
                    $data[$result['purchase_invoice_id']] = @(PI_PREFIX) . "00" . $result['purchase_invoice_id'];
                } else
                if (strlen($result['purchase_invoice_id']) == 3) {
                    $data[$result['purchase_invoice_id']] = @(PI_PREFIX) . "0" . $result['purchase_invoice_id'];
                } else {
                    $data[$result['purchase_invoice_id']] = @(PI_PREFIX) . "" . $result['purchase_invoice_id'];
                }
                $ErpFinanceVendorPayment_form->getElement("purchase_invoice_id")
                        ->setAttrib('readonly', 'readonly')
                        ->setAttrib('class', array('form-control'))
                        ->setMultiOptions($data);
                $ErpFinanceVendorPayment_form->populate($result);
                if ($this->getRequest()->isPost()) {
                    $payment_by = $this->getRequest()->getPost("payment_by");
                    $ErpFinanceVendorPayment_form = $this->validateBankDetails($ErpFinanceVendorPayment_form, $payment_by);
                    if ($ErpFinanceVendorPayment_form->isValid($this->getRequest()->getPost())) {
                        $data = $ErpFinanceVendorPayment_form->getValues();
                        $data = $this->bankdetails($data);
//                        echo '<pre>';
//                        print_r($data);
//                        exit;
                        $ErpFinanceVendorPayment_model->update($data, array('vendor_payment_id=?' => $vendor_payment_id));
                        $this->_flashMessenger->addMessage('Finance Updated Successfully');
                        $this->_redirect('finance/vendor-payment/index');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($vendor_payment_id)
                    $ErpFinanceVendorPayment_model->update($data, array('vendor_payment_id=?' => $vendor_payment_id));
                $this->_flashMessenger->addMessage('Finance Deleted Successfully');
                $this->_redirect('finance/vendor-payment/index');
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ErpFinanceVendorPayment_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    private function bankdetails($data) {
        $payment_by = $data['payment_by'];
        if ($payment_by == 1) {
            $data['bank_id'] = 1;
            $data['check_id'] = '';
            $data['dd_no'] = '';
//            unset($data['bank_id']);
//            unset($data['check_id']);
//            unset($data['dd_no']);
        }
        if ($payment_by == 2) {
//            unset($data['dd_no']);
            $data['dd_no'] = '';
        }
        if ($payment_by == 3) {

//            empty($data['check_id']);
            $data['check_id'] = '';
        }
        return $data;
    }

    private function validateBankDetails($ErpFinanceCustomerPayouts_form, $payment_by) {
        if ($payment_by == 1) {
            $ErpFinanceCustomerPayouts_form->check_id->setRequired(false)->setValidators(array());
            $ErpFinanceCustomerPayouts_form->bank_id->setRequired(false)->setValidators(array());
            $ErpFinanceCustomerPayouts_form->dd_no->setRequired(false)->setValidators(array());
            return $ErpFinanceCustomerPayouts_form;
        }
        if ($payment_by == 2) {
            $ErpFinanceCustomerPayouts_form->dd_no->setRequired(false)->setValidators(array());
            return $ErpFinanceCustomerPayouts_form;
        }
        if ($payment_by == 3) {
            $ErpFinanceCustomerPayouts_form->check_id->setRequired(false)->setValidators(array());
            return $ErpFinanceCustomerPayouts_form;
        }
    }
	
	public function vendorPaymentViewAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {
			$this->view->module = "finance";
			$this->view->action_name = 'index';
			$this->view->controller = 'journal-ledger';
			$vendor_payment_id = $this->_getParam("name");
			$ErpFinanceVendorPayment_model = new Finance_Model_ErpFinanceVendorPayment();
			$result = $ErpFinanceVendorPayment_model->getRecord($vendor_payment_id);
			//echo '<pre>'; print_r($result); die;
			$this->view->result = $result;
			$this->_helper->viewRenderer("vendor-payment-view");         	
        }            
	}

	public function ajaxVendorchequeloadingAction() {
	
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');
        if ($id != '') {          
            $model = new Application_Model_ErpBankChequeMaster();
            $result = $model->getCheque($id);
			//print_r($result); die;
            $this->view->results = $result;
        }
    }
}