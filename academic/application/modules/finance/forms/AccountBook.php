<?php
class Finance_Form_AccountBook extends Zend_Form {
   
    public function init() {
		$InvoiceStatement_model = new Finance_Model_InvoiceStatement();
        $data = $InvoiceStatement_model->getUsers();
		$party_id = $this->createElement('select', 'party_id')
                 ->removeDecorator('label')
                ->setAttrib('class', array('select2'))
                //->setAttrib('required', 'required')				
                //->setRequired(true)
                ->addMultiOptions(array('' => 'Select Customer Name'))
                ->addMultiOptions($data)				
                ->removeDecorator("htmlTag");
        $this->addElement($party_id);
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
