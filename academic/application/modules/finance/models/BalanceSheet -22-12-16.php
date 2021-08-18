<?php

class Finance_Model_BalanceSheet extends Zend_Db_Table_Abstract {

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	
	public function getWaybillStatement()
	{
		$select ="SELECT sum(waybill.total_amount) AS waybill_amount FROM erp_sales_waybill as waybill WHERE waybill.waybill_status !=2 ";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getSalesRetailStatement()
	{
		$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS paid_amount, ( IF(sales_invoice.operator2 = '1', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '1', sales_invoice.price3, 0) ) AS add_amount, ( IF(sales_invoice.operator2 = '2', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '2', sales_invoice.price3, 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !='2' and sales_invoice.invoice_type!='2'";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getSalesExportStatement()
	{
		$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS paid_amount, ( IF(sales_invoice.operator2 = '1', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '1', sales_invoice.price3, 0) ) AS add_amount, ( IF(sales_invoice.operator2 = '2', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '2', sales_invoice.price3, 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !='2' and sales_invoice.invoice_type='2'";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
		
	public function getAccountStatement()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getCurrentAsset()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.account_group='7' and account.vat_status !='1' order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getSecurities()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.account_group='6' order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getExpenses()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.account_group='8' order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getCapitalAccountStatement()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.account_group='1' order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getSecuredLoans()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.account_group='2' order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getUnsecuredLoans()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.account_group='3' order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getCurrentLiabilities()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.account_group='4' order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	
	public function getAccountStatementByDatesForPurchase($data="")
	{		
			$select ="		
			SELECT invoice.purchase_invoice_id as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (CASE WHEN invoice.operator1 = '1' THEN sum(invoice.price) END) AS `add_amount`, (CASE WHEN invoice.operator1 = '2' THEN sum(invoice.price) END) AS `sub_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and (purchase_items.account_id='' OR purchase_items.account_id='0') and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' 			
			UNION	
			SELECT invoice.`commercial_invoice_id`, DATE_FORMAT(invoice.invoice_due_date,'%Y-%m-%d') as date, sum(invoice.amount_inr) as paid_amount, null, null, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '5' THEN 'PCI' END) as payment_type, invoice.commercial_invoice_id as invoice_increment_id, null, null, null, null FROM `erp_purchase_commercial_invoice` as invoice left join erp_vendor_master on erp_vendor_master.vendor_id=invoice.vendor_id WHERE `commercial_invoice_status` !='2' and invoice_due_date >='".$data['start_date']."' and invoice_due_date <='".$data['end_date']."'";	
			//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;		
	}
	
	
	public function getProfitRightAccountStatement()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.profitandloss=1 order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getProfitLeftAccountStatement()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2  GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2  GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2  GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2  GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and account.profitandloss=2 order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	
	public function getCustomerStatementlist($data)
	{
		$select ="SELECT customer.dealer_id, (CASE WHEN dealer_type = '0' THEN customer.dealer_company_name WHEN dealer_type = '1' THEN  customer.custom_name END) AS name, customer.start_balance, customer.operator,(SELECT sum(waybill.total_amount) FROM erp_sales_waybill as waybill LEFT JOIN erp_dealer_master ON erp_dealer_master.dealer_id=waybill.sales_dealer_id WHERE waybill.sales_dealer_id = customer.dealer_id and waybill.waybill_status !=2 and waybill.waybill_date >='".$data['start_date']."' and waybill.waybill_date <='".$data['end_date']."' GROUP BY waybill.sales_dealer_id) AS waybill_amount, 
		(SELECT sum(invoice.grand_total) FROM erp_sales_invoice as invoice LEFT JOIN `erp_dealer_master` AS cus ON cus.dealer_id=invoice.dealer_id WHERE invoice.dealer_id = customer.dealer_id and invoice.invoice_status !=2 and invoice.cash_status !='1' and invoice.invoice_date >='".$data['start_date']."' and invoice.invoice_date <='".$data['end_date']."' GROUP BY invoice.dealer_id order by invoice.dealer_id) AS sales_amount,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=customer.dealer_id AND payment.party_type = 'C' AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=customer.dealer_id AND receipt.party_type = 'C' and receipt.status !=2 and receipt.add_date >='".$data['start_date']."' and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = customer.dealer_id and db_voucher.debit_party_type='C' and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."' and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = customer.dealer_id and cd_voucher.credit_party_type='C' and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."' and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM erp_dealer_master AS customer where customer.dealer_master_status !=2 order by dealer_company_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getVendorStatementlist($data)
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
	
		
	public function getBankStatementlist($data)
	{
		$select ="SELECT bank.bank_id, bank.opening_balance, bank.operator, bank.beneficiary_bank_name,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=bank.bank_id AND ( payment.party_type = 'B') AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=bank.bank_id AND ( receipt.party_type = 'B') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."' and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = bank.bank_id and db_voucher.debit_party_type='B' and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."' and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = bank.bank_id and cd_voucher.credit_party_type='B' and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."' and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM erp_bank_master AS bank where bank.status !=2 and bank.bank_group = '2' order by beneficiary_bank_name";
		/* "UNION
		SELECT account.account_id, account.opening_balance, account.operator, account.account_name AS beneficiary_bank_name, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."' and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = account.account_id and db_voucher.debit_party_type='A' and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."' and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = account.account_id and cd_voucher.credit_party_type='A' and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."' and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 and cash_account='1'
		order by beneficiary_bank_name"; */
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	//Liabilities
	public function getBankStatementLiabilities($data)
	{
		$select ="SELECT bank.bank_id, bank.opening_balance, bank.operator, bank.beneficiary_bank_name,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=bank.bank_id AND ( payment.party_type = 'B') AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=bank.bank_id AND ( receipt.party_type = 'B') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."' and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = bank.bank_id and db_voucher.debit_party_type='B' and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."' and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = bank.bank_id and cd_voucher.credit_party_type='B' and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."' and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM erp_bank_master AS bank where bank.status !=2 and bank.bank_group = '1' order by beneficiary_bank_name";
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	
	public function getAccountStatementByDatesForCash($data)
	{
				
			$select1 ="SELECT finance_payment_vouvher.`payment_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, sum(paid_amount) as paid_amount, (sub_amount) as add_amount, sub_amount, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '4' THEN 'PV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and payment_by='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";
			//echo $select1; die;
			$result1 = $this->getAdapter()
				->fetchRow($select1);
			//return $result1;
		
			$select2 ="SELECT finance_receipt_voucher.`receipt_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, sum(paid_amount) as paid_amount, (sub_amount) as add_amount, sub_amount, erp_dealer_master.dealer_company_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '5' THEN 'RV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and payment_by='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";			
			//echo $select2; die;
			$result2 = $this->getAdapter()
                ->fetchRow($select2);
			
			$select4 ="SELECT erp_sales_invoice.`sales_invoice_id` as sales_invoice_id, DATE_FORMAT(erp_sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(actual_grand_total) as paid_amount,dealer_company_name as vendor_name, (CASE WHEN `erp_sales_invoice`.`receipt_status` = '4' THEN '5' END) as payment_status, (CASE WHEN `erp_sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, null FROM `erp_sales_invoice` left join erp_dealer_master on erp_dealer_master.dealer_id=erp_sales_invoice.dealer_id WHERE `invoice_status` !='2' and cash_status= 1 and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";
			$result4 = $this->getAdapter()
                ->fetchRow($select4);
						
			$select5="SELECT erp_purchase_invoice.`purchase_invoice_id` as sales_invoice_id, DATE_FORMAT(erp_purchase_invoice.invoice_date,'%Y-%m-%d') as date,sum(grand_total_amount) as paid_amount,vendor_name as vendor_name, (CASE WHEN `erp_purchase_invoice`.`payment_status` = '5' THEN '4' END) as payment_status, (CASE WHEN `erp_purchase_invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, null FROM `erp_purchase_invoice` left join erp_vendor_master on erp_vendor_master.vendor_id=erp_purchase_invoice.vendor_id WHERE `purchase_invoice_status` !='2' and cash_status = 1 and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";
			$result5 = $this->getAdapter()
                ->fetchRow($select5);
			
			$select3 = $this->_db->select()
						->from('account_master', array("sum( ABS(opening_balance) ) as start_balance", "operator"))
						//->where("account_name=?", $party_id)
						->where("cash_account=?", 1)
						->where("status!=?", 2);
				$result3 = $this->getAdapter()
						->fetchRow($select3);
				
				if($result3['operator'] == 2){
					$debit_amount = $result2['paid_amount'] + $result4['paid_amount'];
					$credit_amount = $result1['paid_amount'] + $result3['start_balance'] + $result5['paid_amount'];
				}else{
					$credit_amount = $result1['paid_amount'] + $result5['paid_amount'];
					$debit_amount = $result2['paid_amount'] + $result3['start_balance'] + $result4['paid_amount'];
				}
				
				$balance = 0;
				if( $debit_amount > $credit_amount){								
					$balance = $debit_amount - $credit_amount;
				}
				
				if( $credit_amount > $debit_amount){
					$balance = $credit_amount - $debit_amount;
				}
				
			$data = array("balance" => $balance);
			
			return $data;
				
	}
	
	
	//Get Vat For Domestic Purchase
	public function getAccountStatementByDatesForPurchaseVat($data)
	{	
		
		$select ="		
			SELECT invoice.bill_no as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (IF(invoice.operator1 = '1', invoice.price, 0) + IF(invoice.operator2 = '1', invoice.price1, 0) + IF(invoice.operator3 = '1', invoice.price2, 0) ) AS add_amount, ( IF(invoice.operator1 = '2', invoice.price, 0) + IF(invoice.operator2 = '2', invoice.price1, 0) + IF(invoice.operator3 = '2', invoice.price2, 0) ) AS sub_amount, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY invoice.purchase_invoice_id 
			UNION
			SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS `paid_amount`, ( IF(sales_invoice.operator1 = '0' && sales_invoice.tax_type='2', sales_invoice.price1, 0) + IF(sales_invoice.operator2 = '1' && sales_invoice.tax_type1='2', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '1' && sales_invoice.tax_type2='2', sales_invoice.price3, 0) ) AS add_amount, ( IF(sales_invoice.operator2 = '2' && sales_invoice.tax_type1='2', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '2' && sales_invoice.tax_type2='2', sales_invoice.price3, 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !=2 and (sales_invoice.tax_type='2' OR sales_invoice.tax_type1='2' OR sales_invoice.tax_type2='2') and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY sales_items.sales_invoice_id 
			
			UNION
			
			SELECT finance_payment_vouvher.`payment_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, paid_amount as add_amount , null, party_names as vendor_name, (CASE WHEN `finance_payment_vouvher`.`payment_status` = '4' THEN '5' END) as payment_status, (CASE WHEN payment_status = '4' THEN 'PV' END) as payment_type, remarks as invoice_increment_id FROM `finance_payment_vouvher` left join account_master on account_master.account_id=finance_payment_vouvher.vendor_id and party_type='A' WHERE vat_status ='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'
			
			UNION 
			
			SELECT  finance_receipt_voucher.`receipt_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, paid_amount, paid_amount as add_amount, null, party_names as vendor_name,(CASE WHEN `finance_receipt_voucher`.`receipt_status` = '5' THEN '4' END) as payment_status , (CASE WHEN receipt_status = '5' THEN 'RV' END) as payment_type, remarks as invoice_increment_id FROM `finance_receipt_voucher` left join account_master on account_master.account_id=finance_receipt_voucher.dealer_id and party_type='A' WHERE vat_status ='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'
			
			UNION
			
			SELECT `id` as sales_invoice_id, DATE_FORMAT(date,'%Y-%m-%d') as date,credit_amount, `credit_amount` as add_amount, null, `dealer_id` as vendor_name, (CASE WHEN `journal_voucher`.`payment_type` = '5' THEN '4' END) as payment_status,(CASE WHEN payment_type = '5' THEN 'JV' END) payment_type, remarks as invoice_increment_id FROM `journal_voucher` left join account_master on account_master.account_id=journal_voucher.credit_party_id and credit_party_type='A' WHERE payment_type = '5' and account_master.vat_status='1' and date >='".$data['start_date']."' and date <='".$data['end_date']."'
			
			UNION

			SELECT `id` as sales_invoice_id, DATE_FORMAT(date,'%Y-%m-%d') as date,debit_amount, `debit_amount` as add_amount, null, `vendor_id` as vendor_name, (CASE WHEN `journal_voucher`.`payment_type` = '5' THEN '5' END) as payment_status,(CASE WHEN `payment_type` = '5' THEN 'JV' END) as payment_type , remark1 as invoice_increment_id FROM `journal_voucher` left join account_master on account_master.account_id=journal_voucher.debit_party_id and debit_party_type='A' WHERE  payment_type = '5' and account_master.vat_status='1' and date >='".$data['start_date']."' and date <='".$data['end_date']."'
			
			ORDER BY date";
		$result = $this->getAdapter()
                ->fetchAll($select);
		//echo '<pre>';
		//print_r($result);
		$credit_amount = 0;
		$debit_amount = 0;
		
		$select3 = $this->_db->select()
						->from('account_master', array("sum( ABS(opening_balance) ) as start_balance", "operator"))
						//->where("account_name=?", $party_id)
						->where("vat_status=?", 1)
						->where("status!=?", 2);
				$result3 = $this->getAdapter()
						->fetchRow($select3);	
		
		foreach ($result as $vat) {
			
			if( $vat['payment_status'] == 5){			
				$debit_amount += ( $vat['add_amount'] );
				$credit_amount += ( $vat['sub_amount'] );
			}
																	
			if( $vat['payment_status'] == 4){				
				$credit_amount += ( $vat['add_amount'] );
				$debit_amount += ( $vat['sub_amount'] );
			}
												
					
		}
		
		if($result3['operator'] == 2){
			$debit_amount = $debit_amount;
			$credit_amount = $result3['start_balance'] + $credit_amount;
		}else{
			$debit_amount = $result3['start_balance'] + $debit_amount;
			$credit_amount = $credit_amount;
		}
		
		$balance = 0;
		if( $debit_amount > $credit_amount){								
			$balance = $debit_amount - $credit_amount;
		}
		
		if( $credit_amount > $debit_amount){
			$balance = $credit_amount - $debit_amount;
		}
		
		//Data 
		$data = array("balance" => $balance);
		//print_r($data); die;
        return $data;	
	}
	
	public function getAccountStatementList($data)
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(invoice.grand_total_amount) FROM erp_purchase_invoice as invoice LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN erp_vendor_master ON erp_vendor_master.vendor_id=invoice.vendor_id WHERE invoice.vendor_id = vendor.vendor_id and invoice.purchase_invoice_status !=2 and invoice.cash_status !='1' and invoice.invoice_date >='".$data['start_date']."' and invoice.invoice_date <='".$data['end_date']."' GROUP BY invoice.vendor_id) AS purchase_amount,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."' and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = account.account_id and db_voucher.debit_party_type='A' and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."' and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = account.account_id and cd_voucher.credit_party_type='A' and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."' and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 order by account_name";
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getAccountAssestsStatement($data)
	{
	
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, account.percentage, 
		(SELECT sum(purchase_items.purchase_invoice_item_price * purchase_items.purchase_invoice_item_approved_quantity) FROM erp_purchse_invoice_items AS purchase_items LEFT JOIN erp_purchase_invoice AS invoice ON invoice.purchase_invoice_id = purchase_items.purchase_invoice_no LEFT JOIN account_master AS ac ON ac.account_id=purchase_items.account_id WHERE  purchase_items.account_id=account.account_id and invoice.purchase_invoice_status!=2 and invoice.cash_status !='1' and invoice_date >=".$data['start_date']." and invoice_date <='".$data['end_date']."' GROUP BY purchase_items.account_id) as purchase_amount,		
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['mid_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2 and payment.add_date >='".$data['mid_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount1,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."' and receipt.add_date <='".$data['mid_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2 and receipt.add_date >='".$data['mid_date']."' and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount1,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = account.account_id and db_voucher.debit_party_type='A' and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."' and db_voucher.date <='".$data['mid_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.debit_party_id = account.account_id and db_voucher.debit_party_type='A' and db_voucher.status !=2 and db_voucher.date >='".$data['mid_date']."' and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount1,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = account.account_id and cd_voucher.credit_party_type='A' and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."' and cd_voucher.date <='".$data['mid_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.credit_party_id = account.account_id and cd_voucher.credit_party_type='A' and cd_voucher.status !=2 and cd_voucher.date >='".$data['mid_date']."' and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount1
		FROM account_master AS account where account.status !='2' and account.account_group='5' order by account_name";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
				return $result;
		}	
	
}