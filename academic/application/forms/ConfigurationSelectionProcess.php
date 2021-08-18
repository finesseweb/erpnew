<?php

class Application_Form_ConfigurationSelectionProcess extends Zend_Form
{
	public function init()
	{
            
            
        $Placement_models = new Application_Model_Configureselectionprocess(); 
         $placement_model = new Application_Model_placement();
         $placement_modelss = new Application_Model_MasterSelectionProcess();

       $selection_id= $placement_modelss->getRecords_to_configuration_process();		
       $organization_name_registered= $placement_model->getRecords_to_configuration_process();

			$id = $this->createElement('select','selection_id')
							->removeDecorator('label')

							->setAttrib('class',array('form-control','chosen'))
                            ->setAttrib('required','required') 
                            
                          	// ->addMultiOptions(array(''=>'select',$selection_id=>'selection_id'))
                             ->addMultiOptions(array('' => 'Select',
													$selection_id['selection_id'] => $selection_id['selection_id'],
													  ))
                                                      
							->removeDecorator("htmlTag");
                $this->addElement($id); 
                
		 $organization_name = $this->createElement('select','organization_name')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen'))
                 ->setAttrib('required','required')                   
                  ->addMultiOptions(array('' => 'Select',
													$organization_name_registered['organization_name'] => $organization_name_registered['organization_name'],
													  ))
                  
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($organization_name);

          $selection_process = $this->createElement('text','selection_process')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($selection_process);  

        	 $description = $this->createElement('textarea','description')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                 ->setAttrib('cols','3')
                 ->setAttrib('rows','2')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($description);  

         $from_date = $this->createElement('text','from_date')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','datepicker'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($from_date);  

         $to_date = $this->createElement('text','to_date')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','datepicker'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($to_date);  

         	$status = $this->createElement('select','status')
					->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen'))
							 ->setAttrib('required','required')
						   ->setAttrib('required','required')
							->addMultiOptions(array('Active' => 'Active',
													 'Inactive' => 'Inactive'))
							->removeDecorator("htmlTag");
		$this->addElement($status); 
		
	
	}
	
}