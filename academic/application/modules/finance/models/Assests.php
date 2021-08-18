<?php

class Finance_Model_Assests extends Zend_Db_Table_Abstract {

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	 
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