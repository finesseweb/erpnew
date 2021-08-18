<?php

class Application_Form_Routine extends Zend_Form
{
    
	public function init()
	{
            $Academic_model = new Application_Model_HRMModel();
		$data = $Academic_model->getEmployeeIds();
		//print_r($data); die;
		$faculty = $this->createElement('select','faculty')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							// ->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
							$this->addElement($faculty);
                                                        
                                                        
          $department_master = new Application_Model_Department();
          $department_lists = $department_master->getDropDownList(); 
			$department = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							// ->addMultioptions(array(''=>'Select'))
							->addMultioptions($department_lists)
							->removeDecorator('htmlTag');
				$this->addElement($department);
                                
                                
                               
			$room = $this->createElement('select','room_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							// ->addMultioptions(array(''=>'Select'))
							->removeDecorator('htmlTag');
				$this->addElement($room);
                                
                                
       $Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							// ->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($academic_year_id);
        
                    $term_id = $this->createElement('select', 'term_id')
                            ->removeDecorator('label')
                            ->setAttrib('class', array('form-control', 'chosen-select'))
                            ->setAttrib('required', 'required')
                            ->removeDecorator("htmlTag")
                            // ->addMultiOptions(array('' => 'Select'))
                            ->setRegisterInArrayValidator(false);
                    $this->addElement($term_id);
        
 

            $course_id = $this->createElement('select', 'course_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
               // ->setAttrib('style',array('display:none')) 
                       ->setAttrib('required', 'required') 
                // ->addMultiOptions(array('' => 'Select'))
               // ->addMultiOptions($data_course)
               ->removeDecorator("htmlTag"); 
        $this->addElement($course_id);	
            
	}

        
}