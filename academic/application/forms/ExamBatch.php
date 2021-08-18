<?php

class Application_Form_ExamBatch extends Zend_Form
{
	public function init()
	{

        	
		   $batch = $this->createElement('text', 'batch')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($batch);
        
        
            $department = new Application_Model_Department();
		$data = $department->getDropDownList();
        
        	$department = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
                                                        ->addMultioptions($data)
							->removeDecorator('htmlTag');
				$this->addElement($department);
        
        
        
  	$status = $this->createElement('select','status')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array('0'=>'Active','1'=>'Inactive'))
							->removeDecorator('htmlTag');
				$this->addElement($status);
        
        
	
	}
	
}