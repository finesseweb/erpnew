<?php


class Invoice_Model_ErpPurchaseInvoice extends Zend_Db_Table_Abstract {

    protected $_name = 'erp_purchase_invoice';
    protected $_id = 'purchase_invoice_id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $purchase_invoice_id
     * @return single dimention array
     */
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_id=?", $id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	 public function getRecordbyId($grn_id) {

        $select = $this->_db->select()

                ->from($this->_name)
				 ->joinLeft(array("erp_vendor" => "erp_vendor_master"), "erp_vendor.vendor_id=erp_purchase_invoice.vendor_id", array("vendor_name", "vendor_vat", "vendor_vat_tin", "vendor_cst_no", "vendor_address1", "vendor_address2"))

                ->where("erp_purchase_invoice.purchase_invoice_id =?",$grn_id);       

        $result = $this->getAdapter()

                ->fetchRow($select);

        return $result;

    }
    /**
     * Retrieve all Records
     *
     * @return Array
     */
	 //(CASE WHEN erp_purchase_invoice.operator1 = '1' THEN sum(erp_purchase_invoice.price) END) AS `add_amount`, (CASE WHEN erp_purchase_invoice.operator1 = '2' THEN sum(erp_purchase_invoice.price) END) AS `sub_amount`,
    public function getRecords() {
        $select = $this->_db->select('purchase_invoice_id','invoice_date')
                ->from($this->_name,array('purchase_invoice_id','invoice_date','operator1','price','grand_total_amount'))
				->joinLeft(array("erp_vendor" => "erp_vendor_master"), "erp_vendor.vendor_id=erp_purchase_invoice.vendor_id", array("vendor_name"))
				->joinLeft( array('purchase_items' => 'erp_purchse_invoice_items'), 'purchase_items.purchase_invoice_no = erp_purchase_invoice.purchase_invoice_id',  array("sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) as total_amount") )
				->group('purchase_items.purchase_invoice_no')
			   ->order("erp_purchase_invoice.purchase_invoice_id ASC")
                ->where("erp_purchase_invoice.purchase_invoice_status!=?", 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	public function getVendorPayment($id) {
        $select = $this->_db->select()
                ->from(array('invoice' => 'erp_purchase_invoice'), array("grand_total_amount as total_amount", "(CASE WHEN invoice.operator1 = '1' THEN sum(invoice.price) END) AS add_amount", "(CASE WHEN invoice.operator1 = '2' THEN sum(invoice.price) END) AS sub_amount"))				
				//->joinLeft( array('purchase_items' => 'erp_purchse_invoice_items'), 'purchase_items.purchase_invoice_no = invoice.purchase_invoice_id',  array("sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) as total_amount") )
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=invoice.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator', 'start_balance'))	
				->where("invoice.vendor_id=?", $id)
				->where("invoice.purchase_invoice_status !=?", 2)				
				->group("invoice.vendor_id");
			//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getPayment($id) { 
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
    
    public function getVendorTotalPayment($id) { 
        $select = $this->_db->select()
                ->from(array('invoice' => 'erp_purchase_invoice'), array("invoice.*", "(CASE WHEN invoice.operator1 = '1' THEN invoice.price END) AS add_amount", "(CASE WHEN invoice.operator1 = '2' THEN invoice.price END) AS sub_amount"))				
				->joinLeft( array('purchase_items' => 'erp_purchse_invoice_items'), 'purchase_items.purchase_invoice_no = invoice.purchase_invoice_id',  array("sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) as total_amount") )
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=invoice.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator', 'start_balance'))	
				->where("invoice.vendor_id=?", $id)
				->where("invoice.purchase_invoice_status !=?", 2)				
				->group("invoice.purchase_invoice_id");
			//echo $select; die;
			$result = $this->getAdapter()
					->fetchAll($select);		
			$add_amount = 0;
			$sub_amount = 0;
			foreach ($result as $val) {           
				$add_amount += $val['add_amount'];
				$sub_amount += $val['sub_amount'];
			}
			$data = array('add_amount' => $add_amount, 'sub_amount' => $sub_amount);        
		return $data;
    }
	
	public function getFinanceRecords() {
       /* $select = "SELECT `erp_purchase_invoice`.purchase_invoice_id, `erp_purchase_invoice`.invoice_date, `ecm`.`vendor_name`, `ecm`.`vendor_id` FROM `erp_purchase_invoice` LEFT JOIN `erp_vendor_master` AS `ecm` ON ecm.vendor_id = erp_purchase_invoice.vendor_id INNER JOIN `statement_remarks` AS `statement` ON statement.party_name = ecm.vendor_name WHERE (purchase_invoice_status!=2) and statement.start_date >= statement.tallied_enddate ORDER BY `purchase_invoice_id` DESC"; */ 
		$select = "SELECT statement_remarks.*, ecm.vendor_name, invoice.purchase_invoice_id, invoice.invoice_date  FROM `statement_remarks` JOIN `erp_vendor_master` AS `ecm` ON ecm.vendor_id = statement_remarks.party_name
JOIN `erp_purchase_invoice` AS `invoice` ON invoice.vendor_id = ecm.vendor_id WHERE (invoice.purchase_invoice_status!=2) and invoice.invoice_date <= statement_remarks.tallied_enddate ORDER BY invoice.purchase_invoice_id DESC";
        $result = $this->getAdapter()
                ->fetchAll($select);
		$data = array();
        foreach ($result as $val) {
            $data[$val['purchase_invoice_id']] = $val['invoice_date'];            
        }		
        return $data;
    }
	
	public function getFinanceRecordID($id="") {
       $select = "SELECT statement_remarks.*, ecm.vendor_name, invoice.purchase_invoice_id, invoice.invoice_date  FROM `statement_remarks` JOIN `erp_vendor_master` AS `ecm` ON ecm.vendor_id = statement_remarks.party_name
JOIN `erp_purchase_invoice` AS `invoice` ON invoice.vendor_id = ecm.vendor_id WHERE (invoice.purchase_invoice_status!=2) and invoice.invoice_date <= statement_remarks.tallied_enddate and invoice.purchase_invoice_id = '".$id."' ORDER BY invoice.purchase_invoice_id DESC"; 	
        $result = $this->getAdapter()
                ->fetchAll($select);
		$data = array();
        foreach ($result as $val) {
            $data[$val['purchase_invoice_id']] = $val['invoice_date'];            
        }		
        return $data;
    }
	
	/* public function getVendorPayment($id) { 
        $select = $this->_db->select()
                ->from(array('invoice' => 'erp_purchase_invoice'))
				->joinLeft('erp_inventory_grn as grn', 'grn.inventory_grn_id=invoice.inventory_grn_id', array('inventory_grn_id'))	
				->joinLeft( array('purchase_items' => 'erp_inventory_grn_items'), 'purchase_items.inventory_grn_no = grn.inventory_grn_id',  array("sum(inventory_grn_item_price * inventory_grn_item_approved_quantity ) as total_amount") )
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=grn.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator', 'start_balance'))	
				->where("grn.vendor_id=?", $id)
				->group("grn.vendor_id");
				//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
	
	public function getPayment($id) { 
        $select = $this->_db->select()
                ->from(array('payment' => 'finance_payment_vouvher'), array("sum(paid_amount) as total_paidamount", "sum( ABS(total_amount) ) as total_amount", "sum( ABS(balance) ) as balance", "sum( ABS(total_amount_hidden)) as total_amount_hidden") )
				->joinLeft('erp_vendor_master as vendor', 'vendor.vendor_id=payment.vendor_id', array('vendor_id', 'vendor_name', 'vendor_address1', 'opening_balance', 'operator'))
				->where("payment.vendor_id=?", $id)
				->where("payment.status !=?", 2)
				->group("payment.vendor_id");
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    } */

  

}