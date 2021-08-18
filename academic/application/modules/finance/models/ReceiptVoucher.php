<?php

class Finance_Model_ReceiptVoucher extends Zend_Db_Table_Abstract {

    protected $_name = 'finance_receipt_voucher';
    protected $_id = 'receipt_voucher_id';
    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */
    public function getRecord($cp_id) {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('dealer_id', 'dealer_name', 'dealer_company_name', 'opening_balance', 'operator'))
				->joinLeft(array("invoice" => "erp_sales_invoice"), "invoice.sales_invoice_id=$this->_name.invoice_id", array('invoice_increment_id', 'invoice_increment_id as increment_text'))
                ->where("$this->_id=?", $cp_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    /**
     * Retrieve all Records
     *
     * @return Array
     */
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name, array("$this->_name.*", "(CASE WHEN payment_by = '1' THEN 'CASH' WHEN payment_by = '2' THEN 'CHEQUE' WHEN payment_by = '3' THEN 'NEFT/RTGS' END) as payment_by"))
				->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('dealer_id', 'dealer_name', 'dealer_address', 'dealer_type', 'dealer_tin_no', 'dealer_vat_no', 'dealer_cst_no', 'dealer_company_name', 'opening_balance', 'custom_name'))
				->joinLeft(array("invoice" => "erp_sales_invoice"), "invoice.sales_invoice_id=$this->_name.invoice_id", array('invoice_increment_id', 'invoice_increment_id as increment_text'))
                ->where("status!=2")
				->order("$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getInvoiceIds($dealer_id="" ) {
        $select = $this->_db->select()
                ->from(array("invoice" => "erp_sales_invoice"), array('sales_invoice_id', 'invoice_increment_id', 'increment_text', 'grand_total'))
				->joinLeft(array("receipt" => $this->_name), 'receipt.invoice_id=invoice.sales_invoice_id', array('sum(paid_amount) as total_paid'))
				->where("invoice.dealer_id =?", $dealer_id)
				->where("invoice.invoice_status !=?", 2)
				->where("invoice.cash_status !=?", 1)
                ->order('invoice.sales_invoice_id DESC')
				->group("invoice.sales_invoice_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {			
			if( round($val['grand_total']) > $val['total_paid']){
				$data[$val['sales_invoice_id']] = @(SALE_INVOICE) . " - " . $val['invoice_increment_id'];
			}
        }
        return $data;
    } 
	
	//Way Bill ID
	public function getWayBillIds($dealer_id="" ) {
        $select = $this->_db->select()
                ->from(array("waybill" => "erp_sales_waybill"), array('waybill_id', 'total_amount'))
				->joinLeft(array("receipt" => $this->_name), 'receipt.waybill_id=waybill.waybill_id', array('sum(paid_amount) as total_paid'))
				->where("waybill.sales_dealer_id =?", $dealer_id)
				->where("waybill.waybill_status !=?", 2)
                ->order('waybill.waybill_id DESC')
				->group("waybill.waybill_id");
        $result = $this->getAdapter()
                ->fetchAll($select);
		//print_r($result); die;
        $data = array();
        foreach ($result as $val) {			
			if( round($val['total_amount']) > $val['total_paid']){
				if(strlen($val['waybill_id'])==1){
					$data[$val['waybill_id']] = @(WB_PREFIX)."000".$val['waybill_id'];
				}else
				 if(strlen($val['waybill_id'])==2){
				   $data[$val['waybill_id']] = @(WB_PREFIX)."00".$val['waybill_id'];
				}else
				 if(strlen($val['waybill_id'])==3){
					$data[$val['waybill_id']] = @(WB_PREFIX)."0".$val['waybill_id'];
				}
				else{
					$data[$val['waybill_id']] = @(WB_PREFIX)."".$val['waybill_id'];
				}				
			}
        }
        return $data;
    }

    public function getInvoiceDetails() {

        $CompanyMaster_model = new Application_Model_ErpCompanyMaster();
        $company_details = $CompanyMaster_model->getRecord(2);
        $this->view->company_details = $company_details;
        $ErpSalesTyreDispatchFormTyres_model = new Application_Model_ErpSalesTyreDispatchFormTyres();
        $dispatchDetails = $ErpSalesTyreDispatchFormTyres_model->getDispatchtyres($tyre_dispatch_id);
        $this->view->dispatchDetails = $dispatchDetails;
    }

    public function getRecordsByDates($startdate, $enddate) {
        $select = $this->_db->select()
                ->from($this->_name, array("transaction_amount", "adjust_amount", "tyre_invoice_id", "date(added_date) as date", "added_date", "cp_id"))
                ->where("status!=2")
                ->where("added_date between '{$startdate}' and ADDDATE('{$enddate}',INTERVAL 1 DAY)")
                ->order("added_date ASC");

        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo '<pre>';print_r($result);exit;
        return $result;
    }
	
	public function getCustomerOpeningBalance($cp_id) {
        $select = $this->_db->select()
				->from('erp_dealer_master', array("dealer_id", "dealer_name", "dealer_address", 'dealer_company_name', 'opening_balance', 'operator', 'start_balance'))
                ->where("dealer_id=?", $cp_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getRecordByCustomer($cp_id="", $rid="") {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('dealer_id', 'dealer_name', 'dealer_company_name', 'opening_balance', 'operator'))
				//->where("$this->_id =?", $rid)
				->where("$this->_name.status !=?", 2)
                ->where("$this->_name.dealer_id=?", $cp_id)
				->where("$this->_id >=?", $rid)				
				->where("$this->_id !=?", $rid)
				->order("$this->_id ASC");
		//echo $select; die;
				//->limit(1);		
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	public function getFirstRecordByCustomer($cp_id="", $rid="") {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('dealer_id', 'dealer_name', 'dealer_company_name', 'opening_balance', 'operator'))
				//->where("$this->_id =?", $rid)
				->where("$this->_name.status !=?", 2)
                ->where("$this->_name.dealer_id=?", $cp_id)
				->where("$this->_id <=?", $rid)				
				->where("$this->_id !=?", $rid)
				->order("$this->_id DESC")
				->limit(1);		
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getAfterRecordByCustomer($cp_id="", $rid="") {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('dealer_id', 'dealer_name', 'dealer_company_name', 'opening_balance', 'operator'))
				//->where("$this->_id =?", $rid)
				->where("$this->_name.status !=?", 2)
                ->where("$this->_name.dealer_id=?", $cp_id)
				->where("$this->_id >=?", $rid)				
				->where("$this->_id !=?", $rid)
				->order("$this->_id ASC")
				->limit(1);		
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function editRecordByCustomer($cp_id="", $rid="") {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('dealer_id', 'dealer_name', 'dealer_company_name', 'opening_balance', 'operator'))
				//->where("$this->_id =?", $rid)
				->where("$this->_name.status !=?", 2)
                ->where("$this->_name.dealer_id=?", $cp_id)
				->where("$this->_id >=?", $rid)
				->where("$this->_id !=?", $rid)
				->order("$this->_id ASC");
		//echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	public function getFinanceRecords() {
       ///$select = "SELECT `finance_receipt_voucher`.receipt_voucher_id, `finance_receipt_voucher`.add_date, `ecm`.`dealer_name`, `ecm`.`dealer_id`, `ecm`.`dealer_company_name` FROM `finance_receipt_voucher` LEFT JOIN `erp_dealer_master` AS `ecm` ON finance_receipt_voucher.dealer_id = ecm.dealer_id INNER JOIN `statement_remarks` AS `statement` ON statement.party_name = ecm.dealer_company_name WHERE (status!=2) and statement.start_date >= statement.end_date ORDER BY `receipt_voucher_id` DESC"; 
		$select = "SELECT statement_remarks.*, ecm.dealer_company_name, receipt.receipt_voucher_id, receipt.add_date  FROM `statement_remarks` JOIN `erp_dealer_master` AS `ecm` ON ecm.dealer_company_name = statement_remarks.party_name
JOIN `finance_receipt_voucher` AS `receipt` ON receipt.dealer_id = ecm.dealer_id WHERE (receipt.status!=2) and receipt.add_date <= statement_remarks.end_date ORDER BY receipt.receipt_voucher_id DESC";
        $result = $this->getAdapter()
                ->fetchAll($select);
		$data = array();
        foreach ($result as $val) {
            $data[$val['receipt_voucher_id']] = $val['add_date'];            
        }		
        return $data;
    }

	// Get Way Bill amount 	
	public function getIntCustomerPayment($cus_id="", $invoice_id="") { 
        $select = $this->_db->select()
                ->from(array('sales_bill' => 'erp_sales_waybill'), array("sum(sales_bill.total_amount) as total_amount"))
				->joinLeft('erp_dealer_master as customer', 'customer.dealer_id=sales_bill.sales_dealer_id', array('dealer_id', 'dealer_name', 'dealer_address', 'dealer_type', 'dealer_tin_no', 'dealer_vat_no', 'dealer_cst_no', 'dealer_company_name', 'opening_balance', 'operator', 'start_balance'))									
				->where("sales_bill.sales_dealer_id=?", $cus_id)
				->where("sales_bill.waybill_id=?", $invoice_id)
				->where("sales_bill.waybill_status !=?", 2)
				->group("sales_bill.waybill_id");
				//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getReceiptPayment($cus_id="", $invoice_id="") { 
        $select = $this->_db->select()
                ->from(array('receipt' => 'finance_receipt_voucher'), array("sum(paid_amount) as total_paidamount", "sum( ABS(total_amount) ) as total_amount", "sum( ABS(balance) )as balance", "sum( ABS(transaction_amount)) as transaction_amount", "sum( ABS(opening_balance1)) as opening_balance1"))
				->joinLeft('erp_dealer_master as customer', 'customer.dealer_id=receipt.dealer_id', array('dealer_id', 'dealer_name', 'dealer_address', 'dealer_type', 'dealer_tin_no', 'dealer_vat_no', 'dealer_cst_no', 'dealer_company_name', 'opening_balance', 'operator'))
				->where("receipt.dealer_id=?", $cus_id)
				->where("receipt.waybill_id=?", $invoice_id)
				->where("receipt.status !=?", 2)
				->group("receipt.waybill_id");
				//echo  $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getIntCustPayment($cus_id="") { 
        $select = $this->_db->select()
                ->from(array('sales_bill' => 'erp_sales_waybill'), array("sum(sales_bill.total_amount) as total_amount"))
				->joinLeft('erp_dealer_master as customer', 'customer.dealer_id=sales_bill.sales_dealer_id', array('dealer_id', 'dealer_name', 'dealer_address', 'dealer_type', 'dealer_tin_no', 'dealer_vat_no', 'dealer_cst_no', 'dealer_company_name', 'opening_balance', 'operator', 'start_balance'))									
				->where("sales_bill.sales_dealer_id=?", $cus_id)
				//->where("sales_bill.waybill_id=?", $invoice_id)
				->where("sales_bill.waybill_status !=?", 2)
				->group("sales_bill.sales_dealer_id");
				//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getRecPayment($cus_id="") { 
        $select = $this->_db->select()
                ->from(array('receipt' => 'finance_receipt_voucher'), array("sum(paid_amount) as total_paidamount", "sum( ABS(total_amount) ) as total_amount", "sum( ABS(balance) )as balance", "sum( ABS(transaction_amount)) as transaction_amount", "sum( ABS(opening_balance1)) as opening_balance1"))
				->joinLeft('erp_dealer_master as customer', 'customer.dealer_id=receipt.dealer_id', array('dealer_id', 'dealer_name', 'dealer_address', 'dealer_type', 'dealer_tin_no', 'dealer_vat_no', 'dealer_cst_no', 'dealer_company_name', 'opening_balance', 'operator'))
				->where("receipt.dealer_id=?", $cus_id)
				//->where("receipt.waybill_id=?", $invoice_id)
				->where("receipt.status !=?", 2)
				->group("receipt.dealer_id");
				//echo  $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

}
