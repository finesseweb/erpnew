<?php

class Finance_Model_PettyCash extends Zend_Db_Table_Abstract {

     protected $_name = 'petty_cash_transaction';
    protected $_id = 'petty_cash_transaction_id';

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
                ->from($this->_name);
                //->where("status!=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    } 
	
	/* public function getDropdownList() {
        $select = $this->_db->select()
                ->from($this->_name);
                //->where("status!=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {            
            $data[$val['fb_id']] = $val['bank_name'];            
        }
        return $data;
    } */ 

}
