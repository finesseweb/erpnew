<?php

class Finance_Model_JournalVoucherMain extends Zend_Db_Table_Abstract {

    protected $_name = 'journal_voucher_main';
    protected $_id = 'voucher_id';

   /*  public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
				//->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('dealer_id', 'dealer_name'))
				//->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_id', 'vendor_name'))
                ->where("$this->_id=?", $id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }	
	
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name)
				//->joinLeft('erp_dealer_master as customer', "customer.dealer_id=$this->_name.dealer_id", array('customer.dealer_company_name as dealer_id'))
				//->joinLeft('erp_vendor_master as vendor', "vendor.vendor_id=$this->_name.vendor_id", array('vendor_name as vendor_id'))
                ->where("status!=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	public function getUsers()
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
}