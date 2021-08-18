<?php

class Finance_Form_BankDetails extends Zend_Form {
    
    public function init() {
        
        $bank_name = $this->createElement('text', 'bank_name')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($bank_name);
		
		$bank_acc_num = $this->createElement('text', 'bank_acc_num')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($bank_acc_num);
		
		$bank_branch = $this->createElement('text', 'bank_branch')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($bank_branch);
		
		$bank_city = $this->createElement('text', 'bank_city')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($bank_city);
		
		$ifsc_code = $this->createElement('text', 'ifsc_code')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($ifsc_code);
		
		$cheque_str_num = $this->createElement('text', 'cheque_str_num')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($cheque_str_num);
		
		$cheque_end_num = $this->createElement('text', 'cheque_end_num')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($cheque_end_num);
       
    }

}
