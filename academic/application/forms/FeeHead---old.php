<?php

class Application_Form_FeeHead extends Zend_Form
{
	public function init()
	{
		
	$fee_head_name = $this->createElement('text','fee_head_name')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							 ->setRequired(true)
							// ->setValue(0)
						    ->setAttrib('required','required')
							->removeDecorator("htmlTag");
			$this->addElement($fee_head_name);
			
			
			$description = $this->createElement('textarea','description')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setAttrib('required','required')
				->setAttrib('rows', '2')
				//->setRequired(true)
             ->removeDecorator("htmlTag");
        $this->addElement($description);
			
			
			$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_id = $this->createElement('select','academic_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_id);
			
		


		$FeeHeadType_model = new Application_Model_FeeHeadType();
		$data = $FeeHeadType_model->getDropDownList();
		//print_r($data); die;
		$fee_head_type_id = $this->createElement('select','fee_head_type_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($fee_head_type_id);		
	}
	
}