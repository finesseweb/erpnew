<?php

class Application_Form_Placement extends Zend_Form
{
	public function init()
	{
            
          //  $country_json = file_get_contents($this->mainconfig['erp'].'/data/data_json/countryObject.json');
                    
                    
                  //  echo '<pre>'; print_r($zendConfig);
                    
                $Placement_model = new Application_Model_Placement();
		$data = $Placement_model->getDropDownStateList();
		$data_country = $Placement_model->getDropDownCountryList();
		$data_cities = $Placement_model->getDropDownCityList();
		// print_r($data); die;
		
			$id = $this->createElement('text','registration_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
                            ->setAttrib('required','required')
                            // ->setAttrib('value','lokesh')
                            ->setAttrib('readonly',true ) 


							// ->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($id);
		$address = $this->createElement('textarea','address')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
                            ->setAttrib('required','required')
                            ->setAttrib('cols', '3')
                             ->setAttrib('rows', '2')
							
							// ->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($address);
		
		 $organization_name = $this->createElement('text','organization_name')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($organization_name);
         
		
		$city = $this->createElement('select','city')
                ->removeDecorator('label')->setAttrib('class',array('form-control','chosen'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag")
                // ->addMultiOptions(array('' => 'select'))
              ->addMultiOptions($data_cities);
        $this->addElement($city);
		
		 $state = $this->createElement('select','state')
                ->removeDecorator('label')->setAttrib('class',array('form-control','chosen'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag")
                  // ->addMultiOptions(array('' => 'select'))
                ->addMultiOptions($data);
        $this->addElement($state); 
		
		$country = $this->createElement('select','country')
                ->removeDecorator('label')->setAttrib('class',array('form-control','chosen'))
                 ->setAttrib('required','required')
                ->setRequired(true) 
                ->removeDecorator("htmlTag")
                  ->addMultiOptions($data_country);
        $this->addElement($country);

        $contact_person = $this->createElement('text','contact_person')

                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");

        $this->addElement($contact_person);

        $designation = $this->createElement('text','designation')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                ->setRequired(true)
                ->removeDecorator("htmlTag");

        $this->addElement($designation);

        $contact_no = $this->createElement('text','contact_no')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                 // ->setAttrib('maxlength','10')
                 // ->setAttrib('minlength','10')                   
                 // ->setAttrib('number','10')                   
                ->setRequired(true)
                ->removeDecorator("htmlTag");

        $this->addElement($contact_no);

        $email_id = $this->createElement('text','email_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                 ->setAttrib('required','required')
                 // ->setAttrib("type", "email")
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");

        $this->addElement($email_id);  

         $password = $this->createElement('password','password')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                
                ->setRequired(true)
                ->removeDecorator("htmlTag");

        $this->addElement($password); 

         $confirm_password = $this->createElement('password','confirm_password')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                
                ->setRequired(true)
                ->removeDecorator("htmlTag");

        $this->addElement($confirm_password); 
//         $this->addElement('password', 'password', array(
//         'label'      => 'Password:',
//         'required'   => true,
//         'validators' => array(
//             'Identical' => array(What do I put here?)
//         )
//     ));
// $this->addElement('password', 'verifypassword', array(
//         'label'      => 'Verify Password:',
//         'required'   => true,
//         'validators' => array(
//             'Identical' => array(What do I put here?)
//         )
//     ));

         	$status = $this->createElement('select','status')
					->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen'))
							 ->setAttrib('required','required')
						   ->setAttrib('required','required')
							->addMultiOptions(array('Active' => 'Active',
                                                     'Inactive' => 'Inactive'))
							->removeDecorator("htmlTag");
		$this->addElement($status);
		
		// $Academic_model = new Application_Model_Academic();
		// $data = $Academic_model->getIncrementID();
	//	print_r($data);die;
		// $short_code = $this->createElement('text','short_code')
  //               ->removeDecorator('label')->setAttrib('class',array('form-control'))
		// 		->removeDecorator('label')->setAttrib('data-toggle',array('tooltip'))
		// 		->setAttrib('data-placement',array('bottom'))
		// 		->removeDecorator('label')->setAttrib('title',array('PDM-XXXX-YYYY'))
		// 		->setRequired(true)
		// 		//->setAttrib('readonly','readonly')
				
  //               ->removeDecorator("htmlTag");
  //       $this->addElement($short_code);
		
		
	
	}
	
}