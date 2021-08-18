<?php

class Finance_Model_TrailBalance extends Zend_Db_Table_Abstract {

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	
	public function getCustomerAccountStatement()
	{	
		$select ="SELECT customer.dealer_id,customer.dealer_name AS vendor_name,customer.dealer_company_name,customer.start_balance,sum(payment.paid_amount) AS payment_amount,payment.party_type,sum(receipt.paid_amount) AS receipt_amount,sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_invoice.grand_total) AS paid_amount,sales_invoice.receipt_status as payment_status, (CASE WHEN sales_invoice.receipt_status = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark, sum(waybill.total_amount) AS waybill_paid_amount 
		FROM erp_dealer_master AS customer 
		LEFT JOIN erp_sales_invoice AS sales_invoice ON FIND_IN_SET(sales_invoice.dealer_id,customer.dealer_id)
		LEFT JOIN erp_sales_waybill AS waybill ON  waybill.sales_dealer_id = customer.dealer_id
		LEFT JOIN finance_payment_vouvher AS payment ON payment.vendor_id=customer.dealer_id AND payment.party_type = 'C' LEFT JOIN finance_receipt_voucher AS receipt ON receipt.dealer_id=customer.dealer_id AND receipt.party_type = 'C' WHERE customer.dealer_master_status !=2 and sales_invoice.invoice_status !=2 ";
		//GROUP BY customer.dealer_id		
		//ORDER BY payment_type";
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} 
	public function getSearchCustomerAccountStatement($start_date='',$end_date='')
	{	
		$whereSQL = '';
		$whereSQL1 = '';
		$whereSQL2 = '';
		$whereSQL .= "sales_invoice.invoice_date >'".$start_date."' and sales_invoice.invoice_date <'".$end_date."'";
		$whereSQL1 .= "OR ( payment.add_date >'".$start_date."' and payment.add_date <'".$end_date."' )";
		$whereSQL2 .= "OR (receipt.add_date >'".$start_date."' and receipt.add_date <'".$end_date."' )";
		  
		$select ="SELECT customer.dealer_id,customer.dealer_name AS vendor_name,customer.dealer_company_name,customer.start_balance,sum(payment.paid_amount) AS payment_amount,payment.party_type,sum(receipt.paid_amount) AS receipt_amount,sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_invoice.grand_total) AS paid_amount,sales_invoice.receipt_status as payment_status, (CASE WHEN sales_invoice.receipt_status = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark 
		FROM erp_dealer_master AS customer 		
		LEFT JOIN finance_payment_vouvher AS payment ON payment.vendor_id=customer.dealer_id AND payment.party_type = 'C' LEFT JOIN finance_receipt_voucher AS receipt ON receipt.dealer_id=customer.dealer_id AND receipt.party_type = 'C'
		LEFT JOIN erp_sales_invoice AS sales_invoice ON FIND_IN_SET(sales_invoice.dealer_id,customer.dealer_id) WHERE ".$whereSQL.$whereSQL1.$whereSQL2." ";//GROUP BY customer.dealer_id		
		//ORDER BY payment_type";
		//echo $select;die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} 
	public function getBankAccountStatement()
	{	
		$select ="
		SELECT bank.bank_id,bank.operator as payment_status,bank.opening_balance as bank_opening_balance,bank.beneficiary_bank_name as vendor_name,(CASE WHEN bank.operator = '1' THEN sum(payment.paid_amount) END) AS payment_amount,payment.party_type,(CASE WHEN bank.operator = '2' THEN sum(receipt.paid_amount) END)  AS receipt_amount,bank.payment_type FROM erp_bank_master AS bank
		LEFT JOIN finance_payment_vouvher AS payment ON payment.bank_id = bank.bank_id AND payment.party_type = 'B' 
		LEFT JOIN finance_receipt_voucher AS receipt ON receipt.bank_id = bank.bank_id  AND payment.party_type = 'B'
		LEFT JOIN journal_voucher AS journal ON journal.dealer_id = bank.beneficiary_bank_name 
		GROUP BY bank.bank_id";
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	
	public function getSearchBankAccountStatement($start_date='',$end_date='')
	{	
		$whereSQL = '';
		$whereSQL1 = '';
		$whereSQL2 = '';
		//$whereSQL .= "bank.invoice_date >'".$start_date."' and sales_invoice.invoice_date <'".$end_date."'";
		$whereSQL1 .= " ( payment.add_date >'".$start_date."' and payment.add_date <'".$end_date."' )";
		$whereSQL2 .= "OR (receipt.add_date >'".$start_date."' and receipt.add_date <'".$end_date."' )";
		  
		$select ="SELECT bank.bank_id,bank.operator as payment_status,bank.opening_balance as bank_opening_balance,bank.beneficiary_bank_name as vendor_name,(CASE WHEN bank.operator = '1' THEN sum(payment.paid_amount) END) AS payment_amount,payment.party_type,(CASE WHEN bank.operator = '2' THEN sum(receipt.paid_amount) END)  AS receipt_amount,bank.payment_type FROM erp_bank_master AS bank
		LEFT JOIN finance_payment_vouvher AS payment ON payment.bank_id = bank.bank_id AND payment.party_type = 'B' 
		LEFT JOIN finance_receipt_voucher AS receipt ON receipt.bank_id = bank.bank_id  AND payment.party_type = 'B'
		LEFT JOIN journal_voucher AS journal ON journal.dealer_id = bank.beneficiary_bank_name WHERE ".$whereSQL1.$whereSQL2." GROUP BY bank.bank_id";
		//echo $select;die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}

	public function getAccountStatement()
	{	
		$select ="
		SELECT account.account_name as vendor_name,account.operator as payment_status,account.opening_balance as acc_opening_balance,(CASE WHEN account.operator = '1' THEN sum(payment.paid_amount) END) AS payment_amount,payment.party_type,(CASE WHEN account.operator = '2' THEN sum(receipt.paid_amount) END)  AS receipt_amount,account.payment_type 
		FROM account_master AS account
		LEFT JOIN finance_payment_vouvher AS payment ON payment.party_names = account.account_name AND payment.party_type = 'A' 
		LEFT JOIN finance_receipt_voucher AS receipt ON receipt.party_names = account.account_name  AND payment.party_type = 'A'		 
		GROUP BY account.account_id";
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	public function getSearchAccountStatement($start_date='',$end_date='')
	{	
		$whereSQL = '';
		$whereSQL1 = '';
		$whereSQL2 = '';
		//$whereSQL .= "bank.invoice_date >'".$start_date."' and sales_invoice.invoice_date <'".$end_date."'";
		$whereSQL1 .= " ( payment.add_date >'".$start_date."' and payment.add_date <'".$end_date."' )";
		$whereSQL2 .= "OR (receipt.add_date >'".$start_date."' and receipt.add_date <'".$end_date."' )";
		  
		$select ="SELECT account.account_name as vendor_name,account.operator as payment_status,account.opening_balance as acc_opening_balance,(CASE WHEN account.operator = '1' THEN sum(payment.paid_amount) END) AS payment_amount,payment.party_type,(CASE WHEN account.operator = '2' THEN sum(receipt.paid_amount) END)  AS receipt_amount,account.payment_type 
		FROM account_master AS account
		LEFT JOIN finance_payment_vouvher AS payment ON payment.party_names = account.account_name AND payment.party_type = 'A' 
		LEFT JOIN finance_receipt_voucher AS receipt ON receipt.party_names = account.account_name  AND payment.party_type = 'A'	 WHERE ".$whereSQL1.$whereSQL2." GROUP BY account.account_id";
		//echo $select;die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	public function getVendorAccountStatement()
	{	
		$select ="SELECT vendor.vendor_id,vendor.vendor_name,vendor.operator as vendor_payment_status,vendor.opening_balance as vendor_opening_balance,(CASE WHEN vendor.operator = '1' THEN sum(payment.paid_amount) END) AS payment_amount,payment.party_type,(CASE WHEN vendor.operator = '2' THEN sum(receipt.paid_amount) END)  AS receipt_amount,(CASE WHEN purchase_invoice.payment_status = '5' THEN 'PI' END) as payment_type,sum(purchase_invoice.grand_total_amount) AS paid_amount,purchase_invoice.payment_status 
		FROM erp_vendor_master AS vendor		
		LEFT JOIN erp_purchase_invoice AS purchase_invoice ON purchase_invoice.vendor_id = vendor.vendor_id 
		LEFT JOIN finance_payment_vouvher AS payment ON payment.vendor_id = vendor.vendor_id AND payment.party_type = 'V' 
		LEFT JOIN finance_receipt_voucher AS receipt ON receipt.dealer_id = vendor.vendor_id  AND payment.party_type = 'V'		 
		";
		//GROUP BY vendor.vendor_id		
		//ORDER BY payment_type";
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}	
	public function getSearchVendorAccountStatement($start_date='',$end_date='')
	{	
		$whereSQL = '';
		$whereSQL1 = '';
		$whereSQL2 = '';
		$whereSQL .= "purchase_invoice.invoice_date >'".$start_date."' and purchase_invoice.invoice_date <'".$end_date."'";
		$whereSQL1 .= "OR ( payment.add_date >'".$start_date."' and payment.add_date <'".$end_date."' )";
		$whereSQL2 .= "OR (receipt.add_date >'".$start_date."' and receipt.add_date <'".$end_date."' )";
		  
		$select ="SELECT vendor.vendor_id,vendor.vendor_name,vendor.operator as vendor_payment_status,vendor.opening_balance as vendor_opening_balance,(CASE WHEN vendor.operator = '1' THEN sum(payment.paid_amount) END) AS payment_amount,payment.party_type,(CASE WHEN vendor.operator = '2' THEN sum(receipt.paid_amount) END)  AS receipt_amount,(CASE WHEN purchase_invoice.payment_status = '5' THEN 'PI' END) as payment_type,sum(purchase_invoice.grand_total_amount) AS paid_amount,purchase_invoice.payment_status 
		FROM erp_vendor_master AS vendor		
		LEFT JOIN erp_purchase_invoice AS purchase_invoice ON purchase_invoice.vendor_id = vendor.vendor_id 
		LEFT JOIN finance_payment_vouvher AS payment ON payment.vendor_id = vendor.vendor_id AND payment.party_type = 'V' 
		LEFT JOIN finance_receipt_voucher AS receipt ON receipt.dealer_id = vendor.vendor_id  AND payment.party_type = 'V'	 WHERE ".$whereSQL.$whereSQL1.$whereSQL2."";
		// GROUP BY vendor.vendor_id		
		//ORDER BY payment_type";
		//echo $select;die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	   // echo $query = $user->select()->where('firstname LIKE ?', $uname.'%')->ORwhere('lastname LIKE ?', $_POST['lname'].'%')->ORwhere('emailid LIKE ?', $_POST['email'].'%');

	/* public function getAccountStatementByDates($data)
	{
		
		
		if( !empty($data['party_id']) ){
		
			$where = '';
			$where1 = '';
			$where2 = '';
			$where3 = '';
			$where4 = '';
			$where5 = '';
			$where6 = '';
			$where7 = '';
			$where8 = '';
			
			if(!empty( $data['party_id'] )){
				$where .= " and dealer_company_name ='".$data['party_id']."'";
				$where1 .= " and vendor_name ='".$data['party_id']."'";
				$where2 .= " and dealer_company_name ='".$data['party_id']."' OR party_names ='".$data['party_id']."'";
				$where3 .= " and vendor_name ='".$data['party_id']."' OR party_names ='".$data['party_id']."'";
				$where4 .= " and dealer_id ='".$data['party_id']."'";
				$where5 .= " and vendor_id ='".$data['party_id']."'";
				$where6 .= " and vendor_name ='".$data['party_id']."'";
				$where7 .= " and dealer_company_name ='".$data['party_id']."'";
				$where8 .= " OR account.account_name ='".$data['party_id']."'";	
			}
			if(!empty( $data['start_date'] )){
				$where .= " and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";
				$where1 .= " and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."'";
				$where2 .= " and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";
				$where3 .= " and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";
				$where4 .= " and date >='".$data['start_date']."' and date <='".$data['end_date']."'";
				$where5 .= " and date >='".$data['start_date']."' and date <='".$data['end_date']."'";
				$where6 .= " and invoice_due_date >='".$data['start_date']."' and invoice_due_date <='".$data['end_date']."'";
				$where7 .= " and waybill_date >='".$data['start_date']."' and waybill_date <='".$data['end_date']."'";				
			}
			
			$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sales_invoice.grand_total AS `paid_amount`, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark, receipt.payment_by FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id LEFT JOIN `finance_receipt_voucher` AS `receipt` ON receipt.invoice_id=sales_invoice.sales_invoice_id and receipt.payment_by = '1' WHERE sales_invoice.invoice_status !=2 ".$where." GROUP BY sales_items.sales_invoice_id		
			UNION 		
			SELECT invoice.purchase_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, invoice.grand_total_amount AS `paid_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, account.account_id,account.account_name, null, null, payment.payment_by FROM `erp_purchase_invoice` AS `invoice` 
			LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id 
			LEFT JOIN `finance_payment_vouvher` AS `payment` ON payment.invoice_id=invoice.purchase_invoice_id and payment.payment_by = '1' 
			LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id
			LEFT JOIN `account_master` AS `account` ON account.account_id=purchase_items.account_id 
			WHERE purchase_invoice_status !=2 ".$where1.$where8." GROUP BY invoice.purchase_invoice_id
			
			UNION
			SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '4' THEN 'PV' END) as payment_type,  remarks as invoice_increment_id, null, null, null, null, null FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and payment_by !='1' ".$where3."
			
			UNION 
			
			SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`,  erp_dealer_master.dealer_company_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '5' THEN 'RV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null, null FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and payment_by !='1' ".$where2." 
			
			UNION

			SELECT `id`, DATE_FORMAT(date,'%Y-%m-%d') as date, `credit_amount` as paid_amount, `dealer_id` as vendor_name, (`payment_type`) as payment_status, payment_type, remarks as invoice_increment_id, null, null, null, null, null FROM `journal_voucher` WHERE status !=2 and payment_type = '5' ".$where4."

			UNION

			SELECT `id`, DATE_FORMAT(date,'%Y-%m-%d') as date, `debit_amount` as paid_amount, `vendor_id` as vendor_name, (CASE WHEN `payment_type` = '5' THEN '4' END) as payment_status, payment_type, remark1 as invoice_increment_id, null, null, null, null, null FROM `journal_voucher` WHERE status !=2 and payment_type = '5' ".$where5."
			
			UNION
		
			SELECT invoice.`commercial_invoice_id`, DATE_FORMAT(invoice.invoice_due_date,'%Y-%m-%d') as date, invoice.amount_inr as paid_amount,  erp_vendor_master.vendor_name, invoice.payment_status, (CASE WHEN invoice.payment_status = '5' THEN 'PCI' END) as payment_type, invoice.commercial_invoice_id as invoice_increment_id, null, null, null, null, payment.payment_by FROM `erp_purchase_commercial_invoice` as invoice left join erp_vendor_master on erp_vendor_master.vendor_id=invoice.vendor_id LEFT JOIN `finance_payment_vouvher` AS `payment` ON payment.commercial_invoice_id=invoice.commercial_invoice_id and payment.payment_by = '1' WHERE `commercial_invoice_status` !='2' ".$where6." group by invoice.commercial_invoice_id
			UNION		
			SELECT erp_sales_waybill.`waybill_id`, DATE_FORMAT(erp_sales_waybill.waybill_date,'%Y-%m-%d') as date, erp_sales_waybill.total_amount as paid_amount, erp_dealer_master.custom_name as vendor_name, erp_sales_waybill.`receipt_status` as payment_status, (CASE WHEN erp_sales_waybill.receipt_status = '4' THEN 'WB' END) as payment_type, null, null, null, null, null, receipt.payment_by FROM `erp_sales_waybill` left join erp_dealer_master on erp_dealer_master.dealer_id=erp_sales_waybill.sales_dealer_id LEFT JOIN `finance_receipt_voucher` AS `receipt` ON receipt.waybill_id=erp_sales_waybill.waybill_id and receipt.payment_by = '1' WHERE `waybill_status` !='2' ".$where7." group by erp_sales_waybill.waybill_id
						
			ORDER BY date";
			
			
									
		}else{
			
			$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sales_invoice.grand_total AS `paid_amount`, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY sales_items.sales_invoice_id		
			UNION 		
			SELECT invoice.purchase_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, invoice.grand_total_amount AS `paid_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY invoice.purchase_invoice_id
			UNION
			SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '4' THEN 'PV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and payment_by !='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'
			
			UNION 
			
			SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, erp_dealer_master.dealer_company_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '5' THEN 'RV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and payment_by !='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."' 
			
			UNION

			SELECT `id`, DATE_FORMAT(date,'%Y-%m-%d') as date, `credit_amount` as paid_amount, `dealer_id` as vendor_name, (`payment_type`) as payment_status, payment_type, remarks as invoice_increment_id, null, null, null, null FROM `journal_voucher` WHERE status !=2 and payment_type = '5' and date >='".$data['start_date']."' and date <='".$data['end_date']."'

			UNION

			SELECT `id`, DATE_FORMAT(date,'%Y-%m-%d') as date, `debit_amount` as paid_amount, `vendor_id` as vendor_name, (CASE WHEN `payment_type` = '5' THEN '4' END) as payment_status, payment_type, remark1 as invoice_increment_id, null, null, null, null FROM `journal_voucher` WHERE status !=2 and payment_type = '5' and date >='".$data['start_date']."' and date <='".$data['end_date']."'
			
			UNION
		
			SELECT invoice.`commercial_invoice_id`, DATE_FORMAT(invoice.invoice_due_date,'%Y-%m-%d') as date, invoice.amount_inr as paid_amount, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '5' THEN 'PCI' END) as payment_type, invoice.commercial_invoice_id as invoice_increment_id, null, null, null, null FROM `erp_purchase_commercial_invoice` as invoice left join erp_vendor_master on erp_vendor_master.vendor_id=invoice.vendor_id WHERE `commercial_invoice_status` !='2' and invoice_due_date >='".$data['start_date']."' and invoice_due_date <='".$data['end_date']."'
			UNION		
			SELECT erp_sales_waybill.`waybill_id`, DATE_FORMAT(erp_sales_waybill.waybill_date,'%Y-%m-%d') as date, erp_sales_waybill.total_amount as paid_amount, erp_dealer_master.custom_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '4' THEN 'WB' END) as payment_type, null, null, null, null, null FROM `erp_sales_waybill` left join erp_dealer_master on erp_dealer_master.dealer_id=erp_sales_waybill.sales_dealer_id WHERE `waybill_status` !='2' and waybill_date >='".$data['start_date']."' and waybill_date <='".$data['end_date']."'			
			ORDER BY date";
			
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} */
	
	
	/* public function getAccountStatementByDatesForPurchase($data)
	{
				
		if( !empty($data['party_id']) ){		
			
			$where1 = '';			
			$where6 = '';
			$where7 = '';
			 //if(!empty( $data['type'] )){
				//$where .= " and receipt_status ='".$data['type']."'";
				//$where1 .= " and payment_status ='".$data['type']."'";
			//} 
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
			
			$select ="		
			SELECT invoice.purchase_invoice_id as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (CASE WHEN invoice.operator1 = '1' THEN sum(invoice.price) END) AS `add_amount`, (CASE WHEN invoice.operator1 = '2' THEN sum(invoice.price) END) AS `sub_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY purchase_items.purchase_invoice_no
			
			UNION
		
			SELECT invoice.`commercial_invoice_id`, DATE_FORMAT(invoice.invoice_due_date,'%Y-%m-%d') as date, invoice.amount_inr as paid_amount, null, null, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '5' THEN 'PCI' END) as payment_type, invoice.commercial_invoice_id as invoice_increment_id, null, null, null, null FROM `erp_purchase_commercial_invoice` as invoice left join erp_vendor_master on erp_vendor_master.vendor_id=invoice.vendor_id WHERE `commercial_invoice_status` !='2' and invoice_due_date >='".$data['start_date']."' and invoice_due_date <='".$data['end_date']."'
			
			ORDER BY date";			
			
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} */
	
	//Get Vat For Domestic Purchase
	/* public function getAccountStatementByDatesForPurchaseVat($data)
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
			
		
			
		}else{
			$select ="		
			SELECT invoice.bill_no as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (IF(invoice.operator1 = '1', invoice.price, 0) ) AS add_amount, ( IF(invoice.operator1 = '2', invoice.price, 0) ) AS sub_amount, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY invoice.purchase_invoice_id ORDER BY date";	
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} */
	
	/* public function getAccountStatementByDatesBeforePurchase($data)
	{					
			$select ="		
			SELECT invoice.purchase_invoice_id as sales_invoice_id, DATE_FORMAT(invoice.invoice_date,'%Y-%m-%d') as date, sum(purchase_invoice_item_price * purchase_invoice_item_approved_quantity ) AS `paid_amount`, (CASE WHEN invoice.operator1 = '1' THEN sum(invoice.price) END) AS `add_amount`, (CASE WHEN invoice.operator1 = '2' THEN sum(invoice.price) END) AS `sub_amount`, `vendor`.`vendor_name`, `invoice`.`payment_status`, (CASE WHEN `invoice`.`payment_status` = '5' THEN 'PI' END) as payment_type, (invoice.purchase_invoice_id) as invoice_increment_id, null, null, null, null FROM `erp_purchase_invoice` AS `invoice` LEFT JOIN `erp_purchse_invoice_items` AS `purchase_items` ON purchase_items.purchase_invoice_no = invoice.purchase_invoice_id LEFT JOIN `erp_vendor_master` AS `vendor` ON vendor.vendor_id=invoice.vendor_id WHERE purchase_invoice_status !='2' and invoice_date <='".$data['start_date']."'			
			ORDER BY date";	
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchRow($select);
        return $result;	
	} */
	
	/* public function getAccountStatementByDatesForCash($data)
	{
				
		if( !empty($data['party_id']) ){		
			
			$where2 = '';			
			$where3 = '';
			 //if(!empty( $data['type'] )){
				//$where .= " and receipt_status ='".$data['type']."'";
				//$where1 .= " and payment_status ='".$data['type']."'";
			//} 
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
			
									
		}else{
			
			$select ="SELECT finance_payment_vouvher.`payment_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, (sub_amount) as add_amount, sub_amount, erp_vendor_master.vendor_name, payment_status, (CASE WHEN payment_status = '4' THEN 'PV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and payment_by='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'
			
			UNION 
			
			SELECT finance_receipt_voucher.`receipt_voucher_id` as sales_invoice_id, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, (sub_amount) as add_amount, sub_amount, erp_dealer_master.dealer_company_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '5' THEN 'RV' END) as payment_type, remarks as invoice_increment_id, null, null, null, null FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and payment_by='1' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'
			
			ORDER BY date";			
			
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} */
	
	//CST Account
	/* public function getAccountStatementByDatesForSalesCST($data)
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
			
			$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS `paid_amount`, ( IF(sales_invoice.operator1 = '0', sales_invoice.price1, 0) ) AS add_amount, ( IF(sales_invoice.operator1 = '1', sales_invoice.price1, 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY sales_items.sales_invoice_id
			
			ORDER BY date";			
			
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} */
	
	//Sales Account
	/* public function getAccountStatementByDatesForSalesAccount($data)
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
			
			$select ="SELECT sales_invoice.sales_invoice_id, DATE_FORMAT(sales_invoice.invoice_date,'%Y-%m-%d') as date, sum(sales_items.total_price) AS `paid_amount`, ( IF(sales_invoice.operator2 = '1', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '1', sales_invoice.price3, 0) ) AS add_amount, ( IF(sales_invoice.operator2 = '2', sales_invoice.price2, 0) + IF(sales_invoice.operator3 = '2', sales_invoice.price3, 0) ) AS sub_amount, `customer`.`dealer_company_name` as vendor_name, `sales_invoice`.`receipt_status` as payment_status, (CASE WHEN `sales_invoice`.`receipt_status` = '4' THEN 'SI' END) as payment_type, sales_invoice.invoice_increment_id, sales_invoice.name_of_transport, sales_invoice.no_of_packages, sales_invoice.despatched_through, sales_invoice.private_mark FROM `erp_sales_invoice` AS `sales_invoice` LEFT JOIN `erp_sales_invoice_items` AS `sales_items` ON sales_items.sales_invoice_id = sales_invoice.sales_invoice_id LEFT JOIN `erp_dealer_master` AS `customer` ON customer.dealer_id=sales_invoice.dealer_id WHERE sales_invoice.invoice_status !='2' and invoice_date >='".$data['start_date']."' and invoice_date <='".$data['end_date']."' GROUP BY sales_items.sales_invoice_id
			UNION
			SELECT erp_sales_waybill.`waybill_id`, DATE_FORMAT(erp_sales_waybill.waybill_date,'%Y-%m-%d') as date, erp_sales_waybill.total_amount as paid_amount, null, null, erp_dealer_master.custom_name as vendor_name, `receipt_status` as payment_status, (CASE WHEN receipt_status = '4' THEN 'WB' END) as payment_type, null, null, null, null, null FROM `erp_sales_waybill` left join erp_dealer_master on erp_dealer_master.dealer_id=erp_sales_waybill.sales_dealer_id WHERE `waybill_status` !='2' and waybill_date >='".$data['start_date']."' and waybill_date <='".$data['end_date']."'	
			ORDER BY date";			
			
		}		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} */
	
	/* public function getUsers()
	{
		$select ="SELECT `dealer_id`, dealer_name, dealer_company_name as com_name, dealer_company_name as `name` FROM erp_dealer_master WHERE dealer_master_status !='2' and dealer_type='0' UNION SELECT `dealer_id`, dealer_name, dealer_company_name as com_name, custom_name as `name` FROM erp_dealer_master JOIN erp_sales_waybill AS waybill ON waybill.sales_dealer_id=erp_dealer_master.dealer_id WHERE dealer_master_status !='2' and dealer_type='1' group by erp_dealer_master.dealer_id UNION SELECT `bank_id`, `beneficiary_bank_name`, beneficiary_bank_name as com_name, `beneficiary_bank_name` as name FROM erp_bank_master WHERE status !='2' and user_id='2' UNION SELECT `vendor_id`, `vendor_name`, vendor_name as com_name, `vendor_name` as name FROM erp_vendor_master WHERE vendor_status !='2' UNION SELECT `account_id`, `status`, `account_name`as com_name, `account_name` as name FROM account_master WHERE status !='2'";		
		$result = $this->getAdapter()
                ->fetchAll($select);
		$data = array();
        foreach ($result as $val) {            
            $data[$val['com_name']] = $val['name'];            
        }
        return $data;	
	}  */
	
	/* public function customerOpeningBalance($party_id = "") {
        $select = $this->_db->select()
                ->from('erp_dealer_master', array("sum( ABS(start_balance) ) as start_balance", "operator"))
				->where("dealer_company_name=?", $party_id)
                ->where("dealer_master_status!=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    } */
	
	/* public function vendorOpeningBalance($party_id = "") {
        $select = $this->_db->select()
                ->from('erp_vendor_master', array("sum( ABS(start_balance) ) as start_balance", "operator"))
				->where("vendor_name=?", $party_id)
                ->where("vendor_status!=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    } */
	
	/* public function bankOpeningBalance($party_id = "") { 
        $select = $this->_db->select()
                ->from('erp_bank_master', array("sum( ABS(opening_balance) ) as start_balance", "operator"))
				->where("beneficiary_bank_name=?", $party_id)
                ->where("status!=?", 2);
				//echo $select;die;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    } */
	
	/* public function accountOpeningBalance($party_id = "") {
        $select = $this->_db->select()
                ->from('account_master', array("sum( ABS(opening_balance) ) as start_balance", "operator"))
				->where("account_name=?", $party_id)
                ->where("status!=?", 2);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    } */
	
	
	/* public function getUserEmail($party_name)
	{
		$select ="SELECT dealer_name, dealer_company_name as `name`, dealer_email_id as email FROM erp_dealer_master WHERE dealer_master_status !='2' and dealer_company_name='".$party_name."' UNION SELECT `vendor_name`, `vendor_name` as name, vendor_email_id as email FROM erp_vendor_master WHERE vendor_status !='2' and vendor_name='".$party_name."'";		
		$result = $this->getAdapter()
                ->fetchRow($select);		
        return $result;	
	} */
}