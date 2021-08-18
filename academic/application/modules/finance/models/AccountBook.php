<?php
class Finance_Model_AccountBook extends Zend_Db_Table_Abstract {

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	public function getAccountStatement($data)
	{		
			$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sales_invoice.grand_total AS `paid_amount`, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' and cash_status != 1 GROUP BY sales_items.sales_invoice_id		
			UNION 		
			SELECT invoice.purchase_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, invoice.grand_total_amount AS `paid_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' and cash_status != 1 GROUP BY invoice.purchase_invoice_id
			UNION
			SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, party_names as vendor_name, payment_status, (CASE WHEN payment_status = '4' THEN 'PV' END) as payment_type, remarks as invoice_increment_id FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'
			
			UNION 
			
			SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, party_names as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '5' THEN 'RV' END) as payment_type, remarks as invoice_increment_id FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."' 
			
			UNION

			SELECT `id`, DATE_FORMAT(date,'%Y-%m-%d') as date, `credit_amount` as paid_amount, `dealer_id` as vendor_name, (`payment_type`) as payment_status, (CASE WHEN payment_type = '5' THEN 'JV' END) as payment_type, remarks as invoice_increment_id FROM `journal_voucher` WHERE status !=2 and payment_type = '5' and date >='".$data['start_date']."' and date <='".$data['end_date']."'

			UNION

			SELECT `id`, DATE_FORMAT(date,'%Y-%m-%d') as date, `debit_amount` as paid_amount, `vendor_id` as vendor_name, (CASE WHEN `payment_type` = '5' THEN '4' END) as payment_status, (CASE WHEN payment_type = '5' THEN 'JV' END) as payment_type, remark1 as invoice_increment_id FROM `journal_voucher` WHERE status !=2 and payment_type = '5' and date >='".$data['start_date']."' and date <='".$data['end_date']."'
			
			UNION
		
			SELECT invoice.`commercial_invoice_id`, DATE_FORMAT(invoice.invoice_due_date,'%Y-%m-%d') as date, invoice.amount_inr as paid_amount, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '5' THEN 'PCI' END) as payment_type, invoice.commercial_invoice_id as invoice_increment_id FROM `erp_purchase_commercial_invoice` as invoice left join erp_vendor_master on erp_vendor_master.vendor_id=invoice.vendor_id WHERE `commercial_invoice_status` !='2' and invoice_due_date >='".$data['start_date']."' and invoice_due_date <='".$data['end_date']."'
			UNION		
			SELECT erp_sales_waybill.`waybill_id`, DATE_FORMAT(erp_sales_waybill.waybill_date,'%Y-%m-%d') as date, erp_sales_waybill.total_amount as paid_amount, erp_dealer_master.custom_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '4' THEN 'WB' END) as payment_type, erp_sales_waybill.`waybill_id` as invoice_increment_id FROM `erp_sales_waybill` left join erp_dealer_master on erp_dealer_master.dealer_id=erp_sales_waybill.sales_dealer_id WHERE `waybill_status` !='2' and waybill_date >='".$data['start_date']."' and waybill_date <='".$data['end_date']."'			
			ORDER BY vendor_name, date";				
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
			$data[$val['vendor_name']][] = $val;
        }		
        return $data;	
	}	
}