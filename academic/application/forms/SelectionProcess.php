<?php

class Application_Form_SelectionProcess extends Zend_Form
{
	public function init()
	{
            
            
        $Placement_model = new Application_Model_MasterSelectionProcess(); 
		
			$id = $this->createElement('text','selection_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
                            ->setAttrib('required','required') 
							->removeDecorator("htmlTag");
                $this->addElement($id); 
                
		 $selection_process = $this->createElement('text','selection_process')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($selection_process);  

         	$status = $this->createElement('select','status')
					->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen'))
							 ->setAttrib('required','required')
						   ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select',
													'Active' => 'Active',
													 'Inactive' => 'Inactive'))
							->removeDecorator("htmlTag");
		$this->addElement($status); 
		
	
	}
	
}