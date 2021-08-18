<?php

class Application_Form_Index extends Zend_Form
{
	public function init()
	{
            
            $Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList1();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							->addMultiOptions(array('' => '--Select--'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                                                $this->addElement($academic_year_id);
                                                
            $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => '--Select--'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
        
        
        
         $top_id = $this->createElement('select', 'top')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('1'=>'Top 1' ,'2' => 'Top 2','3'=>'Top 3','4'=>'Top 4','5' => 'Top 5'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($top_id);
        
               $course = $this->createElement('select', 'course')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(''=>'Select'));
        $this->addElement($course);  
        
        
         $instructor = $this->createElement('select', 'instructor')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                //->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(''=>'Select'));
        $this->addElement($instructor);   
            
        }
        
        
}
