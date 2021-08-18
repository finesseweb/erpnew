<?php

class Application_Form_Room extends Zend_Form
{
	public function init()
	{
     
        	
		   $room_number = $this->createElement('text', 'room_number')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($room_number);
        
           $seating_capacity = $this->createElement('text', 'seating_capacity')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($seating_capacity);

         $seating_capacity_exam = $this->createElement('text', 'seating_capacity_exam')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($seating_capacity_exam);
       
			
			$status = $this->createElement('select','status')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select','0'=>'Active','1'=>'Inactive'))
							->removeDecorator('htmlTag');
				$this->addElement($status);
                               $department_master = new Application_Model_Department();
                               $department_lists = $department_master->getDropDownList(); 
			$status = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
							->addMultioptions($department_lists)
							->removeDecorator('htmlTag');
				$this->addElement($status);
        
	
	}
	
}