<?php

class Application_Form_Corecourselearning extends Zend_Form
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
		
		$Term_model = new Application_Model_TermMaster();
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
							->setAttrib('class',array('form-control','chosen'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($course_id);
		
		
		/* $course_model = new Application_Model_Course();
		$data = $course_model->getCoreDropDownList();
		//print_r($data); die;
		$course_id = $this->createElement('select','course_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($course_id); */
		
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
        $this->addElement($cc_id);
		
		$CreditMaster_model = new Application_Model_CreditMaster();
		$data = $CreditMaster_model->getCourseCreditDropDownList();
		//print_r($data); die;
		$credit_id = $this->createElement('select','credit_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($credit_id);
		
}
	
}