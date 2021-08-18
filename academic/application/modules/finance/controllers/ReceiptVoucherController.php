<?php

class Finance_ReceiptVoucherController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		  $this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'Transactions';
        $this->view->controller = 'receipt-voucher';
		 $this->view->sub_title_name = 'receipt-voucher';
        $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$ErpFinanceReceiptVoucher_form = new Finance_Form_ReceiptVoucher();
		$vendor_model = new Application_Model_ErpVendorMaster();
		$BankDetails_model = new Application_Model_ErpBankMaster();
		$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
		$Account_model = new Application_Model_Account();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
        switch ($type) {
            case "add":                
                $this->view->type = $type;
                $this->view->ErpFinanceReceiptVoucher_form = $ErpFinanceReceiptVoucher_form;
                if ($this->getRequest()->isPost()) {					
                    if ($ErpFinanceReceiptVoucher_form->isValid($this->getRequest()->getPost())) {
						$data = $ErpFinanceReceiptVoucher_form->getValues();
						 
						if( !empty($_FILES["file_upload"]["tmp_name"]) ){
							$uploadPath = realpath(APPLICATION_PATH . '/../public/images/uploads') . '/';
							//print_r($uploadPath);die;
							$file_upload = $ErpFinanceReceiptVoucher_form->file_upload;
							//print_r($file_upload);die;
							$fileinfo = $file_upload->getFileInfo();
							//echo '<pre>';print_r($fileinfo['file_upload']);die;
							$data['file_upload'] = $this->_act->uploadfile($fileinfo['file_upload'], $uploadPath);							
						}else{
							$data['file_upload'] = '';
						}
						
						//echo '<pre>';print_r($data);die;
						//$data['radio_button_value'] = $this->getRequest()->getPost('radio_button_value');
						//$data['party_names'] = $this->getRequest()->getPost('party_names');
						$data['total_amount_hidden'] = 0;
						if( $data['total_amount'] > 0){						
							$data['total_amount_hidden'] = $data['total_amount'] - $data['paid_amount'];
						}
						
						$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
						$party_id = explode(',', $data['dealer_id']);
						//print_r($party_id ); die;
						if(($party_id[1] == 'C') || ($party_id[1] == 'C')){
							$result = $ErpDealerMaster_model->getRecord($party_id[0]);
							$data['party_names'] = $result['dealer_company_name'];
						}
						if($party_id[1] == 'V'){
							$result = $vendor_model->getRecord($party_id[0]);
							$data['party_names'] = $result['vendor_name'];
						}
						if($party_id[1] == 'B'){
							$result = $BankDetails_model->getRecord($party_id[0]);							
							$data['party_names'] = $result['beneficiary_bank_name'];
						}
						if($party_id[1] == 'A'){
							$result = $Account_model->getRecord($party_id[0]);
							$data['party_names'] = $result['account_name'];
						}
						$data['dealer_id'] = $party_id[0];
						if(isset($party_id[1])){
							$data['party_type'] =  $party_id[1];
						}						
						if($data['dealer_id'] != null){
							$dealer_data = $ErpDealerMaster_model->getRecord($data['dealer_id']);  
							if($data['invoice_id'] != null){
								if($dealer_data['dealer_type'] == 1){
									$data['waybill_id'] =  $data['invoice_id'];
									$data['invoice_id'] =  '';
								}else{
									$data['invoice_id'] =  $data['invoice_id'];
								}
							}else{
								$data['invoice_id'] = 0;
							}	
						}else{
							$data['dealer_id'] = 0;
						}
						//echo '<pre>';print_r($id);die;
                        $last_inert_id = $ErpFinanceReceiptVoucher_model->insert($data);						
						$data1['opening_balance'] = $data['balance'];
						$dealer_model = new Application_Model_ErpDealerMaster();
						$dealer_model->update($data1, array('dealer_id=?' => $data['dealer_id']));
                        $this->_flashMessenger->addMessage('Finance Successfully added');
                        $this->_redirect('finance/receipt-voucher/index');
                    }
                }
                break;
            case 'edit':               
                $this->view->type = $type;
                $this->view->ErpFinanceReceiptVoucher_form = $ErpFinanceReceiptVoucher_form;
                $result = $ErpFinanceReceiptVoucher_model->getRecord($id);
				$this->view->result	=$result;
				//print_r($result); die;
				/* $ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
				 //$data1 = $ErpDealerMaster_model->getAllDropDownList();
				//if($result['dealer_id'] !=0){
				$dealer_data = $ErpDealerMaster_model->getRecord($result['dealer_id']);				
					if($dealer_data['dealer_type'] == 1){
						//$data['commercial_invoice_id'] =  $data['invoice_id'];
						//$data['invoice_id'] =  '';
						//$data['']="Select Sales Order ID"; 
						if(strlen($result['waybill_id'])==1){
						$data[$result['waybill_id']]= @(WB_PREFIX)."000".$result['waybill_id'];
						}else
						if(strlen($result['waybill_id'])==2){
						$data[$result['waybill_id']]= @(WB_PREFIX)."00".$result['waybill_id'];
						}else
						if(strlen($result['waybill_id'])==3){
						$data[$result['waybill_id']]= @(WB_PREFIX)."0".$result['waybill_id'];
						}
						else{
						$data[$result['waybill_id']]= @(WB_PREFIX)."".$result['waybill_id'];
						}
						ksort($data);
						$ErpFinanceReceiptVoucher_form->getElement("invoice_id")
											->setAttrib('readonly','readonly')
											->setAttrib('class',array('form-control'))
											->setMultiOptions($data);
					}else{
						//$data['invoice_id'] =  $data['invoice_id'];
						if(strlen($result['invoice_id'])==1){
						$data[$result['invoice_id']]= $result['increment_text'];
						}else
						if(strlen($result['invoice_id'])==2){
						$data[$result['invoice_id']]= $result['increment_text'];
						}else
						if(strlen($result['invoice_id'])==3){
						$data[$result['invoice_id']]= $result['increment_text'];
						}
						else{
						$data[$result['invoice_id']]= $result['increment_text'];
						}
						ksort($data);
						$ErpFinanceReceiptVoucher_form->getElement("invoice_id")
											->setAttrib('readonly','readonly')
											->setAttrib('class',array('form-control'))
											->setMultiOptions($data);
					} */ 
				//}
				
				 $data2= $ErpDealerMaster_model->getAllDropDownList();
				 //print_r($data2); die;
				 //$data2 = '';
				$data1[$result['dealer_id'].','.$result['party_type']] = $result['party_names'].'('.$result['party_type'].')';
					ksort($data1);
					//print_r($data1); die;
					 $ErpFinanceReceiptVoucher_form->getElement("dealer_id")
										->setAttrib('readonly','readonly')
										//->setAttrib('class',array('form-control'))
										->setMultiOptions($data1)
										->addMultiOptions($data2)
										 ->setAttrib('class', array('form-control','select2')); 
				$ErpFinanceReceiptVoucher_form->populate($result); 
                if ($this->getRequest()->isPost()) {
				
				//if (!$ErpFinanceReceiptVoucher_form->file_upload->isUploaded() && $this->getRequest()->getPost('prev_image')) {
						//$ErpFinanceReceiptVoucher_form->file_upload->setRequired(false)->setValidators(array());
                   // }
                    if ($ErpFinanceReceiptVoucher_form->isValid($this->getRequest()->getPost())) {
                        $data = $ErpFinanceReceiptVoucher_form->getValues();
						
						$uploadPath = realpath(APPLICATION_PATH . '/../public/images/uploads') . '/';
						if( !empty($_FILES["file_upload"]["tmp_name"]) ){ 
                            $file_upload = $ErpFinanceReceiptVoucher_form->file_upload;
                            $fileinfo = $file_upload->getFileInfo();
                            $data['file_upload'] = $this->_act->uploadfile($fileinfo['file_upload'], $uploadPath);
							//print_r($data['file_upload']); die;
                            //unlink($uploadPath . $this->getRequest()->getPost('prev_image'));
                        } else {
                            //$data['file_upload'] = $this->getRequest()->getPost('prev_image');
							unset($data['file_upload']);
                        }						
						
						$data['party_names'] = $this->getRequest()->getPost('party_names');
							//print_r($data);die;
						$data['total_amount_hidden'] = 0;
						if( $data['total_amount'] > 0){						
							$data['total_amount_hidden'] = $data['total_amount'] - $data['paid_amount'];
						}
						
						$party_id = explode(',', $data['dealer_id']);
						if(($party_id[1] == 'C') || ($party_id[1] == 'I')){
							$result = $ErpDealerMaster_model->getRecord($party_id[0]);
							$data['party_names'] = $result['dealer_company_name'];
						}
						if($party_id[1] == 'V'){
							$result = $vendor_model->getRecord($party_id[0]);
							$data['party_names'] = $result['vendor_name'];
						}
						if($party_id[1] == 'B'){
							$result = $BankDetails_model->getRecord($party_id[0]);
							$data['party_names'] = $result['beneficiary_bank_name'];
						}
						if($party_id[1] == 'A'){
							$result = $Account_model->getRecord($party_id[0]);
							$data['party_names'] = $result['account_name'];
						}
						$data['dealer_id'] = $party_id[0];
						if(isset($party_id[1])){
							$data['party_type'] =  $party_id[1];
						}
						if($data['dealer_id'] != null){
							// $dealer_data = $ErpDealerMaster_model->getRecord($data['dealer_id']);  
							$dealer_data = '';
							if($data['invoice_id'] != null){
								if($dealer_data['dealer_type'] == 1){
									$data['waybill_id'] =  $data['invoice_id'];
									$data['invoice_id'] =  '';
								}else{
									$data['invoice_id'] =  $data['invoice_id'];
								}
							}else{
								$data['invoice_id'] = 0;
							}	
						}else{
							$data['dealer_id'] = 0;
						}	
					//print_r($data);	die;
					/* $data['opening_balance1'] = $data['balance']; */					
					$ErpFinanceReceiptVoucher_model->update($data, array('receipt_voucher_id=?' => $id));
					
					$getMainRecord = $ErpFinanceReceiptVoucher_model->getRecord($id);
					//
					if($getMainRecord['dealer_id'] != 0 ){
					$result = $ErpFinanceReceiptVoucher_model->editRecordByCustomer($getMainRecord['dealer_id'], $id);
					//print_r($result);die;
						if( count($result) > 0 ){						
							$delete_paidamt = 0;
							$i=1;
							foreach($result as $value){								
								$getFirstRecord = $ErpFinanceReceiptVoucher_model->getFirstRecordByCustomer($value['dealer_id'], $value['receipt_voucher_id']);
								$total_amount = $getFirstRecord['balance'];									
								if( $value['operator'] == 2){					
									$opening_balance = ($getFirstRecord['balance'] - $value['paid_amount']);
								}else{							
									$opening_balance = ($getFirstRecord['balance'] - $value['paid_amount']);
								}
								
								//$data1['opening_balance'] = $opening_balance;
								//$dealer_model = new Application_Model_ErpDealerMaster();							
								//$dealer_model->update($data1, array('dealer_id=?' => $value['dealer_id']));	
								$reupdate_data = array('opening_balance1' => $opening_balance, 'total_amount' => $total_amount, 'balance' => $opening_balance);	
								$ErpFinanceReceiptVoucher_model->update($reupdate_data, array('receipt_voucher_id =?' => $value['receipt_voucher_id']));
								
							$i++;
							}
						} 
					}
                        $this->_flashMessenger->addMessage('Finance Updated Successfully');
                        $this->_redirect('finance/receipt-voucher/index');
                    }
                }
                break;
				
            case 'delete':
                $data['status'] = 2;
                if ($id){
					
					$getRecord = $ErpFinanceReceiptVoucher_model->getRecord($id);					
					$result = $ErpFinanceReceiptVoucher_model->getAfterRecordByCustomer($getRecord['dealer_id'], $id);
					$dealer_id = '';
					$receipt_voucher_id = '';
					if( count($result) > 0 ){ 
						$total_amount = $getRecord['total_amount'];								
						if( $result['operator'] == 2){					
							$opening_balance = ($getRecord['total_amount'] - $result['paid_amount']);																
						}else{							
							$opening_balance = ($getRecord['total_amount'] - $result['paid_amount']);								
						}
						$data1=array('transaction_amount' => new Zend_Db_Expr("transaction_amount +".$getRecord['transaction_amount']), 'opening_balance1' => $opening_balance, 'total_amount' => $total_amount, 'balance' => $opening_balance);
						$ErpFinanceReceiptVoucher_model->update($data1, array('receipt_voucher_id =?' => $result['receipt_voucher_id']));
					$dealer_id = $result['dealer_id'];
					$receipt_voucher_id = $result['receipt_voucher_id'];
					}
					
					$ErpFinanceReceiptVoucher_model->update($data, array('receipt_voucher_id=?' => $id));
					if( !empty($result) ){
						$getResult = $ErpFinanceReceiptVoucher_model->getRecordByCustomer($dealer_id, $receipt_voucher_id);						
						//echo '<pre>'; 
						//print_r($getResult); die;
						if( count($getResult) > 0 ){
							$delete_paidamt = 0;
							$i=1;
							foreach($getResult as $value){							 
								$getFirstRecord = $ErpFinanceReceiptVoucher_model->getFirstRecordByCustomer($value['dealer_id'], $value['receipt_voucher_id']);
								$total_amount = $getFirstRecord['balance'];								
								if( $value['operator'] == 2){					
									$opening_balance = ($getFirstRecord['balance'] - $value['paid_amount']);																
								}else{							
									$opening_balance = ($getFirstRecord['balance'] - $value['paid_amount']);								
								}
								
								//$data1['opening_balance'] = $opening_balance;							
								//$dealer_model = new Application_Model_ErpDealerMaster();							
								//$dealer_model->update($data1, array('dealer_id=?' => $value['dealer_id'])); 						
								$reupdate_data = array('opening_balance1' => $opening_balance, 'total_amount' => $total_amount, 'balance' => $opening_balance);	
								$ErpFinanceReceiptVoucher_model->update($reupdate_data, array('receipt_voucher_id =?' => $value['receipt_voucher_id']));
								//echo '<pre>';
								//print_r($reupdate_data);
							$i++;
							}
						}
					}
					//die;
				}
                $this->_flashMessenger->addMessage('Finance Deleted Successfully');
                $this->_redirect('finance/receipt-voucher/index');
                break;
				case 'download':
				//echo "test"; die;
                $this->_helper->viewRenderer->setNoRender(true);
				$this->_helper->layout->disableLayout();
				$shipment_files = $this->_getParam("file");				
				if ($shipment_files) { 					
					$path_file = realpath(APPLICATION_PATH . '/../public/images/uploads') . '/';					
					//print_r($path_file);die;
					$filename  = $shipment_files; // of course find the exact filename....
					$file = $path_file.$filename;					
					$validator = new Zend_Validate_File_Exists();
					$validator->addDirectory($path_file);
					
					//zfdebug(mime_content_type($file));
					if($validator->isValid($filename)){					
						header('Pragma: public');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Cache-Control: private', false); // required for certain browsers 
						//header('Content-Type: '.mime_content_type($file));
						header('Content-Disposition: attachment; filename="'. basename($file) . '";');
						header('Content-Transfer-Encoding: binary');
						header('Content-Length: ' . filesize($file));
						readfile($file);
					}else{
						$this->_flashMessenger->addMessage('File does not exist');
						$this->_redirect('finance/receipt-voucher/index');
					}
				}
				break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ErpFinanceReceiptVoucher_model->getRecords();
				$getDate = $ErpFinanceReceiptVoucher_model->getFinanceRecords();
				$this->view->financeDate = $getDate;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
				$storage = new Zend_Session_Namespace("admin_login");
				$data    = $storage->admin_login;
				$role_id = '';
				if( isset($data) ){
					$role_id = $data->role_id;
				}
				$this->view->role_id = $role_id;
                break;
        }
    }
    
	public function customerByAmountAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$dealer_id = $this->_getParam("dealer_id");
			$invoice_id = $this->_getParam("invoice_id");
			$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
			$dealer_data = $ErpDealerMaster_model->getRecord($dealer_id);
			if($dealer_data['dealer_type'] == 1){
				//$ErpSalesInvoice_model = new Application_Model_ErpSalesInvoice();				
				$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
				$receipt = $ErpFinanceReceiptVoucher_model->getReceiptPayment($dealer_id, $invoice_id);
				$result = $ErpFinanceReceiptVoucher_model->getIntCustomerPayment($dealer_id, $invoice_id);
				$dealer_val = $ErpFinanceReceiptVoucher_model->getCustomerOpeningBalance($dealer_id);
				//print_r($receipt); die;
				$total_invoice_amt =  ( $result['total_amount'] );	
				
					$operator = $dealer_val['operator'];
					$transaction_amount = 0;
					if( $operator == 2){					
						$total_invoice = ( $total_invoice_amt) - $receipt['total_paidamount'];						
					}elseif( $operator == 1 ){					
						$total_invoice = ( $total_invoice_amt) - $receipt['total_paidamount'];
					}else{					
						$total_invoice = ( $total_invoice_amt) - $receipt['total_paidamount'];					
					}				
					if($total_invoice_amt >= $receipt['total_paidamount']){
						$transaction_amount = ($total_invoice_amt) - $receipt['total_paidamount'];						
					} 
					
					$array_data = array('total_amount' => $total_invoice, 'transaction_amount' => $transaction_amount );
					//print_r($array_data); die; 
					echo json_encode($array_data); die;
			}else{
				$ErpSalesInvoice_model = new Application_Model_ErpSalesInvoice();
				$result = $ErpSalesInvoice_model->getdealerPayment($dealer_id, $invoice_id);			
				$receipt = $ErpSalesInvoice_model->getReceiptPayment($dealer_id, $invoice_id);
				$get_amount = $ErpSalesInvoice_model->getdealerTotalPayment($dealer_id, $invoice_id);
				
				$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
				$dealer_val = $ErpFinanceReceiptVoucher_model->getCustomerOpeningBalance($dealer_id);
				
				$total_invoice_amt =  round( ( $result['total_amount'] + $get_amount['add_amount']) - ($get_amount['sub_amount']) );	
				
					$operator = $dealer_val['operator'];
					$transaction_amount = 0;
					if( $operator == 2){					
						$total_invoice = ( $total_invoice_amt ) - $receipt['total_paidamount'];						
					}elseif( $operator == 1 ){					
						$total_invoice = ( $total_invoice_amt ) - $receipt['total_paidamount'];
					}else{					
						$total_invoice = ( $total_invoice_amt ) - $receipt['total_paidamount'];					
					}				
					if($total_invoice_amt >= $receipt['total_paidamount']){
						$transaction_amount = ($total_invoice_amt) - $receipt['total_paidamount'];						
					} 
					
					$array_data = array('total_amount' => $total_invoice, 'transaction_amount' => $transaction_amount );
					echo json_encode($array_data); die;
			}
		}
	}
	
	public function customerOperatorAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$dealer_id = $this->_getParam("dealer_id");			
			$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
			$result = $ErpDealerMaster_model->getRecord($dealer_id);
			echo json_encode($result);
			//echo $result['operator'];
			die;         	
        }		
	}
	
	public function recordViewAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$id = $this->_getParam("id");			
			$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
			$result = $ErpFinanceReceiptVoucher_model->getRecord($id);			
			$this->view->paginator = $result;  
        }		
	}
	
	//checked status
	public function ajaxChangesStatusAction() {
        $this->_helper->layout->disableLayout();
        $receipt_voucher_id = $this->_getParam("id");
		if($receipt_voucher_id){		
			$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();			
			$status = 1;
			$updateStatus = array('checked_status' => new Zend_Db_Expr($status));
			$ErpFinanceReceiptVoucher_model->update($updateStatus, array('receipt_voucher_id=?' => $receipt_voucher_id));			
        }
    }
	
	//Get invoice ID's	
	public function getInvoiceIdAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$customerId = $this->_getParam("dealer_id");	
			$dealer_id = explode(',',$customerId);
			//print_r($dealer_id);die;
			$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
			if(($dealer_id[1] == 'C')){ 
			$dealer_data = $ErpDealerMaster_model->getRecord($dealer_id[0]);			
			if($dealer_data['dealer_type'] == 1){
				$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
				$result = $ErpFinanceReceiptVoucher_model->getWayBillIds($dealer_id[0]);
				if( count($result) >0 ){					
					echo '<option value="" label="Select WayBill ID">Select WayBill ID</option>';
					foreach($result as $k=>$val){
					echo '<option value="'.$k.'" label="'.$val.'">'.$val.'</option>';
					}
				} else {
					echo '<option value="" label="No WayBill ID">No WayBill ID</option>';
				}
				die;
			}else{
				$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
				$result = $ErpFinanceReceiptVoucher_model->getInvoiceIds($dealer_id[0]);
				if( count($result) >0 ){
					echo '<option value="" label="Select Invoice ID">Select Invoice ID</option>';
					foreach($result as $k=>$val){
					echo '<option value="'.$k.'" label="'.$val.'">'.$val.'</option>';
					}
				} else {
					echo '<option value="" label="No Invoice ID">No Invoice ID</option>';
				}
				 die;
			}			
			}
			/* if($dealer_id[1] == 'V'){
				$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
				$result = $ErpFinanceReceiptVoucher_model->getInvoiceIds($dealer_id[0]);
				if( count($result) >0 ){
					echo '<option value="" label="Select Invoice ID">Select Invoice ID</option>';
					foreach($result as $k=>$val){
					echo '<option value="'.$k.'" label="'.$val.'">'.$val.'</option>';
					}
				} else {
					echo '<option value="" label="No Invoice ID">No Invoice ID</option>';
				}
				 die;
			} */
		 die;	
		}
	}
	public function getTotalInvoiceAmountAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$customerId = $this->_getParam("dealer_id");	
			$customerType = $this->_getParam("party_type");
			
			$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
			$dealer_data = $ErpDealerMaster_model->getRecord($customerId);
			if($dealer_data['dealer_type'] == 1){
				//$ErpSalesInvoice_model = new Application_Model_ErpSalesInvoice();				
				$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
				$receipt = $ErpFinanceReceiptVoucher_model->getRecPayment($customerId);
				$result = $ErpFinanceReceiptVoucher_model->getIntCustPayment($customerId);
				$dealer_val = $ErpFinanceReceiptVoucher_model->getCustomerOpeningBalance($customerId);
				//print_r($receipt); die;
				$total_invoice_amt =  ( $result['total_amount'] );	
				
					$operator = $dealer_val['operator'];
					$transaction_amount = 0;
					if( $operator == 2){					
						$total_invoice = ( $total_invoice_amt) - $receipt['total_paidamount'];						
					}elseif( $operator == 1 ){					
						$total_invoice = ( $total_invoice_amt) - $receipt['total_paidamount'];
					}else{					
						$total_invoice = ( $total_invoice_amt) - $receipt['total_paidamount'];					
					}				
					if($total_invoice_amt >= $receipt['total_paidamount']){
						$transaction_amount = ($total_invoice_amt) - $receipt['total_paidamount'];						
					} 
					
					$array_data = array('total_amount' => $total_invoice, 'transaction_amount' => $transaction_amount );
					//print_r($array_data); die; 
					echo json_encode($total_invoice); die;
			}else{
					$SalesInvoice_model = new Application_Model_ErpSalesInvoice();
					//if(($customerType == 'D') ||($customerType == 'I')){
					$invoice_data = $SalesInvoice_model->getTotalSum($customerId);
					$RV_total = $SalesInvoice_model->getRVTotal($customerId);
					$dealer_Opening = $SalesInvoice_model->getCustomerOpening($customerId);
					$total_invoice = 0;
					if( $dealer_Opening['operator'] == 2){					
						$total_invoice = ( $invoice_data['grand_total'] - $dealer_Opening['start_balance']) -  $RV_total['total_paid_amount'];						
					}else{					
						$total_invoice = ( $invoice_data['grand_total'] + $dealer_Opening['start_balance']) -  $RV_total['total_paid_amount'];
					}
					
					echo json_encode($total_invoice); die;
						//echo '<pre>';print_r($total_invoice);die;
					//}
			}
		
		}
		die;	
	}
	//get all values for drop down
	public function getAllValuesAction() {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam("id");
		if($id){		
			//customer
			$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
			$customerData = $ErpDealerMaster_model->getAllDropDownList();
			//vendor
			$vendor_model = new Application_Model_ErpVendorMaster();
			$vendorData = $vendor_model->getDropDownList();	
			//bank
			$Bank_model = new Finance_Model_BankDetails();
			$bankData = $Bank_model->getDropdownList();	
			//account
			$Account_model = new Application_Model_Account();
			$accountData = $Account_model->getDropDown();	
			
			echo '<option value="" >Select</option>';
			foreach($customerData as $k=>$val){
				echo '<option value="'.$val.'" label="'.$val.'">'.$val.'</option>';
			}foreach($vendorData as $k1=>$val1){
				echo '<option value="'.$val1.'" label="'.$val1.'">'.$val1.'</option>';
			}foreach($bankData as $k2=>$val2){
				echo '<option value="'.$val2.'" label="'.$val2.'">'.$val2.'</option>';
			}foreach($bankData as $k3=>$val3){
				echo '<option value="'.$val3.'" label="'.$val3.'">'.$val3.'</option>';
			}die;		
        }
    }
}
