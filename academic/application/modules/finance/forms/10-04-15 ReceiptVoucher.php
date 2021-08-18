<?php

class Finance_Form_ReceiptVoucher extends Zend_Form {   

    public function init() {
        $FinanceCustomerPayouts_model = new Finance_Model_ErpFinanceCustomerPayouts();
        $data = $FinanceCustomerPayouts_model->getInvoiceIds();
        $tyre_invoice_id = $this->createElement('select', 'tyre_invoice_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addMultiOptions(array('' => 'Select Sale Invoice ID '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($tyre_invoice_id);
        $payment_by = $this->createElement('radio', 'payment_by')
                ->removeDecorator('label')
                ->removeDecorator("htmlTag")
                ->setMultiOptions(array('1' => 'Cash ', '2' => 'Check ', "3" => "DD "))
                ->setValue(1)
                ->setOptions(array('label_class' => array('class' => 'radionew')))
                ->setSeparator('');
        $this->addElement($payment_by);


        $ErpBankMaster_model = new Application_Model_ErpBankMaster();
        $data = $ErpBankMaster_model->getDropDownList();

        $bank_details_object = $this->createElement('select', 'bank_name')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addMultiOptions(array('' => 'Select Bank Name'))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($bank_details_object);

        $check_no = $this->createElement('text', 'check_no')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($check_no);

        $dd_no = $this->createElement('text', 'dd_no')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($dd_no);

        $transaction_amount = $this->createElement('text', 'transaction_amount')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($transaction_amount);
        $adjust_amount = $this->createElement('text', 'adjust_amount')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->setValue(0)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($adjust_amount);
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
