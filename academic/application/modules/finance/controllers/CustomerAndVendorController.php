<?php


class Finance_CustomerAndVendorController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		$this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'customer-vendor';
		$this->view->sub_title_name = 'customer-vendor';
        $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$CustomerAndVendor_model = new Finance_Model_CustomerAndVendor();
		$CustomerAndVendor_form = new Finance_Form_CustomerAndVendor();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->CustomerAndVendor_form = $CustomerAndVendor_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->CustomerAndVendor_form = $CustomerAndVendor_form;
                if ($this->getRequest()->isPost()) {			
                    if ($CustomerAndVendor_form->isValid($this->getRequest()->getPost())) {						
                        $data = $CustomerAndVendor_form->getValues();
						$this->view->searchby = $data['search_by'];
						$print = $this->getRequest()->getPost("print");
						//print_r($print); die;
						$CustomerAndVendor_form->populate($data);
						
						$getVendorStatement = $CustomerAndVendor_model->getVendorStatement($data);
						$dealer_statement = $CustomerAndVendor_model->getCustomerStatement($data);	
						$this->view->vendor_statement = $getVendorStatement;		
						$this->view->dealer_statement = $dealer_statement;
						if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('customer-and-vendor/customer-and-vendor-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Customer And Vendor Balance Report");
					  }
                }
				}else{
					$this->_redirect('finance/customer-and-vendor');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = date('Y').'-04-01';
				$this->view->enddate = $end_date = (date('Y')+1).'-03-31';
				$data = array("start_date" => $start_date, "end_date" => $end_date);
				
				//$print = $this->getRequest()->getPost("print");
						//print_r($print); die;
				
				$getVendorStatement = $CustomerAndVendor_model->getVendorStatement($data);
				$dealer_statement = $CustomerAndVendor_model->getCustomerStatement($data);
						
				$this->view->vendor_statement = $getVendorStatement;		
				$this->view->dealer_statement = $dealer_statement;
				
	
                break;
        }
    }	
}
