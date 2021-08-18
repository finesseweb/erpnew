<?php

class Finance_Form_PettyCashMaster extends Zend_Form {   

    public function init() {
        
        $chart_of_account_id = $this->createElement('text', 'chart_of_account_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($chart_of_account_id);
		
		$opening_balance = $this->createElement('text', 'opening_balance')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($opening_balance);
		
		$entry_date = $this->createElement('text', 'entry_date')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control','datepicker'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($entry_date);
		
		
       
    }

}
