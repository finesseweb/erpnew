<?php
class Application_Form_StudentFeedElement extends Zend_Form
{
	public function init(){
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
