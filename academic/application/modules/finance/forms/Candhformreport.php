<?php

class Finance_Form_Candhformreport extends Zend_Form {

    public function init() {
	
		$current_year_date = date('Y').'-04-01';
        $start_date = $this->createElement('text', 'start_date',array('value' => $current_year_date))
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->removeDecorator("htmlTag");
        $this->addElement($start_date); 
		
		$end_year_date = (date('Y')+1).'-03-31';
        $end_date = $this->createElement('text', 'end_date',array('value' => $end_year_date))
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->removeDecorator("htmlTag");
        $this->addElement($end_date);		
    }

}
