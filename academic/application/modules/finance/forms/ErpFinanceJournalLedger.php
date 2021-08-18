<?php

class Finance_Form_ErpFinanceJournalLedger extends Zend_Form {

    protected $_name = 'erp_finance_dealer_payouts';
    protected $_id = 'cp_id';

    public function init() {
        $start_date = $this->createElement('text', 'start_date')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)               
                ->removeDecorator("htmlTag");
        $this->addElement($start_date);

        $end_date = $this->createElement('text', 'end_date')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($end_date);
    }

}
