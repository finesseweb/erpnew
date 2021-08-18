<?php

class Finance_Form_Statement extends Zend_Form {

    protected $_name = 'erp_finance_dealer_payouts';
    protected $_id = 'cp_id';

    public function init() {
	
		$Statement_model = new Finance_Model_Statement();
        $data = $Statement_model->getUsers();
		$party_id = $this->createElement('select', 'party_id')
                 ->removeDecorator('label')
                ->setAttrib('class', array('select2'))
                //->setAttrib('required', 'required')				
                //->setRequired(true)
                ->addMultiOptions(array('' => 'Select Party Name'))
                ->addMultiOptions($data)				
                ->removeDecorator("htmlTag");
        $this->addElement($party_id);
		
		/* $ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
        $data = $ErpDealerMaster_model->getDropDownList();
		$dealer_id = $this->createElement('select', 'dealer_id')
                 ->removeDecorator('label')
                ->setAttrib('class', array('select2'))
                //->setAttrib('required', 'required')				
                //->setRequired(true)
                ->addMultiOptions(array('' => 'Select Customer'))
                ->addMultiOptions($data)				
                ->removeDecorator("htmlTag");
        $this->addElement($dealer_id); */
		
        $start_date = $this->createElement('text', 'start_date')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
               // ->setRequired(true)               
                ->removeDecorator("htmlTag");
        $this->addElement($start_date);

        $end_date = $this->createElement('text', 'end_date')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
               // ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($end_date);
		
		$type = $this->createElement('select', 'type')
                 ->removeDecorator('label')
                ->setAttrib('class', array('select2'))
                //->setAttrib('required', 'required')				
                //->setRequired(true)
				->addMultiOptions(array('' => 'Select Payment Type'))
                ->addMultiOptions(array('4' => 'Sales'))
				->addMultiOptions(array('5' => 'Purchase'))               			
                ->removeDecorator("htmlTag");
        $this->addElement($type);
		
    }

}
