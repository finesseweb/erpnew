<?php

class Application_Form_FeeCategory extends Zend_Form
{
	public function init()
	{
	
		
		$category_name = $this->createElement('text','category_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($category_name);
	
	
	}
	
}