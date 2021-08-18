<?php
/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */

class Application_Form_Report extends Zend_Form
{	
    public function init()
    {
		$search_type = $this->createElement('text','search_type')
				->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
                ->removeDecorator("htmlTag");
		 $this->addElement($search_type);
		 
		 $Country_model = new Application_Model_Country();
			$data = $Country_model->getDropDownList();
		$country_id = $this->createElement('select','country_id')
						->removeDecorator('label')
						->setAttrib('class',array('form-control'))
						->addMultioptions(array(''=>'Select'))
						->addMultioptions($data)
						->removeDecorator('htmlTag');
			$this->addElement($country_id);	
			$State_model = new Application_Model_State();
			$data = $State_model ->getDropDownList();
			$state_id = $this->createElement('select','state_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
							->addMultioptions($data)
							->removeDecorator('htmlTag');
				$this->addElement($state_id);	
			
			$City_model = new Application_Model_City();
			$data = $City_model->getDropDownList();
			$city_id = $this->createElement('select','city_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
							->addMultioptions($data)
							->removeDecorator('htmlTag');
				$this->addElement($city_id);	
				
			$Location_model = new Application_Model_Location();
			$data = $Location_model->getDropDownData();
			$location_id = $this->createElement('select','location_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
							->addMultioptions($data)
							->removeDecorator('htmlTag');
				$this->addElement($location_id);			
			$Branch_model = new Application_Model_Branch();
			$data = $Branch_model->getDropDownList();
			$branch_id = $this->createElement('multiselect','branch_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
							->addMultioptions($data)
							->removeDecorator('htmlTag');
				$this->addElement($branch_id);
			$from_date = $this->createElement('text','from_date')	
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
							->removeDecorator("htmlTag");
		 $this->addElement($from_date);
		 $to_date = $this->createElement('text','to_date')	
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
							->removeDecorator("htmlTag");
		 $this->addElement($to_date);
		 $AcademicYear_model = new Application_Model_AcademicYear();
			$data = $AcademicYear_model->getDropDownList();
			$academic_year = $this->createElement('select','academic_year')
							->removeDecorator('label')
							->setAttrib('class',array('form-control'))
							->addMultioptions(array(''=>'Select'))
							->addMultioptions($data)
							->removeDecorator('htmlTag');
				$this->addElement($academic_year);
	$Program_model = new Application_Model_Program();
	$data = $Program_model->getDropDownList();
	$program_id = $this->createElement('select','program_id')
					->removeDecorator('label')
					->setAttrib('class',array('form-control'))
					->addMultioptions(array(''=>'Select'))
					->addMultioptions($data)
					->removeDecorator('htmlTag');
		$this->addElement($program_id);		
	$Subprogram_model  = new Application_Model_SubProgram();	
		$data = $Subprogram_model->getDropDownList();	
		$subprogram_id = $this->createElement('select','subprogram_id')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data);
	    $this->addElement($subprogram_id);		
		$Frequency_model  = new Application_Model_Frequency();	
		$data = $Frequency_model->getDropDownList();	
		$frequency_id = $this->createElement('select','frequency_id')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data);
	    $this->addElement($frequency_id);	
		$Employee_model = new Application_Model_Employees();
			$data = $Employee_model->getEmployeesDropList();
			$counselor_id = $this->createElement('select','counselor_id')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data);
	    $this->addElement($counselor_id);	
		
		$WhyEsperanza_model = new Application_Model_WhyEsperanza();
			$data = $WhyEsperanza_model->getDropDownList();
			$why_esperanza = $this->createElement('select','why_esperanza')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data);
	    $this->addElement($why_esperanza);
			$how_do_you_esperanza =	new Application_Model_HowDoYouKnowEsp();
				$data = $how_do_you_esperanza->getDropDownList();
			$how_know_esperanza = $this->createElement('select','how_know_esperanza')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
				 ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($how_know_esperanza);
		$TypeofEnquiry = new Application_Model_TypeofEnquiry();
				$data = $TypeofEnquiry->getDropDownList();
			$type_of_enquiry = $this->createElement('select','type_of_enquiry')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
				 ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($type_of_enquiry);
		$SourceofEnquiry = new Application_Model_SourceofEnquiry();
		$data = $SourceofEnquiry->getDropDownList();
		$source_of_enquiry = $this->createElement('select','source_of_enquiry')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
				 ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($source_of_enquiry);
		$EnquiryMindSet_model = new Application_Model_EnquiryMindSet();
		$data = $EnquiryMindSet_model->getDropDownList();
		$enquiry_mind_set = $this->createElement('select','enquiry_mind_set')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
				 ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($enquiry_mind_set); 
		$enquiry_date = $this->createElement('text','enquiry_date')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
                ->removeDecorator("htmlTag");
        $this->addElement($enquiry_date);
		$Occupation_model = new Application_Model_Occupation();
		$data = $Occupation_model->getDropDownList();
		$occupation_id = $this->createElement('select','occupation_id')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
				 ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($occupation_id);
		$AddCompany_model = new Application_Model_AddCompany();
		$data = $AddCompany_model->getDropDownList();
			$company_id = $this->createElement('select','company_id')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
				 ->addMultiOptions(array('' => 'Select '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($company_id);
		$reference = $this->createElement('select','reference')
			   ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
				->addMultiOptions(array(''=>'Select',
								  '1'=>'Yes',
								  '2'=>'No'))
                ->removeDecorator("htmlTag");
        $this->addElement($reference);
		
		/* $search_type = $this->createElement('text','search_type')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				//->setRequired(true)
                //->setAttrib('required','required')
             ->removeDecorator("htmlTag");
        $this->addElement($search_type); */
    }
}

