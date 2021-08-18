<?php


class Finance_JournalController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'journal-ledger';
		$this->view->sub_title_name = 'general-ledger';
        $vendor_payment_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $ErpFinanceJournalLedger_form = new Finance_Form_ErpFinanceJournalLedger();
		$ErpFinanceJournalLedger_model = new Finance_Model_ErpFinanceJournalLedger();
        $this->view->ErpFinanceJournalLedger_form = $ErpFinanceJournalLedger_form;
        if ($this->getRequest()->isPost()) {
            if ($ErpFinanceJournalLedger_form->isValid($this->getRequest()->getPost())) {
                $data = $ErpFinanceJournalLedger_form->getValues();                
                $this->view->start_date = $startdate = $data['start_date'];
                $this->view->enddate = $enddate = $data['end_date'];
				$this->view->post_data = $data;				
				$account_statement = $ErpFinanceJournalLedger_model->getAccountStatementByDates($data);			
				$this->view->account_statement = $account_statement;
                $payment = $ErpFinanceJournalLedger_model->getPaymentVoucherByDates($data);
                $this->view->payment = $payment;				
                
                $receipt = $ErpFinanceJournalLedger_model->getReceiptVoucherByDates($data);				
                $this->view->receipt = $receipt;
				
                $journal_voucher = $ErpFinanceJournalLedger_model->journalVoucher();
                $this->view->journal_voucher = $journal_voucher;
            }
        }else{			
			$this->view->start_date = $startdate = '2014-2-16';
            $this->view->enddate =  date("Y-m-d");
			$account_statement = $ErpFinanceJournalLedger_model->getAccountStatement();			
			$this->view->account_statement = $account_statement;
			$payment = $ErpFinanceJournalLedger_model->getPaymentVoucher();
			$this->view->payment = $payment;	
			
			$receipt = $ErpFinanceJournalLedger_model->getReceiptVoucher();
			$this->view->receipt = $receipt;			
			$journal_voucher = $ErpFinanceJournalLedger_model->journalVoucher();
			$this->view->journal_voucher = $journal_voucher;
			
		}
    }
	
	public function voucherAction() {
        $this->view->module = "finance";
		$this->view->sub_title_name = "generalvoucher";
        $this->view->action_name = 'Finance';
        $this->view->controller = 'general';
        $JournalVoucher_model = new Finance_Model_JournalVoucher();
		$messages = $this->_flashMessenger->getMessages();
		$this->view->messages = $messages;
		$result = $JournalVoucher_model->getRecords();                
		$this->view->result = $result;
		$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
		$this->view->customer = $JournalVoucher_model->getUsers();
		$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
		$this->view->vendor = $JournalVoucher_model->getUsers();
		
		$BankDetails_model = new Application_Model_ErpBankMaster();
        $this->view->bank = $bank = $BankDetails_model->getDropdownList();
    }
	
	public function statementAction() {
        $this->view->module = "statement";
		$this->view->sub_title_name = "statement";
        $this->view->action_name = 'statement';
        $this->view->controller = 'general';
        $Statement_form = new Finance_Form_Statement();
		$Statement_model = new Finance_Model_Statement();
        $this->view->form = $Statement_form;
		//$dealer_id = $this->_getParam("dealer_id");
		$vendor_id = $this->_getParam("vendor_id");
        if ($this->getRequest()->isPost()) {
            if ($Statement_form->isValid($this->getRequest()->getPost())) {
                $data = $Statement_form->getValues();                
                $this->view->start_date = $startdate = $data['start_date'];
                $this->view->enddate = $enddate = $data['end_date'];
				$this->view->post_data = $data;				
				$account_statement = $Statement_model->getAccountStatementByDates($data);			
				$this->view->account_statement = $account_statement;
            }
        }else{			
			$this->view->start_date = $startdate = '2014-2-16';
            $this->view->enddate =  date("Y-m-d");
			$account_statement = $Statement_model->getAccountStatement();			
			$this->view->account_statement = $account_statement;			
		}
		
		
    }
	
	public function ajaxVoucherAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $oper = $this->getRequest()->getPost("oper");
			$post = $this->getRequest()->getPost();
			$JournalVoucher_model = new Finance_Model_JournalVoucher();
			$data = array('date' => $post['date'], 'dealer_id' => $post['dealer_id'], 'credit_amount' => $post['credit_amount'], 'remarks' => $post['remarks'], 'debit_amount' => $post['debit_amount'], 'vendor_id' => $post['vendor_id'], 'remark1' => $post['remark1'], 'payment_type' => '5');			
			if($oper == 'add'){			
				$result = $JournalVoucher_model->insert($data);
			} elseif($oper=='del'){			
				$data['status'] = 2;
                if ($post['id'])
                $JournalVoucher_model->delete(array('id=?' => $post['id']));			
			} else{				
				$JournalVoucher_model->update($data, array('id=?' => $post['id']));
			}
			
        }
		die;
    }
	
	//checked status
	public function ajaxChangesStatusAction() {
        $this->_helper->layout->disableLayout();
        $id         = $this->_getParam("id");
		//echo $id;die;
		if($id){		
			$JournalVoucher_model = new Finance_Model_JournalVoucher();	
			$status = 1;
			$updateStatus = array('checked_status' => new Zend_Db_Expr($status));
			$JournalVoucher_model->update($updateStatus, array('id=?' => $id));			
        }die;
    }
	/* 
	*/
    public function getSubArray($array, $searchValue, $searchkey) {

        $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        $outputArray = array();
        foreach ($arrIt as $sub) {
            $subArray = $arrIt->getSubIterator();
            if ($subArray[$searchkey] === $searchValue) {
                $outputArray[] = iterator_to_array($subArray);
            }
        }

        return $outputArray;
    }
	
	public function invoiceStatementAction() {
        $this->view->module = "finance";
		$this->view->sub_title_name = "invoice-statement";
        $this->view->action_name = 'Finance';
        $this->view->controller = 'general';
        $Statement_form = new Finance_Form_InvoiceStatement();
		$Statement_model = new Finance_Model_InvoiceStatement();
		$OpeningBalance_model = new Finance_Model_OpeningBalance();
		$SatementRemark_model = new Finance_Model_SatementRemark();
		
        $this->view->form = $Statement_form;				
		//$dealer_id = $this->_getParam("dealer_id");
		$vendor_id = $this->_getParam("vendor_id");	
		$partyname = $this->_getParam("partyname");
        if ($this->getRequest()->isPost()  || !empty($partyname) ) {
            if ($Statement_form->isValid($this->getRequest()->getPost()) || $this->getRequest()->isGet() ) {
				
				if( $this->getRequest()->getPost() ){						
					$data = $Statement_form->getValues();
				}else{
					$data['party_id'] = $this->_getParam("partyname");
					$data['start_date'] = $this->_getParam("from");
					$data['end_date'] = $this->_getParam("to");
					$data['search_by'] = $this->_getParam('search_by');
				}				
				//print_r($data);die;								
				$this->view->party_name_and_id = $data['party_id'];
				
				if( !empty($data['party_id']) ){
					$array_cid = explode(':;', $data['party_id']);
					$data['party_id'] = $array_cid[0];
					$data['search_id'] = $array_cid[1];
					$data['search_type'] = $array_cid[2];
					//print_r($data); die;
				}
				
				$this->view->party_name = $data['party_id'];
				
				$data2 = $this->getRequest()->getPost();				
				if( isset( $data2['save']) ){					              
					/* $this->view->start_date = $startdate = $data['start_date'];
					$this->view->enddate = $enddate = $data['end_date'];
					$this->view->post_data = $data;				
					$account_statement = $Statement_model->getAccountStatementByDates($data);			
					$this->view->account_statement = $account_statement; */
					$this->view->start_date = $startdate = $data2['start_date'];
					$this->view->enddate = $enddate = $data2['end_date'];
					$this->view->search_by = $data2['search_by'];
					$Statement_form->populate($data2);
					//echo '<pre>';
					//print_r($data2); die;
					$data = array('statement_remarks' => $data2['statement_remarks'], 'party_name' => $data2['party_name'], 'start_date'=>$data2['start_date'], 'end_date'=>$data2['end_date'], 'auto_increment_count' => $data2['auto_increment_count']);
					$statement_remarks = $SatementRemark_model->getRecordByDate($data2['party_name'], $data2['start_date'], $data2['end_date']);
					//echo '<pre>'; 
					//print_r($statement_remarks); die;	
					//echo count($statement_remarks); die;
					//if( !empty($statement_remarks) > 0){						
						//$SatementRemark_model->update($data, array('id=?' => $statement_remarks['id']));
					//}else{						
						$SatementRemark_model->insert($data);
					//}
					$account_statement = $Statement_model->getAccountStatement();			
					$this->view->account_statement = $account_statement;	
					$statement_remarks = $SatementRemark_model->getRecordsByDate($data2['party_name'], $data2['start_date'], $data2['end_date']);
					$this->view->statement_remarks = $statement_remarks;
					$this->view->days_count = $SatementRemark_model->dayscount($data2['party_name'], $data2['start_date'], $data2['end_date']);
					$this->view->formdayscount = $SatementRemark_model->formdayscount($data2['party_name'], $data2['start_date'], $data2['end_date']);
					//$this->_flashMessenger->addMessage('Comment Added Successfully');
					//$this->_redirect('finance/journal/invoice-statement');
				}else{ 	
				
					//$purchaeBalance = $Statement_model->purchaeBalance($data['party_id']);
					//$this->view->purchaeBalance = $purchaeBalance;
					//$data = $Statement_form->getValues();
					if( $this->getRequest()->getPost() ){						
						$data = $Statement_form->getValues(); //print_r($data);die;
					}else{
						$data['party_id'] = $this->_getParam("partyname"); 
						$data['start_date'] = $this->_getParam("from");
						$data['end_date'] = $this->_getParam("to");
						$data['search_by'] = $this->_getParam('search_by');
					}
					
					$this->view->search_by = $data['search_by'];
					$this->view->start_date = $startdate = $data['start_date'];
					$this->view->enddate = $enddate = $data['end_date'];
					//$this->view->start_date = $startdate = '2014-2-16';
					//$this->view->enddate =  date("Y-m-d");
					$Statement_form->populate($data);
					$this->view->post_data = $data;
					//$data['search_type'] = '';
					//$data['search_id'] = '';
					if( !empty($data['party_id']) ){
						$array_cid = explode(':;', $data['party_id']);
						$data['party_id'] = $array_cid[0];
						$data['search_id'] = $array_cid[1];
						$data['search_type'] = $array_cid[2];
						//print_r($data); die;
						
						$customerOpeningBalance = $Statement_model->customerOpeningBalance($data['party_id'], $data['search_id'], $data['search_type']);
						$this->view->customerOpeningBalance = $customerOpeningBalance;
						$vendorOpeningBalance = $Statement_model->vendorOpeningBalance($data['party_id'], $data['search_id'], $data['search_type']);
						$this->view->vendorOpeningBalance = $vendorOpeningBalance;
						//bank
						$bankOpeningBalance = $Statement_model->bankOpeningBalance($data['party_id'], $data['search_id'], $data['search_type']);
						//print_r($bankOpeningBalance);die;
						$this->view->bankOpeningBalance = $bankOpeningBalance;
						//account
						$accountOpeningBalance = $Statement_model->accountOpeningBalance($data['party_id'], $data['search_id'], $data['search_type']);
						$this->view->accountOpeningBalance = $accountOpeningBalance;
					}
					
					if($data['search_by'] == 2){
						$account_statement = $Statement_model->getAccountStatementByDatesForPurchaseVat($data);
						//$account_vat_statement = $Statement_model->accountVat($data);
						//echo '<pre>';print_r($account_vat_statement);die;
					}elseif($data['search_by'] == 3){
						$account_statement = $Statement_model->getAccountStatementByDatesForPurchase($data);
						//echo '<pre>';print_r($account_statement);die;
					}elseif($data['search_by'] == 4){
						$account_statement = $Statement_model->getAccountStatementByDatesForCash($data);
						$accountOpeningBalanceForCash = $Statement_model->accountOpeningBalanceForCash($data['party_id']);
						$this->view->accountOpeningBalanceForCash = $accountOpeningBalanceForCash;
					}elseif($data['search_by'] == 5){
						$account_statement = $Statement_model->getAccountStatementByDatesForSalesCST($data);
					}elseif($data['search_by'] == 6){
						$account_statement = $Statement_model->getAccountStatementByDatesForSalesAccount($data);
					}elseif($data['search_by'] == 7){
						$account_statement = $Statement_model->getAccountStatementByDatesForCustomerAccount($data);
					}elseif($data['search_by'] == 8){
						$account_statement = $Statement_model->getAccountStatementByDatesForVendorAccount($data);
					}else{
						$account_statement = $Statement_model->getAccountStatementByDates($data);
						//echo '<pre>';print_r($account_statement);die;
					}
					$purchae_balance = $Statement_model->getAccountStatementByDatesBeforePurchase($data);
					$this->view->purchae_balance = $purchae_balance;
					//echo '<pre>';print_r($purchae_balance);die;
					$this->view->account_statement = $account_statement;
					
					
					//$statement_remarks = $SatementRemark_model->getRecord($data['party_id']);					
					$statement_remarks = $SatementRemark_model->getRecordsByDate($data['party_id'], $data['start_date'], $data['end_date']);
					//print_r($statement_remarks);die;
					$this->view->statement_remarks = $statement_remarks;
					$this->view->days_count = $SatementRemark_model->dayscount($data['party_id'], $data['start_date'], $data['end_date']);
					$this->view->formdayscount = $SatementRemark_model->formdayscount($data['party_id'], $data['start_date'], $data['end_date']);
					if( isset($data2['export']) ){
						$pdfheader = ''; //$this->view->render('index/pdfheader.phtml');
						$pdffooter = ''; //$this->view->render('index/pdffooter.phtml');				
						$htmlcontent = $this->view->render('journal/pdf-invoice-statement.phtml');
						//$file = $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Account Statement");
						$document_name = $data['party_id'].' Account Statement';
						//$file = $this->_act->savePdf($pdfheader, $pdffooter, $htmlcontent, $document_name);
						//$mail = $this->_act->sendMail($document_name, $description);
						$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Account Statement");						
					}
					
					if( isset($data2['sendmail']) ){
						$pdfheader = ''; //$this->view->render('index/pdfheader.phtml');
						$pdffooter = ''; //$this->view->render('index/pdffooter.phtml');				
						$htmlcontent = $this->view->render('journal/pdf-invoice-statement.phtml');
						//$file = $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Account Statement");
						$document_name = $data['party_id'].' Account Statement';
						$file = $this->_act->savePdf($pdfheader, $pdffooter, $htmlcontent, $document_name);	
						$description = nl2br($data2['description']);
						$getUserData = $Statement_model->getUserEmail($data['party_id']);						
						$mail = $this->_act->sendMail($document_name, $description, $getUserData);
						//$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Account Statement");						
					}
				}
            }
        }else{			
			$this->view->start_date = $startdate = '2014-04-01';
            $this->view->enddate =  date("Y-m-d");
			$opening_balance = $OpeningBalance_model->getRecords();
			$this->view->opening_balance = $opening_balance;
			$account_statement = $Statement_model->getAccountStatement();			
			$this->view->account_statement = $account_statement;			
		}		
    }
	
	//Sales Invoice view
	public function salesInvoiceViewAction()
    {
        $this->_helper->layout->disableLayout();
        if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ())
        {
            $invoice_id = $this->_getParam("id");
            //echo $invoice_id; exit;
            if($invoice_id)
            { 
                $ErpSalesInvoice_model = new Application_Model_ErpSalesInvoice();                      
                $salesInvoice=$ErpSalesInvoice_model->getInvoiceRecord($invoice_id);
				//print_r($salesInvoice);die;
				$ErpSalesInvoiceItems_model = new Application_Model_ErpSalesInvoiceItems();                      
                $salesInvoiceItems=$ErpSalesInvoiceItems_model->getInvoiceItemsRecord($salesInvoice['sales_invoice_id']);
				//echo '<pre>';print_r($salesInvoiceItems);die;
				//$getDate = $ErpSalesInvoice_model->getFinanceRecordID($salesInvoice['sales_invoice_id']);
				$this->view->financeDate = ''; //$getDate;
				$ErpCompany_model = new Application_Model_ErpCompanyMaster();                      
                $company=$ErpCompany_model->getCompanyRecords();                
			   $this->view->salesInvoiceItems=$salesInvoiceItems;
                $this->view->salesInvoice=$salesInvoice;
				$this->view->company=$company;
				$this->view->startdate = $this->_getParam("start_date");
				$this->view->enddate =  $this->_getParam("end_date");
				$this->view->partyname = $this->_getParam("party_name");
				$this->view->search_by = $this->_getParam('search_by');
            }
        }
		
    }
		
	public function salesInvoiceWithoutTaxAction()
    {
        $this->_helper->layout->disableLayout();
        if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ())
        {
            $invoice_id = $this->_getParam("id");
            //echo $invoice_id; exit;
            if($invoice_id)
            { 
                $ErpSalesInvoice_model = new Application_Model_ErpSalesInvoice();                      
                $salesInvoice=$ErpSalesInvoice_model->getInvoiceRecord($invoice_id);
				//print_r($salesInvoice);die;
				$ErpSalesInvoiceItems_model = new Application_Model_ErpSalesInvoiceItems();                      
                $salesInvoiceItems=$ErpSalesInvoiceItems_model->getInvoiceItemsRecord($salesInvoice['sales_invoice_id']);
				//echo '<pre>';print_r($salesInvoiceItems);die;
				//$getDate = $ErpSalesInvoice_model->getFinanceRecordID($salesInvoice['sales_invoice_id']);
				$this->view->financeDate = ''; //$getDate;
				$ErpCompany_model = new Application_Model_ErpCompanyMaster();                      
                $company=$ErpCompany_model->getCompanyRecords();                
			   $this->view->salesInvoiceItems=$salesInvoiceItems;
                $this->view->salesInvoice=$salesInvoice;
				$this->view->company=$company;
				$this->view->startdate = $this->_getParam("start_date");
				$this->view->enddate =  $this->_getParam("end_date");
				$this->view->partyname = $this->_getParam("party_name");
				$this->view->search_by = $this->_getParam('search_by');
            }
        }
		
    }
	
	public function purchaseInvoiceViewAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $cmp_model = new Application_Model_ErpCompanyMaster;
            $this->view->cmpDetail = $cmp_model->getCompanyRecords();
            $grn_id = $this->getRequest()->getPost("id");
			$this->view->startdate = $this->_getParam("start_date");
            $this->view->enddate =  $this->_getParam("end_date");
			$this->view->partyname = $this->_getParam("party_name");
			$this->view->search_by = $this->_getParam('search_by');
			$this->view->close = $this->getRequest()->getPost("close");
            $Purchase_invoice_model = new Invoice_Model_ErpPurchaseInvoice();
			$result = $Purchase_invoice_model->getRecordbyId($grn_id);
			$Purchase_invoice_items_model = new Invoice_Model_ErpPurchaseInvoiceItems();
			$item_Result = $Purchase_invoice_items_model->getRecord($result['purchase_invoice_id']);			
			$this->view->invoiceData = $result;
			$this->view->itemInvoiceData = $item_Result;   
			
			//$getDate = $Purchase_invoice_model->getFinanceRecordID($result['purchase_invoice_id']);
			$this->view->financeDate = ''; //$getDate;
			
        } else {
            exit;
        }
    }
	
	public function purchaseInvoiceWithoutTaxAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $cmp_model = new Application_Model_ErpCompanyMaster;
            $this->view->cmpDetail = $cmp_model->getCompanyRecords();
            $grn_id = $this->getRequest()->getPost("id");
			$this->view->startdate = $this->_getParam("start_date");
            $this->view->enddate =  $this->_getParam("end_date");
			$this->view->partyname = $this->_getParam("party_name");
			$this->view->search_by = $this->_getParam('search_by');
			$this->view->close = $this->getRequest()->getPost("close");
            $Purchase_invoice_model = new Invoice_Model_ErpPurchaseInvoice();
			$result = $Purchase_invoice_model->getRecordbyId($grn_id);
			$Purchase_invoice_items_model = new Invoice_Model_ErpPurchaseInvoiceItems();
			$item_Result = $Purchase_invoice_items_model->getRecord($result['purchase_invoice_id']);			
			$this->view->invoiceData = $result;
			$this->view->itemInvoiceData = $item_Result;   
			
			//$getDate = $Purchase_invoice_model->getFinanceRecordID($result['purchase_invoice_id']);
			$this->view->financeDate = ''; //$getDate;
			
        } else {
            exit;
        }
    }
	
	public function ajaxWayBillAction() {
      $this->_helper->layout->disableLayout(); 
      // Select Commercial Invoice	  
	  $waybill_id = $this->_getParam("id");	 
	  $this->view->startdate = $this->_getParam("start_date");
		$this->view->enddate =  $this->_getParam("end_date");
		$this->view->partyname = $this->_getParam("party_name");
		$this->view->search_by = $this->_getParam('search_by');
	  // Select Order
	  $waybill = new Application_Model_ErpSalesWayBill();
	  $waybill_record = $waybill->getRecord($waybill_id);
	  $this->view->record = $waybill_record;
	  //echo $waybill_id; die;
	  $ErpSalesOrder_model = new Application_Model_ErpSalesWayBillProducts();
      $data_enquiry = $ErpSalesOrder_model->getSlaesItemsDetails($waybill_id);
      $this->view->getItems=$data_enquiry;
	  //print_r($waybill_record); die;
	   // Select Proforma Invoice
	  $ErpSalesProformaInvoice_model = new Application_Model_ErpSalesProformaInvoice();				
	  $proforma = $ErpSalesProformaInvoice_model->getRecord($waybill_record['proforma_invoice_id']);
	  $this->view->proforma = $proforma;
	  
	  // Select Company
	  $company_details = new Application_Model_ErpCompanyMaster();
	  $company_details= $company_details->getCompanyRecords();
      $this->view->company=$company_details;	   
    }
	
	// View Commercial Invoice
	public function ajaxCommercialInvoiceAction(){
        $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$commercial_invoice_id = $this->_getParam("id");
			$this->view->startdate = $this->_getParam("start_date");
			$this->view->enddate =  $this->_getParam("end_date");
			$this->view->partyname = $this->_getParam("party_name");
			$this->view->search_by = $this->_getParam('search_by');
			if($commercial_invoice_id){
				$ErpPurchaseCommercialInvoice_model = new Application_Model_ErpPurchaseCommercialInvoice();				
				$result = $ErpPurchaseCommercialInvoice_model->getRecord($commercial_invoice_id);
				$this->view->record = $result;
				$ErpPurchaseCommercialInvoiceItems =new  Application_Model_ErpPurchaseCommercialInvoiceItems();
				$this->view->getItems = $ErpPurchaseCommercialInvoiceItems->getItems($commercial_invoice_id);
				$company_model = new Application_Model_ErpCompanyMaster();
				$company_details  = $company_model->getCompanyRecords();				 
				$this->view->company = $company_details;				
			}               
         }        
    }
	
	public function voucherIndexAction() {
		$this->view->module = "finance";
		$this->view->sub_title_name = "generalvoucher";
        $this->view->action_name = 'Finance';
        $this->view->controller = 'general';
        $JournalVoucher_model = new Finance_Model_JournalVoucher();
        $JournalVoucherMain_model = new Finance_Model_JournalVoucherMain();
        $voucherForm = new Finance_Form_VoucherForm();
		$this->view->form = $voucherForm;
		$messages = $this->_flashMessenger->getMessages();
		$this->view->messages = $messages;		
		$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
		$this->view->customer = $JournalVoucher_model->getUsers();
		$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
		$this->view->vendor = $JournalVoucher_model->getUsers();		
		//$BankDetails_model = new Application_Model_ErpBankMaster();
        //$this->view->bank = $bank = $BankDetails_model->getDropdownList();
        
        $type = $this->_getParam("type");
		$voucher_id = $this->_getParam("id");
        switch ($type) {
            case "add":                
                $this->view->type = $type;
                if ($this->getRequest()->isPost()) {
                    if ($voucherForm->isValid($this->getRequest()->getPost())) {
                        $data = $voucherForm->getValues();						
							$voucherId=$JournalVoucherMain_model->insert($data);
 							$voucher=$this->getRequest()->getPost("voucher");
							foreach(array_filter($voucher['dealer_id']) as $k=>$dealer_id){
								if(!empty($dealer_id)){									
									$array_cid = explode(':;', $dealer_id);
									$dealer_id = $array_cid[0];
									$item_data['dealer_id']=$dealer_id;
									$item_data['credit_party_id']=$array_cid[1];
									$item_data['credit_party_type']=$array_cid[2];
								}
								
								$item_data['voucher_id']=$voucherId;								
								$item_data['date']=$voucher['date'][$k];
								$item_data['credit_amount']=$voucher['credit_amount'][$k];
								$item_data['remarks']=$voucher['remarks'][$k];
								$item_data['debit_amount']=$voucher['debit_amount'][$k];
								if(!empty($voucher['vendor_id'][$k])){
									$array_vid = explode(':;', $voucher['vendor_id'][$k]);
									$item_data['vendor_id']= $array_vid[0];
									$item_data['debit_party_id']=$array_vid[1];
									$item_data['debit_party_type']=$array_vid[2];
								}
								
								$item_data['remark1']=$voucher['remark1'][$k];
								$lastinsert_id = $JournalVoucher_model->insert($item_data);								
							}
                        $this->_flashMessenger->addMessage(' Added Successfully ');
                        $this->_redirect('finance/journal/voucher-index');
                    }
                }
                break;
				
				case "edit":
				$edit_records = $JournalVoucher_model->getRecord($voucher_id);
				$this->view->edit_records = $edit_records;
                $this->view->type = $type;
                 if ($this->getRequest()->isPost()) {
                    if ($voucherForm->isValid($this->getRequest()->getPost())) {
                        $data = $voucherForm->getValues();	
							//$JournalVoucherMain_model->update((array('voucher_id =?' => $voucher_id))); 
							$JournalVoucher_model->delete(array('voucher_id =?' => $voucher_id));
 							$voucher=$this->getRequest()->getPost("voucher");
							foreach(array_filter($voucher['dealer_id']) as $k=>$dealer_id){
								$item_data['voucher_id']=$voucher_id;
								//$item_data['dealer_id']=$dealer_id; 
								if(!empty($dealer_id)){									
									$array_cid = explode(':;', $dealer_id);
									$dealer_id = $array_cid[0];
									$item_data['dealer_id']=$dealer_id;
									$item_data['credit_party_id']=$array_cid[1];
									$item_data['credit_party_type']=$array_cid[2];
								}
								$item_data['date']=$voucher['date'][$k];
								$item_data['credit_amount']=$voucher['credit_amount'][$k];
								$item_data['remarks']=$voucher['remarks'][$k];
								$item_data['debit_amount']=$voucher['debit_amount'][$k];
								//$item_data['vendor_id']=$voucher['vendor_id'][$k];
								if(!empty($voucher['vendor_id'][$k])){
									$array_vid = explode(':;', $voucher['vendor_id'][$k]);
									$item_data['vendor_id']= $array_vid[0];
									$item_data['debit_party_id']=$array_vid[1];
									$item_data['debit_party_type']=$array_vid[2];
								}
								$item_data['remark1']=$voucher['remark1'][$k];								
								$lastinsert_id = $JournalVoucher_model->insert($item_data);								
							}
                        $this->_flashMessenger->addMessage(' Updated Successfully ');
                        $this->_redirect('finance/journal/voucher-index');
                    }
                }
                break;
				
				 case 'delete':
                if ($voucher_id) {
					$data['status'] = 2;
					$JournalVoucherMain_model->update($data,array('voucher_id =?' => $voucher_id)); 
                    $JournalVoucher_model->update($data,array('voucher_id =?' => $voucher_id));
                    $this->_flashMessenger->addMessage('Record Deleted');
                    $this->_redirect('finance/journal/voucher-index');
                }
                break;

            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $JournalVoucher_model->getRecords();                
				//$this->view->result = $result;				
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
