<?php

class Application_Form_ReferenceGradeMaster extends Zend_Form
{
	public function init()
	{
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		 $academic_year_id = $this->createElement('select','academic_year_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
				->setAttrib('required','required')
                ->setRequired(true)
				->addMultioptions(array(""=>"Select"))
				->addMultioptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($academic_year_id);
	
	}
	
}