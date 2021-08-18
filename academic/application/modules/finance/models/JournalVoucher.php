<?php

class Finance_Model_JournalVoucher extends Zend_Db_Table_Abstract {

    protected $_name = 'journal_voucher';
    protected $_id = 'id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $vendor_payment_id
     * @return single dimention array
     */

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.voucher_id=?", $id);
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
                ->from($this->_name)
				//->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('customer.dealer_company_name as dealer_id'))
				//->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_name as vendor_id'))
                ->where("status!=2")
				//->group("$this->_name.voucher_id")
				->order("id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	/* public function getUsers()
	{
		$select ="SELECT `dealer_id`, dealer_name, dealer_company_name as com_name, dealer_company_name as `name`, (CASE WHEN dealer_master_status != '2' THEN 'C' END) as type FROM erp_dealer_master WHERE dealer_master_status !='2' and dealer_type='0' UNION SELECT `dealer_id`, dealer_name, dealer_company_name as com_name, custom_name as `name`, (CASE WHEN dealer_master_status != '2' THEN 'C' END) as type FROM erp_dealer_master WHERE dealer_master_status !='2' and dealer_type='1' group by erp_dealer_master.dealer_id UNION SELECT `bank_id`, `beneficiary_bank_name`, beneficiary_bank_name as com_name, `beneficiary_bank_name` as name, (CASE WHEN status != '2' THEN 'B' END) as type FROM erp_bank_master WHERE status !='2' and user_id='2' UNION SELECT `vendor_id`, `vendor_name`, vendor_name as com_name, `vendor_name` as name, (CASE WHEN vendor_status != '2' THEN 'V' END) as type FROM erp_vendor_master WHERE vendor_status !='2' UNION SELECT `account_id`, `status`, `account_name`as com_name, `account_name` as name, (CASE WHEN status != '2' THEN 'A' END) as type FROM account_master WHERE status !='2'";		
		$result = $this->getAdapter()
                ->fetchAll($select);
		$data = array();
        foreach ($result as $val) {            
            $data[$val['com_name'].':;'.$val['dealer_id'].':;'.$val['type']] = $val['name'];            
        }
        return $data;	
	} */
	
	public function getUsers()
	{
		$select ="SELECT `bank_id`, `beneficiary_bank_name`, beneficiary_bank_name as com_name, `beneficiary_bank_name` as name, (CASE WHEN status != '2' THEN 'B' END) as type FROM erp_bank_master WHERE status !='2' and user_id='2' UNION SELECT `vendor_id`, `vendor_name`, vendor_name as com_name, `vendor_name` as name, (CASE WHEN vendor_status != '2' THEN 'V' END) as type FROM erp_vendor_master WHERE vendor_status !='2' UNION SELECT `account_id`, `status`, `account_name`as com_name, `account_name` as name, (CASE WHEN status != '2' THEN 'A' END) as type FROM account_master WHERE status !='2'";		
		$result = $this->getAdapter()
                ->fetchAll($select);
		$data = array();
        foreach ($result as $val) {            
            $data[$val['com_name'].':;'.$val['type']] = $val['name'];            
        }
        return $data;	
	}
	
}