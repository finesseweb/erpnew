<?php

class Application_Form_ProgramDesignView extends Zend_Form
{
	public function init()
	{
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownListShortCode();
	//print_r($data); die;
		$short_id = $this->createElement('select','short_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($short_id);
		
		
		$ProgramMaster_model = new Application_Model_ProgramMaster();
		$data = $ProgramMaster_model->getDropDownListSessionName();
		//print_r($data);die;
		$pm_name = $this->createElement('select','pm_name')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag")
				->addMultiOptions(array('' => 'Select'))
				->addMultiOptions($data);
        $this->addElement($pm_name);
	
	}
	
}