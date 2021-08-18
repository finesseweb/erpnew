<?php


class Invoice_PurchaseController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {
        $this->view->action_name = 'index';
        $this->view->sub_title_name = 'purchase';
        $ErpPurchaseInvoice_model = new Invoice_Model_ErpPurchaseInvoice();
		$PurchaseInvoiceItems_model=new Invoice_Model_ErpPurchaseInvoiceItems();
        $purchase_invoice_id = $this->_getParam("id");
		// Items 
		$ItemsMaster_model=new Application_Model_ErpItemsMaster();
		$this->view->items=$ItemsMaster_model->ItemNameDropDown();		
		$ItemsCategoryMaster_model = new Application_Model_ErpItemsCategoryMaster();
		$this->view->categories = $ItemsCategoryMaster_model->getDropDownList();
		$ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
		$this->view->vendordata = $ErpVendorMaster_model->getDropDownList();
		
		$ErpPurchaseInvoice_form = new Invoice_Form_ErpPurchaseInvoice();
		// End
		$partyname = $this->_getParam("partyname");
		$from = $this->_getParam("from");
		$to = $this->_getParam("to");
		
		//Account master 
		$account_model = new Application_Model_Account();
		$this->view->account = $account_model->getDropDown();
		
        $type = $this->_getParam("type");
        switch ($type) {
            case "add":
                
                $this->view->type = $type;
                $this->view->ErpPurchaseInvoice_form = $ErpPurchaseInvoice_form;
                if ($this->getRequest()->isPost()) {
                    if ($ErpPurchaseInvoice_form->isValid($this->getRequest()->getPost())) {
                        $data = $ErpPurchaseInvoice_form->getValues();
						
							$ErpItemsMaster_model=new Application_Model_ErpItemsMaster();
							$purchase_quatation_data=$this->getRequest()->getPost("purchase_invoice");
													
							//echo '<pre>';print_r($this->getRequest()->getPost());die;							
							//$entry_data['direct_invoice']=1;
							$entry_data['vendor_id']=$this->getRequest()->getPost('vendor_id');
							$entry_data['purchase_invoice_vehicle_no']=$this->getRequest()->getPost('vehicle_no');
							$entry_data['purchase_invoice_own_vehicle_3rd_party']=$this->getRequest()->getPost('vehicle');
							$entry_data['invoice_date']=$this->getRequest()->getPost('invoice_date');
							$entry_data['entry_field']=$this->getRequest()->getPost('entry_field');
							$entry_data['operator1']=$this->getRequest()->getPost('operator1');
							$entry_data['price']=$this->getRequest()->getPost('price');
							$entry_data['operator2']=$this->getRequest()->getPost('operator2');
							$entry_data['price1']=$this->getRequest()->getPost('price1');
							$entry_data['entry_field2']=$this->getRequest()->getPost('entry_field2');
							$entry_data['operator3']=$this->getRequest()->getPost('operator3');
							$entry_data['price2']=$this->getRequest()->getPost('price2');
							$entry_data['total']=$this->getRequest()->getPost('total');
							$entry_data['grand_total_amount']=$this->getRequest()->getPost('grand_total_amt');
							//echo '<pre>';print_r($purchase_quatation_data);die;
							$purchase_invoice_no=$ErpPurchaseInvoice_model->insert($entry_data); 
							
							foreach(array_filter($purchase_quatation_data['purchase_invoice_item_id']) as $k=>$erp_inventory_grn_item_id){
								//$entry_data=array();
								
								$ItemsMaster_data=array('item_quantity' => new Zend_Db_Expr("item_quantity +".$purchase_quatation_data['purchase_invoice_item_approved_quantity'][$k]));
								$ErpItemsMaster_model->update($ItemsMaster_data, array('item_id=?' =>$erp_inventory_grn_item_id));
								
								$item_data['purchase_invoice_no']=$purchase_invoice_no;                                        
								$item_data['purchase_grn_category_id']=$purchase_quatation_data['purchase_grn_category_id'][$k];
								$item_data['item_id']=$erp_inventory_grn_item_id;
								$item_data['purchase_invoice_actual_quantity']=$purchase_quatation_data['purchase_invoice_actual_quantity'][$k];
								$item_data['purchase_invoice_item_approved_quantity']=$purchase_quatation_data['purchase_invoice_item_approved_quantity'][$k];
								$item_data['purchase_invoice_item_rejected_quantity']=$purchase_quatation_data['purchase_invoice_item_rejected_quantity'][$k];
								$item_data['purchase_grn_item_code']=$purchase_quatation_data['purchase_grn_item_code'][$k];
								$item_data['purchase_invoice_item_price']=$purchase_quatation_data['purchase_invoice_item_price'][$k];
								$item_data['total_price']=$purchase_quatation_data['total_price'][$k];
								$item_data['account_id']=$purchase_quatation_data['purchase_invoice_account_id'][$k];
								//echo '<pre>'; print_r($entry_data); exit;
								$lastinsert_id = $PurchaseInvoiceItems_model->insert($item_data);								
							}
							
                        $this->_flashMessenger->addMessage('Invoice Added Successfully ');
                        $this->_redirect('invoice/purchase/index');
                    }
                }
                break;
				
				case "edit":
				$result           = $ErpPurchaseInvoice_model->getRecord($purchase_invoice_id);
				//print_r($result);die;
				$inventory_result   = $PurchaseInvoiceItems_model->getRecord($result['purchase_invoice_id']);	
				//echo '<pre>';print_r($inventory_result);die;
				$this->view->result = $result;
				$this->view->inventory_result = $inventory_result;
                $this->view->type = $type;
                $this->view->ErpPurchaseInvoice_form = $ErpPurchaseInvoice_form;
                if ($this->getRequest()->isPost()) {
                    if ($ErpPurchaseInvoice_form->isValid($this->getRequest()->getPost())) {
                        
							$purchase_quatation_data=$this->getRequest()->getPost("purchase_invoice");
							//$entry_data['purchase_order_id']=$data['purchase_order_id'];
							$entry_data['vendor_id']=$this->getRequest()->getPost('vendor_id');
							$entry_data['purchase_invoice_vehicle_no']=$this->getRequest()->getPost('vehicle_no');
							$entry_data['purchase_invoice_own_vehicle_3rd_party']=$this->getRequest()->getPost('vehicle');
							$entry_data['invoice_date']=$this->getRequest()->getPost('invoice_date');
							$entry_data['entry_field']=$this->getRequest()->getPost('entry_field');
							$entry_data['operator1']=$this->getRequest()->getPost('operator1');
							$entry_data['price']=$this->getRequest()->getPost('price');
							$entry_data['operator2']=$this->getRequest()->getPost('operator2');
							$entry_data['price1']=$this->getRequest()->getPost('price1');
							$entry_data['entry_field2']=$this->getRequest()->getPost('entry_field2');
							$entry_data['operator3']=$this->getRequest()->getPost('operator3');
							$entry_data['price2']=$this->getRequest()->getPost('price2');
							//echo '<pre>';print_r($purchase_quatation_data);die;
							$entry_data['total']=$this->getRequest()->getPost('total');
							$entry_data['grand_total_amount']=$this->getRequest()->getPost('grand_total_amt');
							//echo '<pre>';print_r($entry_data);die;
							$purchase_invoice_no=$ErpPurchaseInvoice_model->update($entry_data,(array('purchase_invoice_id =?' => $purchase_invoice_id))); 
							//echo '<pre>';print_r($purchase_invoice_id);die;
							$PurchaseInvoiceItems_model->delete(array('	purchase_invoice_no =?' => $purchase_invoice_id));
							//echo '<pre>';print_r($purchase_quatation_data);die;
							foreach(array_filter($purchase_quatation_data['purchase_invoice_item_id']) as $k=>$erp_inventory_grn_item_id){
								$entry_data=array();
								
								$entry_data['purchase_invoice_no']=$purchase_invoice_id;                                        
								$entry_data['purchase_grn_category_id']=$purchase_quatation_data['purchase_grn_category_id'][$k];
								$entry_data['item_id']=$erp_inventory_grn_item_id;
								$entry_data['purchase_invoice_actual_quantity']=$purchase_quatation_data['purchase_invoice_actual_quantity'][$k];
								$entry_data['purchase_invoice_item_approved_quantity']=$purchase_quatation_data['purchase_invoice_item_approved_quantity'][$k];
								$entry_data['purchase_invoice_item_rejected_quantity']=$purchase_quatation_data['purchase_invoice_item_rejected_quantity'][$k];
								$entry_data['purchase_grn_item_code']=$purchase_quatation_data['purchase_grn_item_code'][$k];
								$entry_data['purchase_invoice_item_price']=$purchase_quatation_data['purchase_invoice_item_price'][$k];
								$entry_data['total_price']=$purchase_quatation_data['total_price'][$k];
								$entry_data['account_id']=$purchase_quatation_data['purchase_invoice_account_id'][$k];
								//echo '<pre>'; print_r($entry_data); exit;
								$lastinsert_id = $PurchaseInvoiceItems_model->insert($entry_data);								
							}
							
                        $this->_flashMessenger->addMessage('Invoice Updated Successfully ');
						if( isset($partyname) ){
							$this->_redirect('finance/journal/invoice-statement/partyname/'.$partyname.'/from/'.$from.'/to/'.$to); 
						}else{
							$this->_redirect('invoice/purchase/index');
						}
                        
                    }
                }
                break;
				
				 case 'delete':
                if ($purchase_invoice_id) {
					$data['purchase_invoice_status'] = 2;
                    $ErpPurchaseInvoice_model->update($data,array('purchase_invoice_id =?' => $purchase_invoice_id));
                    $this->_flashMessenger->addMessage('Purchase Invoice Deleted');
                    $this->_redirect('invoice/purchase/index');
                }
                break;

            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ErpPurchaseInvoice_model->getRecords();
				//echo'<pre>';print_r($result); die;
				$getDate = $ErpPurchaseInvoice_model->getFinanceRecords();
				$this->view->financeDate = $getDate;
              // echo '<pre>'; print_r($result);die;
				$page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function getInvoiceTotal($grn_id) {
        $InventoryGrn_model = new Application_Model_ErpInventoryGrn();
        $ErpPurchaseOrder_model = new Application_Model_ErpPurchaseOrder();
        $oreder_record = $InventoryGrn_model->getRecord($grn_id);
        $grand = 0;
        $grand_discount = 0;
        if ($oreder_record['purchase_order_id'] == '-1') {
            $data = array();
            $orderDetails = $InventoryGrn_model->getGrnItems($grn_id);
            foreach ($orderDetails as $item) {
                $discount_amount = ($item['purchase_quatation_disc_rate'] / 100) * ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']);
                //$total = ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']) - $discount_amount;
				$total = ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']);
                $grand+=$total;
                $grand_discount+= $discount_amount;
            }
            $vat = ($orderDetails[0]['vendor_vat']) ? $orderDetails[0]['vendor_vat'] : 0;
            $vat_total = ($vat / 100) * $grand;
            $grand_totals = $grand + $vat_total;
            $data['grand_total'] = $grand_totals;
            $data['vat_total'] = $vat_total;
            $data['grand_discount'] = $grand_discount;
            return $data;
        } else if ($oreder_record['purchase_order_id'] > '-1') {
            $oreder_record = $ErpPurchaseOrder_model->getRecord($oreder_record['purchase_order_id']);
            if ($oreder_record['purchase_quotation_id'] > '-1') {

                $orderDetails = $ErpPurchaseOrder_model->getOrderdetail($oreder_record['purchase_order_id']);
                $productDetails = $ErpPurchaseOrder_model->getOrderItems($oreder_record['purchase_order_id'], $oreder_record);
                $grand = 0;
                $grand_discount = 0;
                foreach ($productDetails as $item) {
                    $discount_amount = ($item['purchase_quatation_disc_rate'] / 100) * ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']);
                    //$total = ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']) - $discount_amount;
					$total = ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']);
                    $grand+=$total;
                    $grand_discount+= $discount_amount;
                }

                $vat = ($orderDetails->vendor_vat) ? $orderDetails->vendor_vat : 0;
                $vat_total = ($vat / 100) * $grand;
                $grand_totals = $grand + $vat_total;
                $data = array();
                $data['grand_total'] = $grand_totals;
                $data['vat_total'] = $vat_total;
                $data['grand_discount'] = $grand_discount;
                return $data;
            } else {
                $orderDetails = $ErpPurchaseOrder_model->getOrderdetail($oreder_record['purchase_order_id']);
                $productDetails = $ErpPurchaseOrder_model->getOrderItems($oreder_record['purchase_order_id'], $oreder_record);
                $grand = 0;
                $grand_discount = 0;
                foreach ($productDetails as $item) {
                    $discount_amount = ($item['purchase_quatation_disc_rate'] / 100) * ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']);
                    //$total = ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']) - $discount_amount;
					$total = ($item['purchase_quatation_item_unit_price'] * $item['purchase_quatation_items_quantity']);
                    $grand+=$total;
                    $grand_discount+= $discount_amount;
                }

                $vat = ($orderDetails->vendor_vat) ? $orderDetails->vendor_vat : 0;
                $vat_total = ($vat / 100) * $grand;
                $grand_totals = $grand + $vat_total;
                $data = array();
                $data['grand_total'] = $grand_totals;
                $data['vat_total'] = $vat_total;
                $data['grand_discount'] = $grand_discount;
                return $data;
            }
        }
    }

    public function getInvoiceAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $cmp_model = new Application_Model_ErpCompanyMaster;
            $this->view->cmpDetail = $cmp_model->getRecord(2);
            $grn_id = $this->getRequest()->getPost("grn");
			$this->view->close = $this->getRequest()->getPost("close");
			
           // $InventoryGrn_model = new Application_Model_ErpInventoryGrn();
            //$ErpPurchaseOrder_model = new Application_Model_ErpPurchaseOrder();
           // $grn_record = $InventoryGrn_model->getRecord($grn_id);
            $Purchase_invoice_model = new Invoice_Model_ErpPurchaseInvoice();
		   $result = $Purchase_invoice_model->getRecordbyId($grn_id);
		   
		   $Purchase_invoice_items_model = new Invoice_Model_ErpPurchaseInvoiceItems();
		   $item_Result = $Purchase_invoice_items_model->getRecord($result['purchase_invoice_id']);
		   //if (isset($result)) {
                $this->view->invoiceData = $result;
				$this->view->itemInvoiceData = $item_Result;
            //}
          /*  if ($grn_record['purchase_order_id'] == '-1') {
                $this->view->grn_items = $InventoryGrn_model->getGrnItems($grn_id);
                $this->view->direct_grn = true;
            } else if ($grn_record['purchase_order_id'] > '-1') {
                $oreder_record = $ErpPurchaseOrder_model->getRecord($grn_record['purchase_order_id']);
                if ($oreder_record['purchase_quotation_id'] > '-1') {

                    $this->view->orderDetails = $ErpPurchaseOrder_model->getOrderdetail($oreder_record['purchase_order_id']);
                    $this->view->orderitems = $ErpPurchaseOrder_model->getOrderItems($oreder_record['purchase_order_id'], $oreder_record);
                } else {
                    $this->view->orderDetails = $ErpPurchaseOrder_model->getOrderdetail($oreder_record['purchase_order_id']);
                    $this->view->orderitems = $ErpPurchaseOrder_model->getOrderItems($oreder_record['purchase_order_id'], $oreder_record);
                }
            } */
        } else {
            exit;
        }
    }

    public function getInvoiceDetailsAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $cmp_model = new Application_Model_ErpCompanyMaster;
            $this->view->cmpDetail = $cmp_model->getRecord(2);
            $invoice_id = $this->getRequest()->getPost("id");
			$this->view->close = $this->getRequest()->getPost("close");
            $ErpPurchaseInvoice_model = new Invoice_Model_ErpPurchaseInvoice();
            $grn_id = $ErpPurchaseInvoice_model->getGrnId($invoice_id);
            $InventoryGrn_model = new Application_Model_ErpInventoryGrn();
            $ErpPurchaseOrder_model = new Application_Model_ErpPurchaseOrder();
            $grn_record = $InventoryGrn_model->getRecord($grn_id);
            $Purchase_invoice_model = new Application_Model_ErpPurchaseInvoice();
            $result = $Purchase_invoice_model->getRecordbyId($grn_id);
           
            if (isset($result)) {
                $this->view->invoiceData = $result;
            }
            if ($grn_record['purchase_order_id'] == '-1') {
                $this->view->grn_items = $InventoryGrn_model->getGrnItems($grn_id);
                $this->view->direct_grn = true;
            } else if ($grn_record['purchase_order_id'] > '-1') {
                $oreder_record = $ErpPurchaseOrder_model->getRecord($grn_record['purchase_order_id']);
                if ($oreder_record['purchase_quotation_id'] > '-1') {

                    $this->view->orderDetails = $ErpPurchaseOrder_model->getOrderdetail($oreder_record['purchase_order_id']);
                    $this->view->orderitems = $ErpPurchaseOrder_model->getOrderItems($oreder_record['purchase_order_id'], $oreder_record);
                } else { 
                    $this->view->orderDetails = $ErpPurchaseOrder_model->getOrderdetail($result['purchase_order_id']);
                    $this->view->orderitems = $ErpPurchaseOrder_model->getOrderItems($result['purchase_order_id'], $oreder_record);
                }
            }
            $this->_helper->viewRenderer("get-invoice");
        } else {
            exit;
        }
    }
	
	public function downloadAction() {
        //$this->_helper->layout->disableLayout();       
            $cmp_model = new Application_Model_ErpCompanyMaster;
            $this->view->cmpDetail = $cmp_model->getRecord(2);
            $grn_id = $this->_getParam("id"); 
			$this->view->close = '';
            $Purchase_invoice_model = new Invoice_Model_ErpPurchaseInvoice();
            $result = $Purchase_invoice_model->getRecordbyId($grn_id);
			
			$Purchase_invoice_items_model = new Invoice_Model_ErpPurchaseInvoiceItems();
			$item_Result = $Purchase_invoice_items_model->getRecord($result['purchase_invoice_id']);
			//print_r($item_Result);die;
                $this->view->invoiceData = $result;
				$this->view->itemInvoiceData = $item_Result;
				
			$pdfheader = $this->view->render('purchase/pdfheader.phtml');
			$pdffooter = $this->view->render('purchase/pdffooter.phtml');				
			$htmlcontent = $this->view->render('purchase/pdf-get-invoice.phtml');							
			$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Invoice");
    }
	public function ajaxCategoryItemsAction()
    {
        $this->_helper->layout->disableLayout();
        if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ())
        {
            $category_id = $this->_getParam("name");
            //echo $category_id; exit;
            if($category_id)
            { 
                $Erp_enquiry_category_items_model = new Application_Model_ErpItemsMaster();                      
                $category_items=$Erp_enquiry_category_items_model->getCategoryItems($category_id);
				echo '<option value="">Select Item</option>';	  
				if( count($category_items) > 0){			
					foreach($category_items as $key=>$val){
						$selected = '';										
						echo '<option value="'.$key.'" label="'.$val.'">'.$val.'</option>';
					} 
					
				}
            }
			die;
        }
	
    }
	public function ajaxItemDataAction()
    {
        $this->_helper->layout->disableLayout();
        if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ())
        {
            $item_id = $this->_getParam("name");
            //echo $category_id; exit;
            if($item_id)
            { 
                $Erp_enquiry_category_items_model = new Application_Model_ErpItemsMaster();                      
                $item_code=$Erp_enquiry_category_items_model->getItemsCodeAndPrice($item_id);  
				echo json_encode($item_code);
                //$this->view->item_code=$item_code;
            }
        }
		die;
    }

}
