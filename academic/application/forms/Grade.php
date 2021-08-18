<?php
class Application_Form_Grade extends Zend_Form
{
    public function init()
	
    {
	
		
        $letter = $this->createElement('text','letter')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($letter);
		
		
		 $number = $this->createElement('text','number')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($number);
		}
	}
		?>