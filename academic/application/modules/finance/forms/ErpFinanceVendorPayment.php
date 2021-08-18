<?php

class Finance_Form_ErpFinanceVendorPayment extends Zend_Form {

    protected $_name = 'erp_finance_vendor_payment';
    protected $_id = 'vendor_payment_id';

    public function init() {
        $FinanceVendorPayment_model = new Finance_Model_ErpFinanceVendorPayment();
        $data = $FinanceVendorPayment_model->getInvoiceIds();
        $purchase_invoice_id = $this->createElement('select', 'purchase_invoice_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addMultiOptions(array('' => 'Select Purchase Invoice ID '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($purchase_invoice_id);
        $ErpBankMaster_model = new Application_Model_ErpBankMaster();
        $data = $ErpBankMaster_model->getDropDownList();

        $bank_id = $this->createElement('select', 'bank_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addMultiOptions(array('' => 'Select Bank Name'))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($bank_id);
		
		$ErpBankChequeMaster_model = new Application_Model_ErpBankChequeMaster();
		$data = $ErpBankChequeMaster_model->getDropDownList();
		
        $check_id = $this->createElement('select', 'check_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->addMultiOptions(array('' => 'Select Cheque Number'))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($check_id);
        $dd_no = $this->createElement('text', 'dd_no')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($dd_no);

        $payment_by = $this->createElement('radio', 'payment_by')
                ->removeDecorator('label')
                ->removeDecorator("htmlTag")
                ->setMultiOptions(array('1' => 'Cash ', '2' => 'Cheque ', "3" => "DD "))
                ->setValue(1)
                ->setOptions(array('label_class' => array('class' => 'radionew')))
                ->setSeparator('');
        $this->addElement($payment_by);

        $transaction_amount = $this->createElement('text', 'transaction_amount')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($transaction_amount);

        $employee_list_model = new Application_Model_ErpEmployees();
        $data = $employee_list_model->getEmployeeList();
        $approved_by = $this->createElement('select', 'approved_by')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addMultiOptions(array('' => 'Select Approved By '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($approved_by);
    }

}
