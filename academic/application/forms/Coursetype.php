<?php

class Application_Form_Coursetype extends Zend_Form
{
	public function init()
	{
		
	$ct_name = $this->createElement('text','ct_name')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							 ->setRequired(true)
							// ->setValue(0)
						    ->setAttrib('required','required')
							->removeDecorator("htmlTag");
			$this->addElement($ct_name);
			
			
			$ct_description = $this->createElement('textarea','ct_description')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setAttrib('required','required')
				->setAttrib('rows', '2')
				//->setRequired(true)
				->removeDecorator("htmlTag");
			$this->addElement($ct_description);
			
			
			
			
		


			
	}
	
}