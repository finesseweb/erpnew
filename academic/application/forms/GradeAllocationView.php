<?php

class Application_Form_GradeAllocationView extends Zend_Form
{
	public function init()
	{
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id);
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getEmployeeIds();
		//print_r($data); die;
		$empl_id = $this->createElement('select','empl_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($empl_id);
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDepartmentsDropdown();
		//print_r($data); die;
		$id = $this->createElement('select','id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($id);
	
	}
	
}