<?php

class Finance_Model_ErpFinanceDailyBook extends Zend_Db_Table_Abstract {

    protected $_name = 'erp_finance_daily_book';
    protected $_id = 'daily_book_id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $daily_book_id
     * @return single dimention array
     */
    public function getRecord($daily_book_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_id=?", $daily_book_id);
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
                ->where("status!=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRecordsByDates($startdate, $enddate) {
        $select = $this->_db->select()
                ->from($this->_name, array("transaction_amount", "trasaction_type", "daily_book_id", "transaction_date as date", "transaction_date"))
                ->where("status!=2")
                ->where("transaction_date between '{$startdate}' and ADDDATE('{$enddate}',INTERVAL 1 DAY)")
                ->order("transaction_date ASC");

        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

}
