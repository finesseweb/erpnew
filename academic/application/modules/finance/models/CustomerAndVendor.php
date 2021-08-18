<?php

class Finance_Model_CustomerAndVendor extends Zend_Db_Table_Abstract {

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	
	public function getCustomerStatement($data)
	{
		$select ="SELECT customer.dealer_id, (CASE WHEN dealer_type = '0' THEN customer.dealer_company_name WHEN dealer_type = '1' THEN  customer.custom_name END) AS name, customer.start_balance, customer.operator,(SELECT sum(waybill.total_amount) FROM erp_sales_waybill as waybill LEFT JOIN erp_dealer_master ON erp_dealer_master.dealer_id=waybill.sales_dealer_id WHERE waybill.sales_dealer_id = customer.dealer_id and waybill.waybill_status !=2 and waybill.waybill_date >='".$data['start_date']."' and waybill.waybill_date <='".$data['end_date']."' GROUP BY waybill.sales_dealer_id) AS waybill_amount, 
		(SELECT sum(invoice.grand_total) FROM erp_sales_invoice as invoice LEFT JOIN `erp_dealer_master` AS cus ON cus.dealer_id=invoice.dealer_id WHERE invoice.dealer_id = customer.dealer_id and invoice.invoice_status !=2 and invoice.cash_status !='1' and invoice.invoice_date >='".$data['start_date']."' and invoice.invoice_date <='".$data['end_date']."' GROUP BY invoice.dealer_id order by invoice.dealer_id) AS sales_amount,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=customer.dealer_id AND payment.party_type = 'C' AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=customer.dealer_id AND receipt.party_type = 'C' and receipt.status !=2 and receipt.add_date >='".$data['start_date']."' and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = customer.dealer_id and db_voucher.debit_party_type='C' and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."' and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = customer.dealer_id and cd_voucher.credit_party_type='C' and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."' and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM erp_dealer_master AS customer where customer.dealer_master_status !=2 order by dealer_company_name";
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getVendorStatement($data)
	{
		$select ="SELECT vendor.vendor_id, vendor.vendor_name AS name, vendor.start_balance, vendor.operator, (SELECT sum(cinvoice.amount_inr) FROM erp_purchase_commercial_invoice as cinvoice LEFT JOIN erp_vendor_master ON erp_vendor_master.vendor_id=cinvoice.vendor_id WHERE cinvoice.vendor_id = vendor.vendor_id and cinvoice.commercial_invoice_status !=2 and cinvoice.invoice_due_date >='".$data['start_date']."' and cinvoice.invoice_due_date <='".$data['end_date']."' GROUP BY cinvoice.vendor_id) AS pcm_amount, 
		(SELECT sum(invoice.grand_total_amount) FROM erp_purchase_invoice as invoice LEFT JOIN erp_vendor_master ON erp_vendor_master.vendor_id=invoice.vendor_id WHERE invoice.vendor_id = vendor.vendor_id and invoice.purchase_invoice_status !=2 and invoice.cash_status !='1' and invoice.invoice_date >='".$data['start_date']."' and invoice.invoice_date <='".$data['end_date']."' GROUP BY invoice.vendor_id) AS purchase_amount,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=vendor.vendor_id AND payment.party_type = 'V' AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=vendor.vendor_id AND receipt.party_type = 'V' and receipt.status !=2 and receipt.add_date >='".$data['start_date']."' and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = vendor.vendor_id and db_voucher.debit_party_type='V' and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."' and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = vendor.vendor_id and cd_voucher.credit_party_type='V' and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."' and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM erp_vendor_master AS vendor where vendor.vendor_status !=2 order by vendor_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
}
?>