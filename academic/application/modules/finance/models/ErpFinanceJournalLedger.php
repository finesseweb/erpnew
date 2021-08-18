<?php

class Finance_Model_ErpFinanceJournalLedger extends Zend_Db_Table_Abstract {

    protected $_name = 'erp_finance_dealer_payouts';
    protected $_id = 'cp_id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	public function journalVoucher()
	{
		$select ="SELECT *, DATE_FORMAT(`date`,'%Y-%m-%d') as date FROM `journal_voucher` where status !='2' ORDER BY date";		
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
	}
	
	public function getPaymentVoucher()
	{
		$select ="SELECT *, DATE_FORMAT(add_date,'%Y-%m-%d') as date FROM `finance_payment_vouvher` where status !='2' ORDER BY add_date";
		$select2 ="SELECT *, DATE_FORMAT(add_date,'%Y-%m-%d') as date FROM `finance_receipt_voucher` where status !='2' ORDER BY add_date";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	
	public function getReceiptVoucher()
	{
		$select ="SELECT *, DATE_FORMAT(add_date,'%Y-%m-%d') as date FROM `finance_receipt_voucher` where status !='2' ORDER BY add_date";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	
	
	
	public function journalVoucherByDates($data)
	{
		$select ="SELECT *, DATE_FORMAT(`date`,'%Y-%m-%d') as date FROM `journal_voucher` where status !='2' ORDER BY date";
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
	}
	
	public function getPaymentVoucherByDates($data)
	{
		$select ="SELECT *, DATE_FORMAT(add_date,'%Y-%m-%d') as date FROM `finance_payment_vouvher` where add_date between '.".$data['start_date']."' and ADDDATE('".$data['end_date']."',INTERVAL 1 DAY) ORDER BY add_date";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	
	public function getReceiptVoucherByDates($data)
	{
		$select ="SELECT *, DATE_FORMAT(add_date,'%Y-%m-%d') as date FROM `finance_receipt_voucher` where add_date between '.".$data['start_date']."' and ADDDATE('".$data['end_date']."',INTERVAL 1 DAY) ORDER BY add_date";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} 
	
	public function getAccountStatement()
	{
		$select ="SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, (CASE WHEN payment_by = '1' THEN 'CASH' WHEN payment_by = '2' THEN 'CHEQUE' WHEN payment_by = '3' THEN 'NEFT/RTGS' END) as payment_by, payment_status FROM `finance_payment_vouvher` WHERE `status` !='2' UNION SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, (CASE WHEN payment_by = '1' THEN 'CASH' WHEN payment_by = '2' THEN 'CHEQUE' WHEN payment_by = '3' THEN 'NEFT/RTGS' END) as payment_by, receipt_status FROM `finance_receipt_voucher` WHERE `status` !='2' ORDER BY date";		
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} 
	
	public function getAccountStatementByDates($data)
	{
		$select ="SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, (CASE WHEN payment_by = '1' THEN 'CASH' WHEN payment_by = '2' THEN 'CHEQUE' WHEN payment_by = '3' THEN 'NEFT/RTGS' END) as payment_by, payment_status FROM `finance_payment_vouvher` WHERE `status` !='2' and add_date between '.".$data['start_date']."' and ADDDATE('".$data['end_date']."',INTERVAL 1 DAY) UNION SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, (CASE WHEN payment_by = '1' THEN 'CASH' WHEN payment_by = '2' THEN 'CHEQUE' WHEN payment_by = '3' THEN 'NEFT/RTGS' END) as payment_by, receipt_status FROM `finance_receipt_voucher` WHERE `status` !='2' and add_date between '.".$data['start_date']."' and ADDDATE('".$data['end_date']."',INTERVAL 1 DAY) ORDER BY date";		
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} 

}
