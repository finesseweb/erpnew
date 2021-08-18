<?php


class Finance_TrailBalanceController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		$this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'trail-balance';
		$this->view->sub_title_name = 'trail-balance';
        $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$TrailBalance_model = new Finance_Model_TrailBalance();
		$TrailBalance_form = new Finance_Form_TrailBalance();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->TrailBalance_form = $TrailBalance_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->TrailBalance_form = $TrailBalance_form;
                if ($this->getRequest()->isPost()) {			
                    if ($TrailBalance_form->isValid($this->getRequest()->getPost())) {						
                        $data = $TrailBalance_form->getValues();
						$print = $this->getRequest()->getPost("print");
						//print_r($print); die;
						$TrailBalance_form->populate($data);
						$account_statement = $TrailBalance_model->getAccountStatement($data);
						$getBankStatement = $TrailBalance_model->getBankStatement($data);
						$getVendorStatement = $TrailBalance_model->getVendorStatement($data);
						$dealer_statement = $TrailBalance_model->getCustomerStatement($data);
						$vat_statement = $TrailBalance_model->getAccountStatementByDatesForPurchaseVat($data);
						$cst_statement	= $TrailBalance_model->getAccountStatementByDatesForSalesCST($data);
						$sales_statement = $TrailBalance_model->getAccountStatementByDatesForSalesAccount($data);
						$cash_statement = $TrailBalance_model->getAccountStatementByDatesForCash($data);
						$purchase_statement = $TrailBalance_model->getAccountStatementByDatesForPurchase($data);
						//echo '<pre>';print_r($cash_statement);die;	
						$this->view->account_statement = $account_statement;		
						$this->view->bank_statement = $getBankStatement;		
						$this->view->vendor_statement = $getVendorStatement;		
						$this->view->dealer_statement = $dealer_statement;
						$this->view->vat_statement = $vat_statement;
						$this->view->cst_statement = $cst_statement;
						$this->view->cash_statement = $cash_statement;
						$this->view->purchase_statement = $purchase_statement;
						$this->view->sales_statement = $sales_statement;
						if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('trail-balance/trial-balance-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Trial Balance Report");
					  }
                }
				}else{
					$this->_redirect('finance/trail-balance');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = '2015-04-01';
				$this->view->enddate = $end_date = date("Y-m-d");
				$data = array("start_date" => $start_date, "end_date" => $end_date);
				
				$print = $this->getRequest()->getPost("print");
						//print_r($print); die;
				$account_statement = $TrailBalance_model->getAccountStatement($data);
				$getBankStatement = $TrailBalance_model->getBankStatement($data);
				$getVendorStatement = $TrailBalance_model->getVendorStatement($data);
				$dealer_statement = $TrailBalance_model->getCustomerStatement($data);
				$vat_statement = $TrailBalance_model->getAccountStatementByDatesForPurchaseVat($data);
				$cst_statement	= $TrailBalance_model->getAccountStatementByDatesForSalesCST($data);
				$sales_statement = $TrailBalance_model->getAccountStatementByDatesForSalesAccount($data);
				$cash_statement = $TrailBalance_model->getAccountStatementByDatesForCash($data);
				$purchase_statement = $TrailBalance_model->getAccountStatementByDatesForPurchase($data);
				//echo '<pre>';print_r($dealer_statement);die;	
				$this->view->account_statement = $account_statement;		
				$this->view->bank_statement = $getBankStatement;		
				$this->view->vendor_statement = $getVendorStatement;		
				$this->view->dealer_statement = $dealer_statement;
				$this->view->vat_statement = $vat_statement;	
				$this->view->cst_statement = $cst_statement;
				$this->view->cash_statement = $cash_statement;
				$this->view->purchase_statement = $purchase_statement;
				$this->view->sales_statement = $sales_statement;
				//$pdfheader = $this->view->render('index/pdfheader.phtml');
				//$pdffooter = $this->view->render('index/pdffooter.phtml');				
				//$htmlcontent = $this->view->render('trail-balance/trial-balance-pdf.phtml');
				//$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Report");
                break;
        }
    }	
}
