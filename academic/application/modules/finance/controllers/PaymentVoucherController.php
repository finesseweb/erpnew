<?php

class Finance_PaymentVoucherController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		  $this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {
        $this->view->module = "finance";
		$this->view->sub_title_name = "paymentvoucher";
        $this->view->action_name = 'Transactions';
        $this->view->controller = 'payment-voucher';
        $PaymentVoucher_model = new Finance_Model_PaymentVoucher();
		$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
		$BankDetails_model = new Application_Model_ErpBankMaster();
		$Account_model = new Application_Model_Account();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->type = $type;
        switch ($type) {
            case "add":
                $PaymentVoucher_form = new Finance_Form_PaymentVoucher();                
                $this->view->form = $PaymentVoucher_form;
                if ($this->getRequest()->isPost()) {
                    if ($PaymentVoucher_form->isValid($this->getRequest()->getPost())) {
                        $data = $PaymentVoucher_form->getValues();
												
						$data['total_amount_hidden'] = 0;
						if( $data['total_amount'] > 0){						
							$data['total_amount_hidden'] = $data['total_amount'] - $data['paid_amount'];
						}
						
						$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
						$party_id = explode(',', $data['vendor_id']);
						if(($party_id[1] == 'C') || ($party_id[1] == 'C')){
							$result = $ErpDealerMaster_model->getRecord($party_id[0]);
							$data['party_names'] = $result['dealer_company_name'];
						}
						if($party_id[1] == 'V'){
							$result = $ErpVendorMaster_model->getRecord($party_id[0]);
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
						$data['vendor_id'] = $party_id[0];
						if(isset($party_id[1])){
							$data['party_type'] =  $party_id[1];
						}		
						
						$vendor_data = $ErpVendorMaster_model->getRecord($data['vendor_id']);
						if($vendor_data['vendor_type'] == 1){
							$data['commercial_invoice_id'] =  $data['invoice_id'];
							$data['invoice_id'] =  '';
						}else{
							$data['invoice_id'] =  $data['invoice_id'];
						}
						
						if( !empty($_FILES["file_upload"]["tmp_name"]) ){
							$uploadPath = realpath(APPLICATION_PATH . '/../public/images/uploads') . '/';
							//print_r($uploadPath);die;
							$file_upload = $PaymentVoucher_form->file_upload;
							//print_r($file_upload);die;
							$fileinfo = $file_upload->getFileInfo();
							//echo '<pre>';print_r($fileinfo['file_upload']);die;
							$data['file_upload'] = $this->_act->uploadfile($fileinfo['file_upload'], $uploadPath);							
						}else{
							$data['file_upload'] = '';
						}
						
						$result = $PaymentVoucher_model->insert($data);	
						$data1['opening_balance'] = $data['balance'];
						$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
						$ErpVendorMaster_model->update($data1, array('vendor_id=?' => $data['vendor_id']));
                        $this->_flashMessenger->addMessage('Finance Successfully added');
                        $this->_redirect('finance/payment-voucher/index');
                    }
                }
                break;
            case 'edit':
                $PaymentVoucher_form = new Finance_Form_PaymentVoucher();                
                $this->view->form = $PaymentVoucher_form;
                $result = $PaymentVoucher_model->getRecord($id);
				$this->view->result = $result;
				
				$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
				$vendor_data = $ErpVendorMaster_model->getRecord($result['vendor_id']);
				if($vendor_data['vendor_type'] == 1){
					//$data['commercial_invoice_id'] =  $data['invoice_id'];
					//$data['invoice_id'] =  '';
					//$data['']="Select Sales Order ID"; 
					if(strlen($result['commercial_invoice_id'])==1){
					$data[$result['commercial_invoice_id']]= @(PCI_PREFIX)."000".$result['commercial_invoice_id'];
					}else
					if(strlen($result['commercial_invoice_id'])==2){
					$data[$result['commercial_invoice_id']]= @(PCI_PREFIX)."00".$result['commercial_invoice_id'];
					}else
					if(strlen($result['commercial_invoice_id'])==3){
					$data[$result['commercial_invoice_id']]= @(PCI_PREFIX)."0".$result['commercial_invoice_id'];
					}
					else{
					$data[$result['commercial_invoice_id']]= @(PCI_PREFIX)."".$result['commercial_invoice_id'];
					}
					ksort($data);
					$PaymentVoucher_form->getElement("invoice_id")
										->setAttrib('readonly','readonly')
										->setAttrib('class',array('form-control'))
										->setMultiOptions($data);
				}else{
					//$data['invoice_id'] =  $data['invoice_id'];
					if(strlen($result['invoice_id'])==1){
					$data[$result['invoice_id']]= @(PI_PREFIX)."000".$result['invoice_id'];
					}else
					if(strlen($result['invoice_id'])==2){
					$data[$result['invoice_id']]= @(PI_PREFIX)."00".$result['invoice_id'];
					}else
					if(strlen($result['invoice_id'])==3){
					$data[$result['invoice_id']]= @(PI_PREFIX)."0".$result['invoice_id'];
					}
					else{
					$data[$result['invoice_id']]= @(PI_PREFIX)."".$result['invoice_id'];
					}
					ksort($data);
					$PaymentVoucher_form->getElement("invoice_id")
										->setAttrib('readonly','readonly')
										->setAttrib('class',array('form-control'))
										->setMultiOptions($data);
				}
				 $data2 = $ErpVendorMaster_model->getDropDown();
				$data1[$result['vendor_id'].','.$result['party_type']] = $result['party_names'].'('.$result['party_type'].')';
					ksort($data1);
					$PaymentVoucher_form->getElement("vendor_id")
										->setAttrib('readonly','readonly')
										//->setAttrib('class',array('form-control'))
										->setMultiOptions($data1)
										->addMultiOptions($data2)
										->setAttrib('class', array('form-control','select2'));	
				
				//echo'<pre>';print_r($data1); die;
				//print_r($result['vendor_id'].','.$result['party_type']); die;
                $PaymentVoucher_form->populate($result);
                if ($this->getRequest()->isPost()) {  
					if (!$PaymentVoucher_form->file_upload->isUploaded() && $this->getRequest()->getPost('prev_image')) {
						$PaymentVoucher_form->file_upload->setRequired(false)->setValidators(array());
                    }
                    if ($PaymentVoucher_form->isValid($this->getRequest()->getPost())) {
						
                        $data = $PaymentVoucher_form->getValues();
						//print_r($data); die;
						$data['total_amount_hidden'] = 0;
						if( $data['total_amount'] > 0){						
							$data['total_amount_hidden'] = $data['total_amount'] - $data['paid_amount'];
						}
						
						$uploadPath = realpath(APPLICATION_PATH . '/../public/images/uploads') . '/';
						if( !empty($_FILES["file_upload"]["tmp_name"]) ){ 
                            $file_upload = $PaymentVoucher_form->file_upload;
                            $fileinfo = $file_upload->getFileInfo();
                            $data['file_upload'] = $this->_act->uploadfile($fileinfo['file_upload'], $uploadPath);
							//print_r($data['file_upload']); die;
                            //unlink($uploadPath . $this->getRequest()->getPost('prev_image'));
                        } else {
                            //$data['file_upload'] = $this->getRequest()->getPost('prev_image');
							unset($data['file_upload']);
                        }
						
						$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
						$party_id = explode(',', $data['vendor_id']);
						if(($party_id[1] == 'C') || ($party_id[1] == 'C')){
							$result = $ErpDealerMaster_model->getRecord($party_id[0]);
							$data['party_names'] = $result['dealer_company_name'];
						}
						if($party_id[1] == 'V'){
							$result = $ErpVendorMaster_model->getRecord($party_id[0]);
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
						$data['vendor_id'] = $party_id[0];
						if(isset($party_id[1])){
							$data['party_type'] =  $party_id[1];
						}
						
						$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
						$vendor_data = $ErpVendorMaster_model->getRecord($data['vendor_id']);
						if($vendor_data['vendor_type'] == 1){
							$data['commercial_invoice_id'] =  $data['invoice_id'];
							$data['invoice_id'] =  '';
						}else{
							$data['invoice_id'] =  $data['invoice_id'];
						}
						
						/* $data['opening_balance1'] = $data['balance']; */
                      /* if( !empty($_FILES["file_upload"]["tmp_name"]) ){
						   if( !empty($data['file_upload']) ){
							$folder_name = $id;
							$dirPath = $uploadPath = realpath(APPLICATION_PATH . '/../public/images/uploads') . '/'.$folder_name.'/';
							$file_name = $_FILES["file_upload"]["name"]; 
							//print_r($file_name); die;
							$tem_name = $_FILES["file_upload"]["tmp_name"];
							if(move_uploaded_file($tem_name, $dirPath.$file_name)){
								//echo "File is valid, and was successfully uploaded";
							}
								
							
						}	*/
						//if( empty($data['file_upload'] )){
							//$data['file_upload'] = $result['file_upload'];
							//print_r($data['file_upload']); die;
						//}
						//else{
							//$data['file_upload'] = '';
						//}
					
						$PaymentVoucher_model->update($data, array('payment_voucher_id=?' => $id));
						$getMainRecord = $PaymentVoucher_model->getRecord($id);
						$result = $PaymentVoucher_model->editRecordByVendor($getMainRecord['vendor_id'], $id);						
							if( count($result) > 0 ){						
								$delete_paidamt = 0;
								$i=1;
								foreach($result as $value){								
									$getFirstRecord = $PaymentVoucher_model->getFirstRecordByVendor($value['vendor_id'], $value['payment_voucher_id']);
									//echo '<pre>';
									//print_r($getFirstRecord);
									$total_amount = $getFirstRecord['balance'];									
									if( $value['operator'] == 2){					
										$opening_balance = ($getFirstRecord['balance'] - $value['paid_amount']);
									}else{							
										$opening_balance = ($getFirstRecord['balance'] + $value['paid_amount']);
									}
									
									//$data1['opening_balance'] = $opening_balance;
									//$dealer_model = new Application_Model_ErpDealerMaster();							
									//$dealer_model->update($data1, array('dealer_id=?' => $value['dealer_id']));	
									$reupdate_data = array('opening_balance1' => $opening_balance, 'total_amount' => $total_amount, 'balance' => $opening_balance);	
									$PaymentVoucher_model->update($reupdate_data, array('payment_voucher_id =?' => $value['payment_voucher_id']));
									
								$i++;
								}
							} 
						//die;
                        $this->_flashMessenger->addMessage('Finance Updated Successfully');
                        $this->_redirect('finance/payment-voucher/index');
                    }
                }
                break;
			case 'view':
				$messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $PaymentVoucher_model->getInvoiceRecords($id);				
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
            case 'delete':
                $data['status'] = 2;
                if ($id){					
					
					$getRecord = $PaymentVoucher_model->getRecord($id);					
					$result = $PaymentVoucher_model->getAfterRecordByVendor($getRecord['vendor_id'], $id);
					$vendor_id = '';
					$payment_voucher_id = '';
					if( count($result) > 0 ){ 
						$total_amount = $getRecord['total_amount'];								
						if( $result['operator'] == 2){					
							$opening_balance = ($getRecord['total_amount'] - $result['paid_amount']);																
						}else{							
							$opening_balance = ($getRecord['total_amount'] + $result['paid_amount']);								
						}
						$data1=array('transaction_amount' => new Zend_Db_Expr("transaction_amount +".$getRecord['transaction_amount']), 'opening_balance1' => $opening_balance, 'total_amount' => $total_amount, 'balance' => $opening_balance);
						$PaymentVoucher_model->update($data1, array('payment_voucher_id =?' => $result['payment_voucher_id']));
					$vendor_id = $result['vendor_id'];
					$payment_voucher_id = $result['payment_voucher_id'];
					}
					
					$PaymentVoucher_model->update($data, array('payment_voucher_id=?' => $id));
					if( !empty($result) ){
						$getResult = $PaymentVoucher_model->getRecordByVendor($vendor_id, $payment_voucher_id);						
						//echo '<pre>'; 
						//print_r($getResult); die;
						if( count($getResult) > 0 ){
							$delete_paidamt = 0;
							$i=1;
							foreach($getResult as $value){							 
								$getFirstRecord = $PaymentVoucher_model->getFirstRecordByVendor($value['vendor_id'], $value['payment_voucher_id']);
								$total_amount = $getFirstRecord['balance'];								
								if( $value['operator'] == 2){					
									$opening_balance = ($getFirstRecord['balance'] - $value['paid_amount']);																
								}else{							
									$opening_balance = ($getFirstRecord['balance'] + $value['paid_amount']);								
								}
								
								//$data1['opening_balance'] = $opening_balance;							
								//$dealer_model = new Application_Model_ErpDealerMaster();							
								//$dealer_model->update($data1, array('vendor_id=?' => $value['vendor_id'])); 						
								$reupdate_data = array('opening_balance1' => $opening_balance, 'total_amount' => $total_amount, 'balance' => $opening_balance);	
								$PaymentVoucher_model->update($reupdate_data, array('payment_voucher_id =?' => $value['payment_voucher_id']));
								//echo '<pre>';
								//print_r($reupdate_data);
							$i++;
							}
						}
					}
					
					/* $getRecord = $PaymentVoucher_model->getRecord($id);
					$result = $PaymentVoucher_model->getRecordByVendor($getRecord['vendor_id'], $id);									
					$data1=array('transaction_amount' => new Zend_Db_Expr("transaction_amount +".$getRecord['transaction_amount']));
					$PaymentVoucher_model->update($data1, array('payment_voucher_id =?' => $result['payment_voucher_id']));
					
                    $PaymentVoucher_model->update($data, array('payment_voucher_id=?' => $id));	*/				
				}
                $this->_flashMessenger->addMessage('Finance Deleted Successfully');
                $this->_redirect('finance/payment-voucher/index');
                break;
				case 'download':
				//echo "test"; die;
                $this->_helper->viewRenderer->setNoRender(true);
				$this->_helper->layout->disableLayout();
				$shipment_files = $this->_getParam("file");	
				//print_r($shipment_files); die;			
				if ($shipment_files) { 					
					$path_file = realpath(APPLICATION_PATH . '/../public/images/uploads') . '/';					
					//print_r($path_file);die;
					$filename  = $shipment_files; // of course find the exact filename....
					$file = $path_file.$filename;
						//print_r($file); die;
					$validator = new Zend_Validate_File_Exists();
					$validator->addDirectory($path_file);
					//print_r($path_file); die;
					
					//zfdebug(mime_content_type($file));
					if($validator->isValid($filename)){	
						//print_r($file); die;					
						header('Pragma: public');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Cache-Control: private', false); // required for certain browsers 
						//header('Content-Type: '.mime_content_type($file));
						header('Content-Disposition: attachment; filename="'. basename($file) . '";');
						header('Content-Transfer-Encoding: binary');
						header('Content-Length: ' . filesize($file));
						readfile($file);
						//print_r($file); die;
					}
				}else{
						$this->_flashMessenger->addMessage('File does not exist');
						$this->_redirect('finance/payment-voucher/index');
					}
				
				break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $PaymentVoucher_model->getRecords();
				//echo '<pre>';print_r($result);die;
				$getDate = $PaymentVoucher_model->getFinanceRecords();
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
	
	public function ajaxBankDetailAction() {
	
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('bank_id');
        if ($id != '') {          
            $model = new Finance_Model_PaymentVoucher();
            $result = $model->getChequeNumber($id);
			echo $result; 
        }
		die;
    }
	
	public function vendorByAmountAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$vendor_id = $this->_getParam("vendor_id");
			$invoice_id = $this->_getParam("invoice_id");
			$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
			$vendor_data = $ErpVendorMaster_model->getRecord($vendor_id);
			if($vendor_data['vendor_type'] == 1){
				//$ErpPurchaseInvoice_model = new Invoice_Model_ErpPurchaseInvoice();						
				
				$PaymentVoucher_model = new Finance_Model_PaymentVoucher();	
				$receipt = $PaymentVoucher_model->getPayment($vendor_id, $invoice_id);
				$vendor_val = $PaymentVoucher_model->getVendorOpeningBalance($vendor_id);
				$result = $PaymentVoucher_model->getIntVendorPayment($vendor_id, $invoice_id);
				$ErpPurchaseInvoice_model = new Invoice_Model_ErpPurchaseInvoice();
				$result_domestic = $ErpPurchaseInvoice_model->getVendorPayment($vendor_id, $invoice_id);
				$total_invoice_amt =  round( $result['total_amount'] + $result_domestic['total_amount'] );				
				$operator = $vendor_val['operator'];
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
				echo json_encode($array_data); die;			
			}else{			
				$ErpPurchaseInvoice_model = new Invoice_Model_ErpPurchaseInvoice();
				$result = $ErpPurchaseInvoice_model->getVendorPayment($vendor_id, $invoice_id);			
				$receipt = $ErpPurchaseInvoice_model->getPayment($vendor_id, $invoice_id);
				$PaymentVoucher_model = new Finance_Model_PaymentVoucher();			
				//$getExtraAmount = $ErpPurchaseInvoice_model->getVendorTotalPayment($vendor_id, $invoice_id);	
				$vendor_val = $PaymentVoucher_model->getVendorOpeningBalance($vendor_id);
				
				//$total_invoice_amt =  ( $result['total_amount'] + $result['add_amount']) - ($result['sub_amount']);
				//$total_invoice_amt =  ( $result['total_amount'] + $getExtraAmount['add_amount']) - ($getExtraAmount['sub_amount']);
				$result_international = $PaymentVoucher_model->getIntVendorPayment($vendor_id, $invoice_id);
				$total_invoice_amt =  ( $result['total_amount'] + $result_international['total_amount']);					
				
				$operator = $vendor_val['operator'];
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
				echo json_encode($array_data); die;
			}	
			//if($result['operator'] == 2){
			/* $total_invoice_amt =  ( $result['total_amount'] );
			if( $receipt['total_paidamount'] == 0 ){
				if( $vendor_val['operator'] == 2){					
					echo ($total_invoice_amt - $vendor_val['start_balance']);
				}elseif( $vendor_val['operator'] == 1 ){
					echo ( $total_invoice_amt + $vendor_val['start_balance']);
				}else{
					echo ( $total_invoice_amt + $vendor_val['start_balance']);					
				}	
			}else{	
				if( $result['operator'] == 2){
					$total_amount_invoice = ($total_invoice_amt - $vendor_val['start_balance']);					
					$balance = $total_amount_invoice - ($receipt['total_paidamount']);
					echo $balance; die;
				}else{
					$total_amount_invoice = ($total_invoice_amt + $vendor_val['start_balance']);					
					$balance = $total_amount_invoice - ($receipt['total_paidamount']);
					echo $balance; die;
				}
			} */			
			die;         	
        }		
	}
	
	public function vendorOperatorAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$vendor_id = $this->_getParam("vendor_id");			
			$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
			$result = $ErpVendorMaster_model->getRecord($vendor_id);
			//print_r($result);
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
			$PaymentVoucher_model = new Finance_Model_PaymentVoucher();
			$result = $PaymentVoucher_model->getRecord($id);			
			$this->view->paginator = $result;  
        }		
	}
	
	//checked status
	public function ajaxChangesStatusAction() {
        $this->_helper->layout->disableLayout();
        $payment_voucher_id         = $this->_getParam("id");
		if($payment_voucher_id){		
			$PaymentVoucher_model = new Finance_Model_PaymentVoucher();	
			$status = 1;
			$updateStatus = array('checked_status' => new Zend_Db_Expr($status));
			$PaymentVoucher_model->update($updateStatus, array('payment_voucher_id=?' => $payment_voucher_id));			
        }
    }
	
	//Get invoice ID's	
	public function getInvoiceIdAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$vendor_id = $this->_getParam("vendor_id");			
			$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
			$vendor_data = $ErpVendorMaster_model->getRecord($vendor_id);
			$PaymentVoucher_model = new Finance_Model_PaymentVoucher();
			$result = $PaymentVoucher_model->getCommercialInvoiceIds($vendor_id);
			if( count($result) > 0){
				//print_r($result); die;
				if( count($result) >0 ){					
					echo '<option value="" label="Select Commercial Invoice ID">Select Commercial Invoice ID</option>';
					foreach($result as $k=>$val){
					echo '<option value="'.$k.'" label="'.$val.'">'.$val.'</option>';
					}
				} else {
					echo '<option value="" label="No Commercial Invoice ID">No Commercial Invoice ID</option>';
				}
				die;
			}else{				
				$result = $PaymentVoucher_model->getInvoiceIds($vendor_id);
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
			
		 die;	
		}
	}
	
	public function getTotalInvoiceAmountAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) 
        {			
			$vendor_id = $this->_getParam("vendor_id");
			$PaymentVoucher_model = new Finance_Model_PaymentVoucher();	
				$result = $PaymentVoucher_model->getVendorDocTotalBalance($vendor_id);			
				$receipt = $PaymentVoucher_model->getTotalDocPayment($vendor_id);
				$vendor_val = $PaymentVoucher_model->getVendorOpeningBalance($vendor_id);
				$international = $PaymentVoucher_model->getTotalIntVendorPayment($vendor_id);
				$total_invoice_amt =  ( $result['total_amount'] + $international['total_amount']);					
				echo $total_invoice_amt; die;
				$operator = $vendor_val['operator'];
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
				
				$array_data = $transaction_amount;
				echo json_encode($total_invoice); die;
				//echo json_encode($total_invoice); 
				//print_r($invoice_data);die;
		}
		die;	
	}
	
}