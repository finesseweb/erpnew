<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */

class Application_Form_StudentsList extends Zend_Form
{	
    public function init()
    {
		//Get Session Value
		$Adminactions = new Application_Model_Adminactions();	
		$auth_id = $Adminactions->auth_id();
		$role_id = $Adminactions->role_id();
		$name = $this->createElement('text','name')
				->removeDecorator('label')
					->setAttrib('class',array('form-control','chosen-select'))
					->removeDecorator("htmlTag");
			$this->addElement($name);
		$Country_model = new Application_Model_Country();
		$data = $Country_model->getDropDownList();
		$country_id = $this->createElement('multiselect','country_id')
					->removeDecorator('label')
					->setAttrib('class',array('form-control','chosen-select'))
					->removeDecorator("htmlTag")
					->addMultiOptions(array('' => 'Select'))
					->addMultiOptions($data);
			$this->addElement($country_id);	
		$State_model = new Application_Model_State();
		$data = $State_model->getDropDownList();
		$state_id = $this->createElement('multiselect','state_id')
					->removeDecorator('label')
					->setAttrib('class',array('form-control','chosen-select'))
					->removeDecorator("htmlTag")
					->addMultiOptions(array('' => 'Select'))
					->addMultiOptions($data);
			$this->addElement($state_id);	
		$City_model = new Application_Model_City();
		$data = $City_model->getDropDownList();
		$city_id = $this->createElement('multiselect','city_id')
					->removeDecorator('label')
					->setAttrib('class',array('form-control','chosen-select'))
					->removeDecorator("htmlTag")
					->addMultiOptions(array('' => 'Select'))
					->addMultiOptions($data);
			$this->addElement($city_id);
		$Location_model = new Application_Model_Location();
		$data = $Location_model->getDropDown();
		$location_id = $this->createElement('multiselect','location_id')
					->removeDecorator('label')
					->setAttrib('class',array('form-control','chosen-select'))
					->removeDecorator("htmlTag")
					->addMultiOptions(array('' => 'Select'))
					->addMultiOptions($data);
			$this->addElement($location_id);		
        $Branch_model = new Application_Model_Branch();
		if($role_id == 1){
			$data = $Branch_model->getDropDownList();	
			$branch_id = $this->createElement('multiselect','branch_id')
					->removeDecorator('label')
					->setAttrib('class',array('form-control','chosen-select'))
					//->setAttrib('required','required')
					//->setRequired(true)
					->removeDecorator("htmlTag")
					->addMultiOptions(array('' => 'Select'))
					->addMultiOptions($data);
			$this->addElement($branch_id);
		}else{
			$data = $Branch_model->getBranchDropDownList($auth_id);	
			$branch_id = $this->createElement('multiselect','branch_id')
					->removeDecorator('label')
					->setAttrib('class',array('form-control','chosen-select'))
					//->setAttrib('required','required')
					//->setRequired(true)
					->removeDecorator("htmlTag")
					//->addMultiOptions(array('' => 'Select'))
					->setAttrib('readonly','readonly')
					->addMultiOptions($data);
			$this->addElement($branch_id);
		}
		$AcademicYear_model = new Application_Model_AcademicYear();
		$data = $AcademicYear_model->getDropDownList();
		$academic_year = $this->createElement('select','academic_year')
					->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
                //->setAttrib('required','required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($academic_year);
			
		$Program_model = new Application_Model_Program();
		$data = $Program_model->getDropDownList();	
		$program_id = $this->createElement('multiselect','program_id')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
                //->setAttrib('required','required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select Program'))
                ->addMultiOptions($data);
        $this->addElement($program_id);
		
		$SubProgram_model = new Application_Model_SubProgram();
		$data = $SubProgram_model->getDropDownList();	
		$subprogram_id = $this->createElement('multiselect','subprogram_id')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
                //->setAttrib('required','required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select Subprogram'))
                ->addMultiOptions($data);
        $this->addElement($subprogram_id);
		
		$Frequency_model = new Application_Model_Frequency();
		$data = $Frequency_model->getDropDownList();	
		$frequency_id = $this->createElement('multiselect','frequency_id')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control','chosen-select'))
                //->setAttrib('required','required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select Frequency'))
                ->addMultiOptions($data);
        $this->addElement($frequency_id);
		
		
    }
}

