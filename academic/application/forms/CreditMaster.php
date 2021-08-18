<?php

class Application_Form_CreditMaster extends Zend_Form
{
	public function init()
	{
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		
		$credit_value = $this->createElement('text','credit_value')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				//->setAttrib('readonly', 'readonly')
				->removeDecorator("htmlTag");
        $this->addElement($credit_value);
		
		$credit_name = $this->createElement('text','credit_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				//->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($credit_name);
		
		
		$credit_type = $this->createElement('select','credit_type')
							->removeDecorator('label')->setAttrib('class',array('form-control'))
							->addMultiOptions(array('0'=>'Select',
													'1'=>'Core Course',
													'2'=>'Experiential Learning'
													))
							->removeDecorator("htmlTag");
		$this->addElement($credit_type);
		
		
		$credit_desc = $this->createElement('textarea','credit_desc')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($credit_desc);
	
	
	}
	
}