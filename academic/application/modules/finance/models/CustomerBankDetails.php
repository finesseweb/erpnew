<?php

class Finance_Model_CustomerBankDetails extends Zend_Db_Table_Abstract {

    protected $_name = 'dealer_bank_details';
    protected $_id = 'cus_id';

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
				->joinLeft(array("dealer_master" => "erp_dealer_master"), "dealer_master.dealer_id=dealer_bank_details.dealer_id")
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
            $data[$val['cus_id']] = $val['cus_bank_name'];            
        }
        return $data;
    }

}
