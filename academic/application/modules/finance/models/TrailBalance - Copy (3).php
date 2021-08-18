<?php

class Finance_Model_TrailBalance extends Zend_Db_Table_Abstract {

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	
	public function getCustomerStatement($data)
	{
		$select ="SELECT customer.dealer_id, customer.dealer_company_name AS name, customer.start_balance, customer.operator,(SELECT sum(waybill.total_amount) FROM erp_sales_waybill as waybill WHERE waybill.sales_dealer_id = customer.dealer_id and waybill.waybill_status !=2 and waybill.waybill_date >='".$data['start_date']."' and waybill.waybill_date <='".$data['end_date']."' GROUP BY waybill.sales_dealer_id) AS waybill_amount, 
		(SELECT sum(invoice.grand_total) FROM erp_sales_invoice as invoice WHERE invoice.dealer_id = customer.dealer_id and invoice.invoice_status !=2 and invoice.invoice_date >='".$data['start_date']."' and invoice.invoice_date <='".$data['end_date']."' GROUP BY invoice.dealer_id) AS sales_amount,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=customer.dealer_id AND ( payment.party_type = 'D' OR payment.party_type = 'I') AND payment.status !=2 and payment.add_date >='".$data['start_date']."' and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=customer.dealer_id AND ( receipt.party_type = 'D' OR receipt.party_type = 'I') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."'and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = customer.dealer_company_name and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."'and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = customer.dealer_company_name and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."'and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM erp_dealer_master AS customer where customer.dealer_master_status !=2 order by dealer_company_name";
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getVendorStatement($data)
	{
		$select ="SELECT vendor.vendor_id, vendor.vendor_name AS name, vendor.start_balance, vendor.operator, (SELECT sum(cinvoice.amount_inr) FROM erp_purchase_commercial_invoice as cinvoice WHERE cinvoice.vendor_id = vendor.vendor_id and cinvoice.commercial_invoice_status !=2 and cinvoice.invoice_due_date >='".$data['start_date']."'and cinvoice.invoice_due_date <='".$data['end_date']."' GROUP BY cinvoice.vendor_id) AS pcm_amount, 
		(SELECT sum(invoice.grand_total_amount) FROM erp_purchase_invoice as invoice WHERE invoice.vendor_id = vendor.vendor_id and invoice.purchase_invoice_status !=2 and invoice.invoice_date >='".$data['start_date']."'and invoice.invoice_date <='".$data['end_date']."' GROUP BY invoice.vendor_id) AS purchase_amount,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=vendor.vendor_id AND payment.party_type = 'V' AND payment.status !=2 and payment.add_date >='".$data['start_date']."'and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=vendor.vendor_id AND ( receipt.party_type = 'D' OR receipt.party_type = 'I') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."'and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = vendor.vendor_name and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."'and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = vendor.vendor_name and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."'and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM erp_vendor_master AS vendor where vendor.vendor_status !=2 order by vendor_name";
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getBankStatement($data)
	{
		$select ="SELECT bank.bank_id, bank.opening_balance, bank.operator, bank.beneficiary_bank_name,
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=bank.bank_id AND ( payment.party_type = 'B') AND payment.status !=2 and payment.add_date >='".$data['start_date']."'and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=bank.bank_id AND ( receipt.party_type = 'B') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."'and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = bank.beneficiary_bank_name  and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."'and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = bank.beneficiary_bank_name  and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."'and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM erp_bank_master AS bank where bank.status !=2 order by beneficiary_bank_name";
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	
	public function getAccountStatement($data)
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator, 
		(SELECT sum(payment.paid_amount) FROM finance_payment_vouvher as payment WHERE payment.vendor_id=account.account_id AND ( payment.party_type = 'A') AND payment.status !=2 and payment.add_date >='".$data['start_date']."'and payment.add_date <='".$data['end_date']."' GROUP BY payment.vendor_id) AS payment_amount,
		(SELECT sum(receipt.paid_amount) FROM finance_receipt_voucher as receipt WHERE receipt.dealer_id=account.account_id AND ( receipt.party_type = 'A') and receipt.status !=2 and receipt.add_date >='".$data['start_date']."'and receipt.add_date <='".$data['end_date']."' GROUP BY receipt.dealer_id) AS receipt_amount,
		(SELECT sum(db_voucher.debit_amount) FROM journal_voucher as db_voucher WHERE db_voucher.vendor_id = account.account_name and db_voucher.status !=2 and db_voucher.date >='".$data['start_date']."'and db_voucher.date <='".$data['end_date']."' GROUP BY db_voucher.vendor_id) AS debit_amount,
		(SELECT sum(cd_voucher.credit_amount) FROM journal_voucher as cd_voucher WHERE cd_voucher.dealer_id = account.account_name and cd_voucher.status !=2 and cd_voucher.date >='".$data['start_date']."'and cd_voucher.date <='".$data['end_date']."' GROUP BY cd_voucher.dealer_id) AS credit_amount
		FROM account_master AS account where account.status !=2 order by account_name";
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
	}
	   
	public function getAccountStatementByDatesForPurchase($data)
	{
				
		if( !empty($data['party_id']) ){		
			
			$where1 = '';			
			$where6 = '';
			$where7 = '';
			/* if(!empty( $data['type'] )){
				$where .= " and receipt_status ='".$data['type']."'";
				$where1 .= " and payment_status ='".$data['type']."'";
			} */
			if(!empty( $data['party_id'] )){				
				$where1 .= " and vendor_name ='".$data['party_id']."'";				
				$where6 .= " and vendor_name ='".$data['party_id']."'";				
				$where7 .= " OR account_name ='".$data['party_id']."'";				
			}
			if(!empty( $data['start_date'] )){				
				$where1 .= " and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";				
				$where6 .= " and invoice_due_date >='".$data['start_date']."' and invoice_due_date <='".$data['end_date']."'";
			}
			
			$select ="SELECT invoice.purchase_invoice_id as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (IF(invoice.operator1 = '1', invoice.price, 0) ) AS add_amount, ( IF(invoice.operator1 = '2', invoice.price, 0) ) AS sub_amount, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, account.account_id,account.account_name, null, null FROM `erp_purchase_invoice` AS `invoice` 
			LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id 
			LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id 
			LEFT JOIN `account_master` AS `account` ON account.account_id=purchase_items.account_id 
			WHERE purchase_invoice_status !=2 ".$where1.$where7." GROUP BY purchase_items.purchase_invoice_no
			
			UNION
		
			SELECT invoice.`commercial_invoice_id`, DATE_FORMAT(invoice.invoice_due_date,'%Y-%m-%d') as date, invoice.amount_inr as paid_amount, null, null, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '5' THEN 'PCI' END) as payment_type, invoice.commercial_invoice_id as invoice_increment_id, null, null, null, null FROM `erp_purchase_commercial_invoice` as invoice left join erp_vendor_master on erp_vendor_master.vendor_id=invoice.vendor_id WHERE `commercial_invoice_status` !='2' ".$where6."
												
			ORDER BY date";
			
									
		}else{
			
			//$select ="		
			//SELECT invoice.purchase_invoice_id as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (CASE WHEN invoice.operator1 = '1' THEN sum(invoice.price) END) AS `add_amount`, (CASE WHEN invoice.operator1 = '2' THEN sum(invoice.price) END) AS `sub_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY purchase_items.purchase_invoice_no
			
			//UNION
		
			//SELECT invoice.`commercial_invoice_id`, DATE_FORMAT(invoice.invoice_due_date,'%Y-%m-%d') as date, invoice.amount_inr as paid_amount, null, null, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '5' THEN 'PCI' END) as payment_type, invoice.commercial_invoice_id as invoice_increment_id, null, null, null, null FROM `erp_purchase_commercial_invoice` as invoice left join erp_vendor_master on erp_vendor_master.vendor_id=invoice.vendor_id WHERE `commercial_invoice_status` !='2' and invoice_due_date >='".$data['start_date']."' and invoice_due_date <='".$data['end_date']."'
			
			//ORDER BY date";
			$select ="		
			SELECT invoice.purchase_invoice_id as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (CASE WHEN invoice.operator1 = '1' THEN sum(invoice.price) END) AS `add_amount`, (CASE WHEN invoice.operator1 = '2' THEN sum(invoice.price) END) AS `sub_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' 
			
			UNION	
			SELECT invoice.`commercial_invoice_id`, DATE_FORMAT(invoice.invoice_due_date,'%Y-%m-%d') as date, sum(invoice.amount_inr) as paid_amount, null, null, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '5' THEN 'PCI' END) as payment_type, invoice.commercial_invoice_id as invoice_increment_id, null, null, null, null FROM `erp_purchase_commercial_invoice` as invoice left join erp_vendor_master on erp_vendor_master.vendor_id=invoice.vendor_id WHERE `commercial_invoice_status` !='2' and invoice_due_date >='".$data['start_date']."' and invoice_due_date <='".$data['end_date']."'";	
			
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	
	//Get Vat For Domestic Purchase
	public function getAccountStatementByDatesForPurchaseVat($data)
	{
				
		if( !empty($data['party_id']) ){		
			
			$where1 = '';
			$where2 = '';
			if(!empty( $data['party_id'] )){				
				$where1 .= " and vendor_name ='".$data['party_id']."'";	
				$where2 .= " OR account_name ='".$data['party_id']."'";	
			}
			if(!empty( $data['start_date'] )){				
				$where1 .= " and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";	
			}
			
			$select ="SELECT invoice.bill_no as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, ( IF(invoice.operator1 = '1', invoice.price, 0) ) AS add_amount, ( IF(invoice.operator1 = '2', invoice.price, 0) ) AS sub_amount, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, account.account_id,account.account_name, null, null FROM `erp_purchase_invoice` AS `invoice` 
			LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id 
			LEFT JOIN `account_master` AS `account` ON account.account_id=purchase_items.account_id 
			LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id 
			WHERE purchase_invoice_status !=2 ".$where1.$where2." GROUP BY purchase_items.purchase_invoice_no ORDER BY date";
			
		/*	$select ="SELECT invoice.purchase_invoice_id as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, ( IF(invoice.operator1 = '1', invoice.price, 0) + IF(invoice.operator2 = '1', invoice.price1, 0) + IF(invoice.operator3 = '1', invoice.price2, 0) ) AS add_amount, ( IF(invoice.operator1 = '2', invoice.price, 0) + IF(invoice.operator2 = '2', invoice.price1, 0) + IF(invoice.operator3 = '2', invoice.price2, 0) ) AS sub_amount, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !=2 ".$where1." GROUP BY purchase_items.purchase_invoice_no ORDER BY date"; */
			
		}else{
			$select ="SELECT (IF(invoice.operator1 = '1', sum(invoice.price), 0) ) AS add_amount, ( IF(invoice.operator1 = '2', sum(invoice.price), 0) ) AS sub_amount FROM `erp_purchase_invoice` AS `invoice` WHERE purchase_invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";	
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	
	public function getAccountStatementByDatesBeforePurchase($data)
	{					
			$select ="		
			SELECT invoice.purchase_invoice_id as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (CASE WHEN invoice.operator1 = '1' THEN sum(invoice.price) END) AS `add_amount`, (CASE WHEN invoice.operator1 = '2' THEN sum(invoice.price) END) AS `sub_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date <='".$data['start_date']."'			
			ORDER BY date";	
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchRow($select);
        return $result;	
	}
	
	public function getAccountStatementByDatesForCash($data)
	{
				
		if( !empty($data['party_id']) ){		
			
			$where2 = '';			
			$where3 = '';
			/* if(!empty( $data['type'] )){
				$where .= " and receipt_status ='".$data['type']."'";
				$where1 .= " and payment_status ='".$data['type']."'";
			} */
			if(!empty( $data['party_id'] )){				
				$where2 .= " and dealer_company_name ='".$data['party_id']."' OR party_names ='".$data['party_id']."'"; 
				$where3 .= " and vendor_name ='".$data['party_id']."' OR party_names ='".$data['party_id']."'";			
			}
			if(!empty( $data['start_date'] )){				
				$where2 .= " and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";
				$where3 .= " and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";
			}
			
			$select ="SELECT finance_payment_vouvher.`payment_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, (sub_amount) as add_amount, sub_amount, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '4' THEN 'PV' END) as payment_type,  remarks as invoice_increment_id, null, null, null, null FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and payment_by='1' ".$where3."
			
			UNION 
			
			SELECT finance_receipt_voucher.`receipt_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, (sub_amount) as add_amount, sub_amount, erp_dealer_master.dealer_company_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '5' THEN 'RV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and payment_by='1' ".$where2."
												
			ORDER BY date";
			
									
		}
		 else{
			
			$select1 ="SELECT finance_payment_vouvher.`payment_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, sum(paid_amount), (sub_amount) as add_amount, sub_amount, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '4' THEN 'PV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and payment_by='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";
			//echo $select1; die;
			$result1 = $this->getAdapter()
                ->fetchRow($select1);
			//return $result1;
		
		
			
		
			$select2 ="SELECT finance_receipt_voucher.`receipt_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, sum(paid_amount), (sub_amount) as add_amount, sub_amount, erp_dealer_master.dealer_company_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '5' THEN 'RV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and payment_by='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";			
			//echo $select2; die;
			
		}
			$result2 = $this->getAdapter()
                ->fetchRow($select2);
				
			$data = array("debit_amount" => $result1['sum(paid_amount)'], "credit_amount" => $result2['sum(paid_amount)']);
			
			return $data;
				
	}
	
	//CST Account
	public function getAccountStatementByDatesForSalesCST($data)
	{
				
		if( !empty($data['party_id']) ){		
			
			$where = '';
			if(!empty( $data['party_id'] )){				
				$where .= " and dealer_company_name ='".$data['party_id']."'";		
			}
			if(!empty( $data['start_date'] )){				
				$where .= " and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";
			}
			
			$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS `paid_amount`, ( IF(sales_invoice.operator1 = '0', sales_invoice.price1, 0) ) AS add_amount, ( IF(sales_invoice.operator1 = '1', sales_invoice.price1, 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !=2 ".$where." GROUP BY sales_items.sales_invoice_id	
			
			ORDER BY date";
			
									
		}else{
			
			//$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS `paid_amount`, ( IF(sales_invoice.operator1 = '0',sum(sales_invoice.price1), 0) ) AS add_amount, ( IF(sales_invoice.operator1 = '1', sum(sales_invoice.price1), 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";			
			$select ="SELECT ( IF(sales_invoice.operator1 = '0',sum(sales_invoice.price1), 0) ) AS add_amount, ( IF(sales_invoice.operator1 = '1', sum(sales_invoice.price1), 0) ) AS sub_amount FROM `erp_sales_invoice` AS `sales_invoice` WHERE sales_invoice.invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	
	//Sales Account
	public function getAccountStatementByDatesForSalesAccount($data)
	{
				
		if( !empty($data['party_id']) ){		
			
			$where = '';
			$where1 = '';
			if(!empty( $data['party_id'] )){				
				$where .= " and dealer_company_name ='".$data['party_id']."'";	
				$where1 .= " and dealer_company_name ='".$data['party_id']."'";
			}
			if(!empty( $data['start_date'] )){				
				$where .= " and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";
				$where1 .= " and waybill_date >='".$data['start_date']."' and waybill_date <='".$data['end_date']."'";	
			}
			
			$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS `paid_amount`, ( IF(sales_invoice.operator2 = '1', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '1', sales_invoice.price3, 0) ) AS add_amount, ( IF(sales_invoice.operator2 = '2', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '2', sales_invoice.price3, 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !=2 ".$where." GROUP BY sales_items.sales_invoice_id	
			UNION
			SELECT erp_sales_waybill.`waybill_id`, DATE_FORMAT(erp_sales_waybill.waybill_date,'%Y-%m-%d') as date, erp_sales_waybill.total_amount as paid_amount, null, null, erp_dealer_master.custom_name as vendor_name, erp_sales_waybill.`receipt_status` as payment_status, (CASE WHEN erp_sales_waybill.receipt_status = '4' THEN 'WB' END) as payment_type, null, null, null, null, null FROM `erp_sales_waybill` left join erp_dealer_master on erp_dealer_master.dealer_id=erp_sales_waybill.sales_dealer_id LEFT JOIN `finance_receipt_voucher` AS `receipt` ON receipt.waybill_id=erp_sales_waybill.waybill_id and receipt.payment_by = '1' WHERE `waybill_status` !='2' ".$where1." group by erp_sales_waybill.waybill_id
			
			ORDER BY date";
			
									
		}else{
			
			$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS `paid_amount`, ( IF(sales_invoice.operator2 = '1', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '1', sales_invoice.price3, 0) ) AS add_amount, ( IF(sales_invoice.operator2 = '2', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '2', sales_invoice.price3, 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'
			
			UNION
			
			SELECT erp_sales_waybill.`waybill_id`, DATE_FORMAT(erp_sales_waybill.waybill_date,'%Y-%m-%d') as date, sum(erp_sales_waybill.total_amount) as paid_amount, null, null, erp_dealer_master.custom_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '4' THEN 'WB' END) as payment_type, null, null, null, null, null FROM `erp_sales_waybill` left join erp_dealer_master on erp_dealer_master.dealer_id=erp_sales_waybill.sales_dealer_id WHERE `waybill_status` !='2' and waybill_date >='".$data['start_date']."' and waybill_date <='".$data['end_date']."'";
			
			
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
}