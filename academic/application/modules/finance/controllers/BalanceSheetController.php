<?php


class Finance_BalanceSheetController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		$this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'balance-sheet';
		$this->view->sub_title_name = 'balance-sheet';
       // $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$BalanceSheet_model = new Finance_Model_BalanceSheet();
		$ProfitAndLoss_model = new Finance_Model_ProfitAndLoss();		
		$TradingAccount_model = new Finance_Model_TradingAccount();
		
		$BalanceSheet_form = new Finance_Form_BalanceSheet();
	
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->BalanceSheet_form = $BalanceSheet_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->BalanceSheet_form = $BalanceSheet_form;
                if ($this->getRequest()->isPost()) {			
                    if ($BalanceSheet_form->isValid($this->getRequest()->getPost())) {						
                        $data = $BalanceSheet_form->getValues();
						$print = $this->getRequest()->getPost("print");
						//print_r($print); die;
						$BalanceSheet_form->populate($data);
						$this->view->middate = $mid_date = (date('Y')).'-09-30';
						$data['mid_date'] = $mid_date;
						
						$getAccountStatementByDatesForCash = $BalanceSheet_model->getAccountStatementByDatesForCash($data);
						$this->view->cash_account = $getAccountStatementByDatesForCash;
						
						$getAccountStatementByDatesForPurchaseVat = $BalanceSheet_model->getAccountStatementByDatesForPurchaseVat($data);
						$this->view->vat_account = $getAccountStatementByDatesForPurchaseVat;
						
						$getAccountAssestsStatement = $BalanceSheet_model->getAccountAssestsStatement($data);
						$this->view->getAccountAssestsStatement = $getAccountAssestsStatement;
						
						$getCapitalAccountStatement = $BalanceSheet_model->getCapitalAccountStatement();
						$this->view->capital_account = $getCapitalAccountStatement;	
						
						$getSecuredLoans = $BalanceSheet_model->getSecuredLoans();
						$this->view->getSecuredLoans = $getSecuredLoans;
						
						$getUnsecuredLoans = $BalanceSheet_model->getUnsecuredLoans();
						$this->view->getUnsecuredLoans = $getUnsecuredLoans;
						
						$getCurrentLiabilities = $BalanceSheet_model->getCurrentLiabilities();
						$this->view->getCurrentLiabilities = $getCurrentLiabilities;
						
						$getCurrentAsset = $BalanceSheet_model->getCurrentAsset();
						$this->view->getCurrentAsset = $getCurrentAsset;
						
						$getSecurities = $BalanceSheet_model->getSecurities();
						$this->view->getSecurities = $getSecurities;
						
						$getExpenses = $BalanceSheet_model->getExpenses();
						$this->view->getExpenses = $getExpenses;
						
						$purchase_statement = $BalanceSheet_model->getAccountStatementByDatesForPurchase($data);
						$this->view->purchase_statement = $purchase_statement;
						
						$waybill_statement = $BalanceSheet_model->getWaybillStatement();
						$this->view->waybill_statement = $waybill_statement;
						
						$retail_statement = $BalanceSheet_model->getSalesRetailStatement();
						$this->view->retail_statement = $retail_statement;
						
						$sales_statement = $BalanceSheet_model->getSalesExportStatement();
						$this->view->sales_statement = $sales_statement;
										
						$bank_statement = $BalanceSheet_model->getBankStatementlist($data);
						$this->view->bank_statement = $bank_statement;
						
						$liabilities_bank_statement = $BalanceSheet_model->getBankStatementLiabilities($data);
						$this->view->liabilities_bank_statement = $liabilities_bank_statement;					
						
						
						$profitandloss_left = $BalanceSheet_model->getProfitLeftAccountStatement();
						$this->view->profitandloss_left = $profitandloss_left;
						
						$profitandloss_right = $BalanceSheet_model->getProfitRightAccountStatement();				
						$this->view->profitandloss_right = $profitandloss_right;
						
						$trading_account = $TradingAccount_model->getAccountStatement($data);
						$this->view->trading_account = $trading_account;
						// Gross Profit amount calculation
						$total_credit = 0;
						$total_debit = 0;
						$amount = 0;
						/* if ( count($account_statement) > 0 ) {
							foreach ($trading_account_statement as $account) {
							//If collect the cash that records not displayed here							
								$amount = $account['payment_amount']  + $account['receipt_amount'] + $account['debit_amount'] + $account['credit_amount'] + $account['opening_balance'];								
								echo number_format(round($amount),2);
								$total_debit += round($amount);							
							}
						}											

						if ( count($purchase_statement) > 0 ) {
							foreach ($purchase_statement as $purchase) {
								$total_debit += $purchase['paid_amount'];
							}
						} */
						
						if ( count($waybill_statement) > 0 ) {
							foreach ($waybill_statement as $waybill) {
								$total_credit += $waybill['waybill_amount'];
							}
						}											

						if ( count($retail_statement) > 0 ) {
							foreach ($retail_statement as $sales) {
								$total_credit += $sales['paid_amount'];
							}
						}		
						if ( count($sales_statement) > 0 ) {
							foreach ($sales_statement as $retail) {
								$total_credit += $retail['paid_amount'];
							}
						}
						
						$this->view->gross_profit = $gross_profit = $total_credit * 17.05 / 100;
						//$this->view->$total_credit;
						// End
						
						$getCustomerStatementlist = $BalanceSheet_model->getCustomerStatementlist($data);
						$getVendorStatementlist = $BalanceSheet_model->getVendorStatementlist($data);				
						$this->view->getVendorStatementlist = $getVendorStatementlist;
						$this->view->getCustomerStatementlist = $getCustomerStatementlist;
						
						if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('balance-sheet/balance-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Trading Account");
						}
		
						
                }
				}else{
					$this->_redirect('finance/balance-sheet');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = date('Y').'-04-01';
				$this->view->enddate = $end_date = (date('Y')+1).'-03-31';
				$this->view->middate = $mid_date = (date('Y')).'-09-30';

				$data = array("start_date" => $start_date, "end_date" => $end_date, "mid_date" => $mid_date);
				
				$getAccountStatementByDatesForCash = $BalanceSheet_model->getAccountStatementByDatesForCash($data);
				$this->view->cash_account = $getAccountStatementByDatesForCash;
				
				$getAccountStatementByDatesForPurchaseVat = $BalanceSheet_model->getAccountStatementByDatesForPurchaseVat($data);
				$this->view->vat_account = $getAccountStatementByDatesForPurchaseVat;
				
				$getAccountAssestsStatement = $BalanceSheet_model->getAccountAssestsStatement($data);
				$this->view->getAccountAssestsStatement = $getAccountAssestsStatement;
				
				$getCapitalAccountStatement = $BalanceSheet_model->getCapitalAccountStatement();
				$this->view->capital_account = $getCapitalAccountStatement;	
				
				$getSecuredLoans = $BalanceSheet_model->getSecuredLoans();
				$this->view->getSecuredLoans = $getSecuredLoans;
				
				$getUnsecuredLoans = $BalanceSheet_model->getUnsecuredLoans();
				$this->view->getUnsecuredLoans = $getUnsecuredLoans;
				
				$getCurrentLiabilities = $BalanceSheet_model->getCurrentLiabilities();
				$this->view->getCurrentLiabilities = $getCurrentLiabilities;
				
				$getCurrentAsset = $BalanceSheet_model->getCurrentAsset();
				$this->view->getCurrentAsset = $getCurrentAsset;
				
				$getSecurities = $BalanceSheet_model->getSecurities();
				$this->view->getSecurities = $getSecurities;
				
				$getExpenses = $BalanceSheet_model->getExpenses();
				$this->view->getExpenses = $getExpenses;
				
				$purchase_statement = $BalanceSheet_model->getAccountStatementByDatesForPurchase($data);
				$this->view->purchase_statement = $purchase_statement;
				
				$waybill_statement = $BalanceSheet_model->getWaybillStatement();
				$this->view->waybill_statement = $waybill_statement;
				
				$retail_statement = $BalanceSheet_model->getSalesRetailStatement();
				$this->view->retail_statement = $retail_statement;
				
				$sales_statement = $BalanceSheet_model->getSalesExportStatement();
				$this->view->sales_statement = $sales_statement;
								
				$bank_statement = $BalanceSheet_model->getBankStatementlist($data);
				$this->view->bank_statement = $bank_statement;
				
				$liabilities_bank_statement = $BalanceSheet_model->getBankStatementLiabilities($data);
				$this->view->liabilities_bank_statement = $liabilities_bank_statement;
				
				$profitandloss_left = $BalanceSheet_model->getProfitLeftAccountStatement();
				$this->view->profitandloss_left = $profitandloss_left;
				
				$profitandloss_right = $BalanceSheet_model->getProfitRightAccountStatement();				
				$this->view->profitandloss_right = $profitandloss_right;
				
				$trading_account = $TradingAccount_model->getAccountStatement($data);
				$this->view->trading_account = $trading_account;
				// Gross Profit amount calculation
				$total_credit = 0;
				$total_debit = 0;
				$amount = 0;
				/* if ( count($account_statement) > 0 ) {
					foreach ($trading_account_statement as $account) {
					//If collect the cash that records not displayed here							
						$amount = $account['payment_amount']  + $account['receipt_amount'] + $account['debit_amount'] + $account['credit_amount'] + $account['opening_balance'];								
						echo number_format(round($amount),2);
						$total_debit += round($amount);							
					}
				}											

				if ( count($purchase_statement) > 0 ) {
					foreach ($purchase_statement as $purchase) {
						$total_debit += $purchase['paid_amount'];
					}
				} */
				
				if ( count($waybill_statement) > 0 ) {
					foreach ($waybill_statement as $waybill) {
						$total_credit += $waybill['waybill_amount'];
					}
				}											

				if ( count($retail_statement) > 0 ) {
					foreach ($retail_statement as $sales) {
						$total_credit += $sales['paid_amount'];
					}
				}		
				if ( count($sales_statement) > 0 ) {
					foreach ($sales_statement as $retail) {
						$total_credit += $retail['paid_amount'];
					}
				}
				
				$this->view->gross_profit = $gross_profit = $total_credit * 17.05 / 100;
				//$this->view->$total_credit;
				// End
				
				$getCustomerStatementlist = $BalanceSheet_model->getCustomerStatementlist($data);
				$getVendorStatementlist = $BalanceSheet_model->getVendorStatementlist($data);				
				$this->view->getVendorStatementlist = $getVendorStatementlist;
				$this->view->getCustomerStatementlist = $getCustomerStatementlist;
				
                break;
        }
    }	
}
