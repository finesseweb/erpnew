<?php


class Finance_AjaxController extends My_MasterController {

    public function init() {
        //$this->globalFunction();
    }

    public function indexAction() {  
        echo ''; die;
    }
    
	public function customerByAmountAction()
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
				$get_amount = $ErpSalesInvoice_model->getCustomerTotalPayment($dealer_id, $invoice_id);
				
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
	
	public function customerAction()
	{					
		$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
		$result = $ErpDealerMaster_model->getDropDownList();
		
		/* echo '<select name="dealer_id" id="dealer_id" class="form-control" required="required">';
		if( count($result) >0 ){					
			echo '<option value="" label="Select Invoice No">Select Invoice No</option>';
			foreach($result as $k=>$val){
			echo '<option value="'.$k.'" label="'.$val.'">'.$val.'</option>';
			}
		} else {			
			echo '<option value="" label="No Invoice No">No Invoice No</option>';
		}
		echo '</select>'; */ 
		echo json_encode($result);
		die;
	}
	
	//Get invoice ID's	
	public function invoiceAction()
	{				
		$dealer_id = $this->_getParam("dealer_id");	
		$ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
		$dealer_data = $ErpDealerMaster_model->getRecord($dealer_id);		
		if($dealer_data['dealer_type'] == 1){
			$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
			$result = $ErpFinanceReceiptVoucher_model->getWayBillIds($dealer_id);
			/* echo '<select name="invoice_id" id="invoice_id" class="form-control" required="required">';
			if( count($result) >0 ){					
				echo '<option value="" label="Select WayBill ID">Select WayBill ID</option>';
				foreach($result as $k=>$val){
				echo '<option value="'.$k.'" label="'.$val.'">'.$val.'</option>';
				}
			} else {
				echo '<option value="" label="No WayBill ID">No WayBill ID</option>';
			}
			echo '</select>'; */
			echo json_encode($result);
			die;
		}else{
			$ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
			$result = $ErpFinanceReceiptVoucher_model->getInvoiceIds($dealer_id);
			/* echo '<select name="invoice_id" id="invoice_id" class="form-control" required="required">';
			if( count($result) >0 ){
				echo '<option value="" label="Select Invoice ID">Select Invoice ID</option>';
				foreach($result as $k=>$val){
				echo '<option value="'.$k.'" label="'.$val.'">'.$val.'</option>';
				}
			} else {
				echo '<option value="" label="No Invoice ID">No Invoice ID</option>';
			}
			echo '</select>'; */ 
			echo json_encode($result);
			die;
		}
		die;		
	}
}
