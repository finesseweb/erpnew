<?php
class Finance_TradingAccountController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		$this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'trading-account';
		$this->view->sub_title_name = 'trading-account';
       // $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$TradingAccount_model = new Finance_Model_TradingAccount();
		$TradingAccount_form = new Finance_Form_TradingAccount();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->TradingAccount_form = $TradingAccount_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->TradingAccount_form = $TradingAccount_form;
                if ($this->getRequest()->isPost()) {			
                    if ($TradingAccount_form->isValid($this->getRequest()->getPost())) {						
                        $data = $TradingAccount_form->getValues();
						$print = $this->getRequest()->getPost("print");
						//$TrailBalance_form->populate($data);
						$account_statement = $TradingAccount_model->getAccountStatement($data);
						$purchase_statement = $TradingAccount_model->getAccountStatementByDatesForPurchase($data);
						$waybill_statement = $TradingAccount_model->getWaybillStatement($data);
						$retail_statement = $TradingAccount_model->getSalesRetailStatement($data);
						$sales_statement = $TradingAccount_model->getSalesExportStatement($data);
						
						//echo '<pre>';print_r($cash_statement);die;	
						$this->view->account_statement = $account_statement;
						$this->view->purchase_statement = $purchase_statement;	
						$this->view->waybill_statement = $waybill_statement;	
						$this->view->sales_statement = $sales_statement;
						$this->view->retail_statement = $retail_statement;		
						if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('trading-account/trading-account-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Trading Account");
						}
						
                }
				}else{
					$this->_redirect('finance/trading-account');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = date('Y').'-04-01';
				$this->view->enddate = $end_date = (date('Y')+1).'-03-31';
				$data = array("start_date" => $start_date, "end_date" => $end_date);
				$account_statement = $TradingAccount_model->getAccountStatement($data);
				$purchase_statement = $TradingAccount_model->getAccountStatementByDatesForPurchase($data);
				$waybill_statement = $TradingAccount_model->getWaybillStatement($data);
				$retail_statement = $TradingAccount_model->getSalesRetailStatement($data);
				$sales_statement = $TradingAccount_model->getSalesExportStatement($data);
				
				//echo '<pre>';print_r($dealer_statement);die;	
				$this->view->account_statement = $account_statement;	
				$this->view->purchase_statement = $purchase_statement;	
				$this->view->waybill_statement = $waybill_statement;	
				$this->view->sales_statement = $sales_statement;
				$this->view->retail_statement = $retail_statement;	
				
                break;
        }
    }	
}
