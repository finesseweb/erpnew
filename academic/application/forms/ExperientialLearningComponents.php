<?php
class Application_Form_ExperientialLearningComponents extends Zend_Form
{
    public function init()
	
    {
		
		
		
		
		$elc_name = $this->createElement('textarea','elc_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($elc_name);
		 
		
		
		$elc_desc = $this->createElement('textarea','elc_desc')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($elc_desc);
		
		
		}
	}
		?>