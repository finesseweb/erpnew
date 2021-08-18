<?php


class Finance_OpeningBalancesController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		$this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'opening-balances';
		$this->view->sub_title_name = 'opening-balances';
       // $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$OpeningBalances_model = new Finance_Model_OpeningBalances();
		$OpeningBalances_form = new Finance_Form_OpeningBalances();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		//$this->view->TrailBalance_form = $TrailBalance_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->OpeningBalances_form = $OpeningBalances_form;
                if ($this->getRequest()->isPost()) {			
                    if ($OpeningBalances_form->isValid($this->getRequest()->getPost())) {						
                        $data = $OpeningBalances_form->getValues();
						$print = $this->getRequest()->getPost("print");
						
						$dealer_statement = $OpeningBalances_model->getCustomerStatement();
						$vendor_statement = $OpeningBalances_model->getVendorStatement();
						$bank_statement = $OpeningBalances_model->getBankStatement();
						$account_statement = $OpeningBalances_model->getAccountStatement();
						$this->view->dealer_statement = $dealer_statement;
						$this->view->vendor_statement = $vendor_statement;
						$this->view->bank_statement = $bank_statement;
						$this->view->account_statement = $account_statement;
						
						if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('opening-balances/pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Opening Balance");
						}
		
						
                }
				}else{
					$this->_redirect('finance/opening-balances');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = '2014-04-01';
				$this->view->enddate = $end_date = date("Y-m-d");
				//$data = array("start_date" => $start_date, "end_date" => $end_date);
				
				$dealer_statement = $OpeningBalances_model->getCustomerStatement();
				$vendor_statement = $OpeningBalances_model->getVendorStatement();
				$bank_statement = $OpeningBalances_model->getBankStatement();
				$account_statement = $OpeningBalances_model->getAccountStatement();
				$this->view->dealer_statement = $dealer_statement;
				$this->view->vendor_statement = $vendor_statement;
				$this->view->bank_statement = $bank_statement;
				$this->view->account_statement = $account_statement;
				
                break;
        }
    }	
}
