<?php

class Application_Form_Credit extends Zend_Form
{
	public function init()
	{
		
		$credit = $this->createElement('text','credit')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
				->setRequired(true)
				->setAttrib('required','required')
                ->removeDecorator("htmlTag");
        $this->addElement($credit);
		
	
		
		
	
	}
	
}