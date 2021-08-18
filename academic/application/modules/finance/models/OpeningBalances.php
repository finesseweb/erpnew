<?php

class Finance_Model_OpeningBalances extends Zend_Db_Table_Abstract {

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	
	
	public function getCustomerStatement()
	{
		$select ="SELECT customer.dealer_id, customer.dealer_company_name AS name,customer.start_balance,customer.operator FROM `erp_dealer_master` AS `customer` WHERE dealer_master_status !='2' and customer.start_balance != ''";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;		
	}
	public function getVendorStatement()
	{
		$select ="SELECT vendor.vendor_id, vendor.vendor_name AS name, vendor.start_balance, vendor.operator FROM `erp_vendor_master` AS `vendor` WHERE vendor_status !='2' and vendor.start_balance != ''";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;		
	}
	public function getBankStatement()
	{
		$select ="SELECT bank.bank_id, bank.beneficiary_bank_name AS name, bank.opening_balance, bank.operator FROM `erp_bank_master` AS `bank` WHERE status !='2' and bank.opening_balance != ''";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;		
	}
	public function getAccountStatement()
	{
		$select ="SELECT account.account_id, account.account_name AS name, account.opening_balance, account.operator FROM `account_master` AS `account` WHERE status !='2' and account.opening_balance != ''";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;		
	}
	   
	
}