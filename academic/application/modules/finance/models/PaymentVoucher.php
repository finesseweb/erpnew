<?php


class Finance_Model_PaymentVoucher extends Zend_Db_Table_Abstract {

    protected $_name = 'finance_payment_vouvher';
    protected $_id = 'payment_voucher_id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $vendor_payment_id
     * @return single dimention array
     */
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_id', 'vendor_name', 'operator'))			
                ->where("$this->_id=?", $id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getInvoiceRecords($id) {
        $select = $this->_db->select()
                ->from($this->_name)				
				 ->joinleft(array("invoice" => "erp_purchase_invoice"), "invoice.purchase_invoice_id=$this->_name.purchase_invoice_id",array('grand_total','vat_total', 'grand_discount'))	
				->joinleft(array("bank" => "finance_bank_details"), "bank.fb_id=$this->_name.bank_id",array('bank_name'))
                ->where("$this->_name.purchase_invoice_id=?", $id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRecordsByDates($startdate, $enddate) {
        $select = $this->_db->select()
                ->from($this->_name, array("*", "date(added_date) as date"))
                ->where("status!=2")
                ->where("added_date between '{$startdate}' and ADDDATE('{$enddate}',INTERVAL 1 DAY)")
                ->order("added_date ASC");

        $result = $this->getAdapter()
                ->fetchAll($select);
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
				->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_id', 'vendor_name'))	
                ->joinLeft('erp_bank_master as bank', "bank.bank_id=$this->_name.bank_id", array('beneficiary_bank_name as bank_name'))					
                ->where("$this->_name.status!=2")
				->order("$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	public function getChequeNumber($bankid)
	{
		
	   $select = "SELECT payment.cheque_no, bank.starting_cheque_no, bank.ending_cheque_no FROM erp_bank_master as bank left join finance_payment_vouvher as payment on payment.bank_id=bank.bank_id WHERE (bank.status !=2) and bank.bank_id='".$bankid."' ORDER BY payment_voucher_id DESC";
		$result = $this->getAdapter()->fetchRow($select);
		if($result['cheque_no'] < $result['ending_cheque_no'] ){
			if( !empty($result['cheque_no']) )
			{
				$data = $result['cheque_no']+1;
			}else{
				$data = $result['starting_cheque_no'];
			}
			return $data;
		}else{
			return 0;
		}
		
	
		/* $select = "SELECT * FROM erp_bank_master as fbd 
		left join `finance_payment_vouvher` as fpv on fpv.bank_id=fbd.bank_id where fbd.bank_id=".$bankid;
		$result = $this->getAdapter()
                ->fetchAll($select);		
		if( count( $result ) > 0 ){		
			foreach($result as $val){
				$cheque_no = $val['cheque_str_num'];
				$pcheque_number = $val['cheque_no'];
			}
			
			if( !empty( $pcheque_number ) ){
				if( $pcheque_number <= $result[0]['cheque_end_num'] ) {	
					$pcheque_number_length = strlen($pcheque_number);
					$cheque_no1 = $pcheque_number + 1;	
					$cheque_no = str_pad($cheque_no1, $pcheque_number_length, '0', STR_PAD_LEFT);
				} else {
					$cheque_no = '0';
				}
			}		
			return $cheque_no;
		} else {
			return 0;
		} */
	}
	
    public function getInvoiceIds($vendor_id="") {
        $select = $this->_db->select()
                ->from(array("invoice" => "erp_purchase_invoice"), array('purchase_invoice_id', "(CASE WHEN invoice.operator1 = '1' THEN invoice.price END) AS add_amount", "(CASE WHEN invoice.operator1 = '2' THEN invoice.price END) AS sub_amount"))
				->joinLeft( array('purchase_items' => 'erp_purchse_invoice_items'), 'purchase_items.purchase_invoice_no = invoice.purchase_invoice_id',  array("(purchase_invoice_item_price * purchase_invoice_item_approved_quantity) as total_amount") )
				->joinLeft(array("receipt" => $this->_name), 'receipt.invoice_id=invoice.purchase_invoice_id', array('sum(paid_amount) as total_paid'))
				->where("invoice.vendor_id =?", $vendor_id)
				->where("invoice.purchase_invoice_status !=?", 2)
				->where("invoice.cash_status !=?", 1)
				->group("invoice.purchase_invoice_id")
                ->order('invoice.purchase_invoice_id DESC');
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
			$total_amount = round( ( $val['total_amount'] + $val['add_amount'] ) - $val['sub_amount'] );
			//echo $total_amount.'<br />'.$val['total_paid']; die;
			if( $total_amount > $val['total_paid']){
				if (strlen($val['purchase_invoice_id']) == 1) {
					$data[$val['purchase_invoice_id']] = @(PI_PREFIX) . "000" . $val['purchase_invoice_id'];
				} else
				if (strlen($val['purchase_invoice_id']) == 2) {
					$data[$val['purchase_invoice_id']] = @(PI_PREFIX) . "00" . $val['purchase_invoice_id'];
				} else
				if (strlen($val['purchase_invoice_id']) == 3) {
					$data[$val['purchase_invoice_id']] = @(PI_PREFIX) . "0" . $val['purchase_invoice_id'];
				} else {
					$data[$val['purchase_invoice_id']] = @(PI_PREFIX) . "" . $val['purchase_invoice_id'];
				}
			}
        }
        return $data;
    }
	
	public function getCommercialInvoiceIds($vendor_id="") {
        $select = $this->_db->select()
                ->from(array("invoice" => "erp_purchase_commercial_invoice"), array('commercial_invoice_id', 'total_amount'))
				->joinLeft(array("receipt" => $this->_name), 'receipt.commercial_invoice_id=invoice.commercial_invoice_id', array('sum(paid_amount) as total_paid'))
				->where("invoice.vendor_id =?", $vendor_id)
				->where("invoice.commercial_invoice_status !=?", 2)
				->group("invoice.commercial_invoice_id")
                ->order('invoice.commercial_invoice_id DESC');
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
			if( round($val['total_amount']) > $val['total_paid']){
				if (strlen($val['commercial_invoice_id']) == 1) {
					$data[$val['commercial_invoice_id']] = @(PCI_PREFIX) . "000" . $val['commercial_invoice_id'];
				} else
				if (strlen($val['commercial_invoice_id']) == 2) {
					$data[$val['commercial_invoice_id']] = @(PCI_PREFIX) . "00" . $val['commercial_invoice_id'];
				} else
				if (strlen($val['commercial_invoice_id']) == 3) {
					$data[$val['commercial_invoice_id']] = @(PCI_PREFIX) . "0" . $val['commercial_invoice_id'];
				} else {
					$data[$val['commercial_invoice_id']] = @(PCI_PREFIX) . "" . $val['commercial_invoice_id'];
				}
			}
        }
        return $data;
    }
	
	public function getVendorOpeningBalance($id) {
        $select = $this->_db->select()
				->from('erp_vendor_master', array('vendor_id', 'vendor_name', 'opening_balance', 'operator', 'start_balance'))
                ->where("vendor_id=?", $id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getRecordByVendor($cp_id="", $rid="") {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_id', 'vendor_name', 'opening_balance', 'operator', 'start_balance'))
				//->where("$this->_id =?", $rid)
				->where("$this->_name.status!=2")
                ->where("$this->_name.vendor_id=?", $cp_id)
				->where("$this->_id >=?", $rid)
				->where("$this->_id !=?", $rid)
				->order("$this->_id ASC");				
		//echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	public function getFirstRecordByVendor($cp_id="", $rid="") {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_id', 'vendor_name', 'opening_balance', 'operator', 'start_balance'))
				//->where("$this->_id =?", $rid)
				->where("$this->_name.status!=2")
                ->where("$this->_name.vendor_id=?", $cp_id)
				->where("$this->_id <=?", $rid)
				->where("$this->_id !=?", $rid)
				->order("$this->_id DESC")				
				->limit(1);		
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getAfterRecordByVendor($cp_id="", $rid="") {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_id', 'vendor_name', 'opening_balance', 'operator', 'start_balance'))
				//->where("$this->_id =?", $rid)
				->where("$this->_name.status!=2")
                ->where("$this->_name.vendor_id=?", $cp_id)
				->where("$this->_id >=?", $rid)
				->where("$this->_id !=?", $rid)
				->order("$this->_id ASC")
				->limit(1);		
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function editRecordByVendor($cp_id="", $rid="") {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_id', 'vendor_name', 'opening_balance', 'operator', 'start_balance'))
				//->where("$this->_id =?", $rid)
				->where("$this->_name.status!=2")
                ->where("$this->_name.vendor_id=?", $cp_id)
				->where("$this->_id >=?", $rid)
				->where("$this->_id !=?", $rid)
				->order("$this->_id ASC");
		//echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	public function getFinanceRecords() {
       /* $select = "SELECT `finance_payment_vouvher`.payment_voucher_id, `finance_payment_vouvher`.add_date, `ecm`.`vendor_name`, `ecm`.`vendor_id` FROM `finance_payment_vouvher` LEFT JOIN `erp_vendor_master` AS `ecm` ON ecm.vendor_id = finance_payment_vouvher.vendor_id INNER JOIN `statement_remarks` AS `statement` ON statement.party_name = ecm.vendor_name WHERE (status!=2) and statement.start_date >= statement.end_date ORDER BY `payment_voucher_id` DESC"; */
		$select = "SELECT statement_remarks.*, ecm.vendor_name, payment.payment_voucher_id, payment.add_date  FROM `statement_remarks` JOIN `erp_vendor_master` AS `ecm` ON ecm.vendor_id = statement_remarks.party_name
JOIN `finance_payment_vouvher` AS `payment` ON payment.vendor_id = ecm.vendor_id WHERE (payment.status!=2) and payment.add_date <= statement_remarks.end_date ORDER BY payment.payment_voucher_id DESC";
        $result = $this->getAdapter()
                ->fetchAll($select);
		$data = array();
        foreach ($result as $val) {
            $data[$val['payment_voucher_id']] = $val['add_date'];            
        }		
        return $data;
    }	
	
	public function getIntVendorPayment($id="", $invoice_id="") {
        $select = $this->_db->select()
                ->from(array('invoice' => 'erp_purchase_commercial_invoice'), array(" sum(total_amount) as total_amount"))
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=invoice.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator', 'start_balance'))	
				->where("invoice.vendor_id=?", $id)
				->where("invoice.commercial_invoice_id=?", $invoice_id)
				->where("invoice.commercial_invoice_status !=?", 2)				
				->group("invoice.commercial_invoice_id");
			//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	//Payment voucher
	public function getPayment($id="", $invoice_id="") { 
        $select = $this->_db->select()
                ->from(array('payment' => 'finance_payment_vouvher'), array("sum(paid_amount) as total_paidamount", "sum( ABS(total_amount) ) as total_amount", "sum( ABS(balance) ) as balance", "sum( ABS(transaction_amount)) as transaction_amount") )
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=payment.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator'))
				->where("payment.vendor_id=?", $id)
				->where("payment.commercial_invoice_id=?", $invoice_id)
				->where("payment.status !=?", 2)
				->group("payment.commercial_invoice_id");
		echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	
	//Get Vendor total balance amount 
	public function getVendorDocTotalBalance($id="", $invoice_id="") {
        $select = $this->_db->select()
                ->from(array('invoice' => 'erp_purchase_invoice'), array("invoice.*", "(invoice.grand_total_amount) AS total_amount"))
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=invoice.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator', 'start_balance'))	
				->where("invoice.vendor_id=?", $id)
				//->where("invoice.purchase_invoice_id=?", $invoice_id)
				->where("invoice.purchase_invoice_status !=?", 2)				
				->group("invoice.vendor_id");			
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getTotalDocPayment($id="", $invoice_id="") { 
        $select = $this->_db->select()
                ->from(array('payment' => 'finance_payment_vouvher'), array("sum(paid_amount) as total_paidamount", "sum( ABS(total_amount) ) as total_amount", "sum( ABS(balance) ) as balance", "sum( ABS(transaction_amount)) as transaction_amount") )
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=payment.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator'))
				->where("payment.vendor_id=?", $id)				
				->where("payment.status !=?", 2)
				->group("payment.vendor_id");
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    } 
	
	public function getTotalIntVendorPayment($id="") {
        $select = $this->_db->select()
                ->from(array('invoice' => 'erp_purchase_commercial_invoice'), array(" sum(total_amount) as total_amount"))
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=invoice.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator', 'start_balance'))	
				->where("invoice.vendor_id=?", $id)
				//->where("invoice.commercial_invoice_id=?", $invoice_id)
				->where("invoice.commercial_invoice_status !=?", 2)				
				->group("invoice.vendor_id");
			//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	// End 
	

}
