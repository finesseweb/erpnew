<?php

class Finance_Model_ErpFinanceCustomerPayouts extends Zend_Db_Table_Abstract {

    protected $_name = 'erp_finance_dealer_payouts';
    protected $_id = 'cp_id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */
    public function getRecord($cp_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_id=?", $cp_id)
        ;
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
                ->order("$this->_id DESC")
                ->where("status!=2");
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getInvoiceIds() {
        $select = $this->_db->select()
                ->from(array("payable_sales_invoice" => "payable_sales_invoice"), array('tyre_invoice_id'))
                ->order('payable_sales_invoice.tyre_invoice_id DESC');
        $result = $this->getAdapter()
                ->fetchAll($select);
        $data = array();
        foreach ($result as $val) {
            if (strlen($val['tyre_invoice_id']) == 1) {
                $data[$val['tyre_invoice_id']] = @(SALE_INVOICE) . "000" . $val['tyre_invoice_id'];
            } else
            if (strlen($val['tyre_invoice_id']) == 2) {
                $data[$val['tyre_invoice_id']] = @(SALE_INVOICE) . "00" . $val['tyre_invoice_id'];
            } else
            if (strlen($val['tyre_invoice_id']) == 3) {
                $data[$val['tyre_invoice_id']] = @(SALE_INVOICE) . "0" . $val['tyre_invoice_id'];
            } else {
                $data[$val['tyre_invoice_id']] = @(SALE_INVOICE) . "" . $val['tyre_invoice_id'];
            }
        }
        return $data;
    }

    public function getInvoiceDetails() {

        $CompanyMaster_model = new Application_Model_ErpCompanyMaster();
        $company_details = $CompanyMaster_model->getRecord(2);
        $this->view->company_details = $company_details;
        $ErpSalesTyreDispatchFormTyres_model = new Application_Model_ErpSalesTyreDispatchFormTyres();
        $dispatchDetails = $ErpSalesTyreDispatchFormTyres_model->getDispatchtyres($tyre_dispatch_id);
        $this->view->dispatchDetails = $dispatchDetails;
    }

    public function getRecordsByDates($startdate, $enddate) {
        $select = $this->_db->select()
                ->from($this->_name, array("transaction_amount", "adjust_amount", "tyre_invoice_id", "date(added_date) as date", "added_date", "cp_id"))
                ->where("status!=2")
                ->where("added_date between '{$startdate}' and ADDDATE('{$enddate}',INTERVAL 1 DAY)")
                ->order("added_date ASC");

        $result = $this->getAdapter()
                ->fetchAll($select);
        //echo '<pre>';print_r($result);exit;
        return $result;
    }

}
