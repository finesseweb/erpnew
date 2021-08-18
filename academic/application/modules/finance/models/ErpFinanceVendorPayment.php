<?php


class Finance_Model_ErpFinanceVendorPayment extends Zend_Db_Table_Abstract {

    protected $_name = 'erp_finance_vendor_payment';
    protected $_id = 'vendor_payment_id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $vendor_payment_id
     * @return single dimention array
     */
    public function getRecord($vendor_payment_id) {
        $select = $this->_db->select()
                ->from($this->_name)
				->joinleft(array("erp_finance_bank_master" => "erp_finance_bank_master"), "erp_finance_bank_master.bank_id=$this->_name.bank_id",array('bank_id','bank_name'))
				
                ->where("$this->_id=?", $vendor_payment_id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    public function getRecordsByDates($startdate, $enddate) {
        $select = $this->_db->select()
                ->from($this->_name, array("*", "date(added_date) as date"))
                ->where("status!=2")
                ->where("added_date between '{$startdate}' and ADDDATE('{$enddate}',INTERVAL 1 DAY)")
                ->order("added_date ASC");

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
                ->where("status!=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getInvoiceIds() {
        $select = $this->_db->select()
                ->from(array("payable_purchase_invoice" => "payable_purchase_invoice"), array('purchase_invoice_id'))
                ->order('payable_purchase_invoice.purchase_invoice_id DESC');
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            if (strlen($val['purchase_invoice_id']) == 1) {
                $data[$val['purchase_invoice_id']] = @(PI_PREFIX) . "000" . $val['purchase_invoice_id'];
            } else
            if (strlen($val['purchase_invoice_id']) == 2) {
                $data[$val['purchase_invoice_id']] = @(PI_PREFIX) . "00" . $val['purchase_invoice_id'];
            } else
            if (strlen($val['purchase_invoice_id']) == 3) {
                $data[$val['purchase_invoice_id']] = @(PI_PREFIX) . "0" . $val['purchase_invoice_id'];
            } else {
                $data[$val['purchase_invoice_id']] = @(PI_PREFIX) . "" . $val['purchase_invoice_id'];
            }
        }
        return $data;
    }

}
