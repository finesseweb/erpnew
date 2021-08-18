<?php

class Application_Form_FeeHeads extends Zend_Form
{
	public function init()
	{
	    $FeeCategory_model = new Application_Model_FeeCategory();
		$data = $FeeCategory_model->getDropDownList();
		//print_r($data); die;
		$feecategory_id = $this->createElement('select','feecategory_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($feecategory_id);
		
		$feehead_name = $this->createElement('text','feehead_name')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag");
        $this->addElement($feehead_name);
		
			
	}
	
}