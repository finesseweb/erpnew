<?php

class Application_Form_Student extends Zend_Form
{
	public function init()
	{
                      
		   $batch = $this->createElement('select', 'batch')
                ->removeDecorator('label')->setAttrib('class', array('form-control','chosen'))
              ->addMultioptions(array(''=>'Select'))
                ->removeDecorator("htmlTag");
        $this->addElement($batch);
        
      $registration_no = new Application_Model_Application();
		$data = $registration_no->getDropDownList();
        	$registration = $this->createElement('select','registration_no')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen'))
							->addMultioptions(array(''=>'Select'))
                                                        ->addMultioptions($data)
							->removeDecorator('htmlTag');
				$this->addElement($registration);
        
        
            $department = new Application_Model_Department();
		$data = $department->getDropDownList();
        
        	$department = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen'))
							->addMultioptions(array(''=>'Select'))
                                                        ->addMultioptions($data)
							->removeDecorator('htmlTag');
				$this->addElement($department);
                                
                                
        		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							//->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($academic_year_id);
        
        
            $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
               // ->addMultiOptions($data1)
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
        
        
  	$status = $this->createElement('select','status')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen'))
							->addMultioptions(array('0'=>'Active','1'=>'Inactive'))
							->removeDecorator('htmlTag');
				$this->addElement($status);
        
        
	
	}
	
}