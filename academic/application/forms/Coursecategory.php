<?php

class Application_Form_Coursecategory extends Zend_Form
{
	public function init()
	{
		
	$cc_name = $this->createElement('text','cc_name')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							 ->setRequired(true)
							// ->setValue(0)
						    ->setAttrib('required','required')
							->removeDecorator("htmlTag");
			$this->addElement($cc_name);
			
			
			$cc_description = $this->createElement('textarea','cc_description')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setAttrib('required','required')
				->setAttrib('rows', '2')
				//->setRequired(true)
				->removeDecorator("htmlTag");
			$this->addElement($cc_description);
			
			
			
		


			
	}
	
}