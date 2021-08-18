<?php

class Application_Form_JobAnnouncement extends Zend_Form
{
	public function init()
	{
            
            
        $Placement_models = new Application_Model_JobAnnouncement(); 
        $placement_model = new Application_Model_placement();
       
         // $placement_modelss = new Application_Model_MasterSelectionProcess();

       $jobannouncement_id= $Placement_models->getRecords_to_jobannouncement_id();		
       $organization_name_registered= $placement_model->getRecords_to_configuration_process();

			$id = $this->createElement('text','job_announcement_id')
							->removeDecorator('label')

							->setAttrib('class',array('form-control'))
                            ->setAttrib('required','required')  
                          	// ->addMultiOptions(array(''=>'select', $jobannouncement_id['job_announcement_id'] => $jobannouncement_id['job_announcement_id'],)) 
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

          $job_title = $this->createElement('text','job_title')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($job_title);  

        	 $no_of_position_offered = $this->createElement('text','no_of_position_offered')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control')) 
                 ->setAttrib('required','required') 
                
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($no_of_position_offered);  

         $functional_area = $this->createElement('textarea','functional_area')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                  ->setAttrib('cols','3')
                 ->setAttrib('rows','2')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($functional_area);  

         $required_skill_set = $this->createElement('textarea','required_skill_set')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                  ->setAttrib('cols','3')
                 ->setAttrib('rows','2')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($required_skill_set);  
              $salary = $this->createElement('text','salary')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($salary);      
          $looking_to_hire_for = $this->createElement('select','looking_to_hire_for')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen'))
                ->addMultiOptions(array('Full-Time' => 'Full-Time',
                                        'Part-Time' => 'Part-Time'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($looking_to_hire_for);      
          $initial_place_of_position = $this->createElement('text','initial_place_of_position')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                 
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($initial_place_of_position);     
           $published_date = $this->createElement('text','published_date')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','datepicker'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($published_date);     
            

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