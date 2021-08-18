<?php


class Finance_ProfitAndLossController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		$this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'profit-and-loss';
		$this->view->sub_title_name = 'profit-and-loss';
       // $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$ProfitAndLoss_model = new Finance_Model_ProfitAndLoss();
		$ProfitAndLoss_form = new Finance_Form_ProfitAndLoss();
	
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->ProfitAndLoss_form = $ProfitAndLoss_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->ProfitAndLoss_form = $ProfitAndLoss_form;
                if ($this->getRequest()->isPost()) {			
                    if ($ProfitAndLoss_form->isValid($this->getRequest()->getPost())) {						
                        $data = $ProfitAndLoss_form->getValues();
						$print = $this->getRequest()->getPost("print");
						//print_r($print); die;
						//$TrailBalance_form->populate($data);
						$getLeftAccountStatement = $ProfitAndLoss_model->getLeftAccountStatement($data);
						$getRightAccountStatement = $ProfitAndLoss_model->getRightAccountStatement($data);
						$account_statement = $ProfitAndLoss_model->getAccountStatement($data);
						$purchase_statement = $ProfitAndLoss_model->getAccountStatementByDatesForPurchase($data);
						$waybill_statement = $ProfitAndLoss_model->getWaybillStatement($data);
						$retail_statement = $ProfitAndLoss_model->getSalesRetailStatement($data);
						$sales_statement = $ProfitAndLoss_model->getSalesExportStatement($data);
						
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
						// End
						//echo '<pre>';print_r($cash_statement);die;	
						$this->view->account_statement_left = $getLeftAccountStatement;	
						$this->view->account_statement_right = $getRightAccountStatement;
						$this->view->account_statement = $account_statement;
						$this->view->purchase_statement = $purchase_statement;	
						$this->view->waybill_statement = $waybill_statement;	
						$this->view->sales_statement = $sales_statement;
						$this->view->retail_statement = $retail_statement;		
						if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('profit-and-loss/profit-and-loss-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Profit And Loss");
						}
		
						
                }
				}else{
					$this->_redirect('finance/profit-and-loss');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = date('Y').'-04-01';
				$this->view->enddate = $end_date = (date('Y')+1).'-03-31';
				$data = array("start_date" => $start_date, "end_date" => $end_date);
				$getLeftAccountStatement = $ProfitAndLoss_model->getLeftAccountStatement($data);
				$getRightAccountStatement = $ProfitAndLoss_model->getRightAccountStatement($data);
				$account_statement = $ProfitAndLoss_model->getLeftAccountStatement($data);
				$purchase_statement = $ProfitAndLoss_model->getAccountStatementByDatesForPurchase($data);
				$waybill_statement = $ProfitAndLoss_model->getWaybillStatement($data);
				$retail_statement = $ProfitAndLoss_model->getSalesRetailStatement($data);
				$sales_statement = $ProfitAndLoss_model->getSalesExportStatement($data);
				
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
				// End
				
				//echo '<pre>';print_r($dealer_statement);die;
				$this->view->account_statement_left = $getLeftAccountStatement;	
				$this->view->account_statement_right = $getRightAccountStatement;
				$this->view->account_statement = $account_statement;	
				$this->view->purchase_statement = $purchase_statement;	
				$this->view->waybill_statement = $waybill_statement;	
				$this->view->sales_statement = $sales_statement;
				$this->view->retail_statement = $retail_statement;	
				
                break;
        }
    }	
}
