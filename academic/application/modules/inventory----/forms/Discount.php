<?php

class Salesdom_Form_Discount extends Zend_Form
{

    public function init()
    {
		$search = $this->createElement('text','search')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                //->setAttrib('required','required')
                ->removeDecorator("htmlTag");
        $this->addElement($search);	
		
	 $from_date = $this->createElement('text','from_date');
		$from_date ->removeDecorator('label')
									->setAttrib('class', 'form-control from_date')
									//->setAttrib('required','required')
									->setValue(date( "Y-m-d" ))
									->removeDecorator("htmlTag");
		$to_date = $this->createElement('text','to_date');
		$to_date ->removeDecorator('label')
									->setAttrib('class', 'form-control to_date')
									//->setAttrib('required','required')
									->setValue(date( "Y-m-d" ))
									->removeDecorator("htmlTag");
		$this->addElements(array(
			  $from_date,
			  $to_date
		));	
        
    }


}
