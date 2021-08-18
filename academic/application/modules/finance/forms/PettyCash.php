<?php

class Finance_Form_PettyCash extends Zend_Form {   

    public function init() {
        
        $chart_of_account_id = $this->createElement('text', 'chart_of_account_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($chart_of_account_id);
		
		$transaction_no = $this->createElement('text', 'transaction_no')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($transaction_no);
		
		$amount = $this->createElement('text', 'amount')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($amount);
		
		$entry_date = $this->createElement('text', 'entry_date')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control','datepicker'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($entry_date);
		
		$remark = $this->createElement('text', 'remark')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($remark);
		
		$type = new Zend_Form_Element_Radio('type');
			$type->setLabel('')
				//->addDecorator(array('HtmlTag', array('tag' => 'label', 'style' => 'margin-right:15px')))
				
				->addMultiOptions(array(
					'1' => 'Credit',
					'2' => 'Debit'	
				  ))
				//->setAttrib("checked", "checked")
				->setSeparator('');
				$this->addElement($type);
       
    }

}
