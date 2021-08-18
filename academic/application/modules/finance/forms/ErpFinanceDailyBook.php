<?php

class Finance_Form_ErpFinanceDailyBook extends Zend_Form {

    protected $_name = 'erp_finance_daily_book';
    protected $_id = 'daily_book_id';

    public function init() {

        $trasaction_type = $this->createElement('radio', 'trasaction_type')
                ->removeDecorator('label')
                ->removeDecorator("htmlTag")
                ->setMultiOptions(array('1' => 'Credit ', '2' => 'Debit '))
                ->setValue(2)
                ->setOptions(array('label_class' => array('class' => 'radionew')))
                ->setSeparator('');

        $this->addElement($trasaction_type);

        $transaction_amount = $this->createElement('text', 'transaction_amount')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($transaction_amount);
        $transaction_date = $this->createElement('text', 'transaction_date')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'datepicker'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($transaction_date);

        $remarks = $this->createElement('textarea', 'remarks')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('rows', '2')
                ->setAttrib('cols', '3')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($remarks);
    }

}
