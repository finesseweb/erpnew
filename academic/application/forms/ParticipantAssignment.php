<?php
class Application_Form_ParticipantAssignment extends Zend_Form
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
        
        $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
        
        
        
         $course_id = $this->createElement('select', 'course_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
               // ->setAttrib('style',array('display:none')) 
                       ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                //->addMultiOptions($data1)
               ->setRegisterInArrayValidator(false);
        $this->addElement($course_id);	
        
        
        
            
        }
        
 }
