<?php

class Finance_Model_VendorBankDetails extends Zend_Db_Table_Abstract {

    protected $_name = 'finance_vendor_bank_details';
    protected $_id = 'ven_id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $daily_book_id
     * @return single dimention array
     */
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
				
                ->where("$this->_id=?", $id);
        $result = $this->getAdapter()
                ->fetchRow($select);
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
				->joinLeft(array("vendor_master" => "erp_vendor_master"), "vendor_master.vendor_id=finance_vendor_bank_details.ven_id")
                ->where("status!=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    } 
	
	public function getInvoiceIds() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("status!=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {            
            $data[$val['ven_id']] = $val['ven_bank_name'];            
        }
        return $data;
    }

}
