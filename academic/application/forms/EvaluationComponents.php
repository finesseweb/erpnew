<?php

class Application_Form_EvaluationComponents extends Zend_Form
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
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($academic_year_id);
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDepartments();
		$department_id = $this->createElement('select','department_id')
		                  ->removeDecorator('label')
						  ->setAttrib('class',array('form-control'))
						  ->setAttrib('required','required')
						  ->addMultiOptions(array(''=>'Select'))
						  ->addMultiOptions($data)
						   ->removeDecorator("htmlTag");
		$this->addElement($department_id);

		$HRMModel_model = new Application_Model_HRMModel();
        $data = $HRMModel_model->getEmployeeIds();
        $employee_id = $this->createElement('select','employee_id')
						->removeDecorator('label')
						->setAttrib('class',array('form-control'))
						->setAttrib('required','required')
						->addMultiOptions(array(''=>'Select'))
						->addMultiOptions($data)
						->removeDecorator("htmlTag");
			$this->addElement($employee_id);			
		
		/* $Term_model = new Application_Model_TermMaster();
		$data = $Term_model->getDropDownList();
		//print_r($data); die;
		$term_id = $this->createElement('select','term_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($term_id);
		
		
		$course_model = new Application_Model_Course();
		$data = $course_model->getDropDownList();
		//print_r($data); die;
		$course_id = $this->createElement('select','course_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($course_id);
		
		
		$course_model = new Application_Model_Course();
		$data = $course_model->getDropDownList();
		//print_r($data); die;
		$course_id = $this->createElement('select','course_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($course_id);
		
		$Coursecategory_model = new Application_Model_Coursecategory();
		$data = $Coursecategory_model->getDropDownList();
		//print_r($data); die;
		$cc_id = $this->createElement('select','cc_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($cc_id); */
		
		
			
	}
	
}