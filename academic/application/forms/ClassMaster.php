<?php

class Application_Form_ClassMaster extends Zend_Form
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
                                                        
                                                        
                                                        
                                                        
	
		//print_r($data); die;
		$class_name = $this->createElement('text','class_name')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                         ->setAttrib('required','required')
							->removeDecorator("htmlTag");
                                                        $this->addElement($class_name);
		$class_hours = $this->createElement('text','hours')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                         ->setAttrib('required','required')
                                                         ->setAttrib('pattern','^\d{1,2}')
                                                         ->setAttrib('title','XX minutes') 
							->removeDecorator("htmlTag");
                                                        $this->addElement($class_hours);
                                                        
                                                        
		$time = $this->createElement('text','time')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select','timepicker'))
                                                         ->setAttrib('required','required')
                                                        // ->setAttrib('pattern','^\d{1,2}')
                                                         ->setAttrib('title','XX minutes') 
							->removeDecorator("htmlTag");
                                                        $this->addElement($time);
                                                        
                                                        
                                                        		$status = $this->createElement('select','status')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select',0 => 'Activate', 2=> 'Deactivate'));
                                                        $this->addElement($status);
		
}
	
}