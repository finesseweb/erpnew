<?php

class Finance_Form_CustomerBankDetails extends Zend_Form {
    
    public function init() {
        
         $dealer_model = new Application_Model_ErpDealerMaster();
        $data = $dealer_model->getDropDownList();
        
		$dealer_id = $this->createElement('select', 'dealer_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
               // ->setRequired(true)
                //->setAttrib("required", "required")
                ->addMultiOptions(array('' => 'Select Customer '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($dealer_id);
		
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
		
       
    }

}
