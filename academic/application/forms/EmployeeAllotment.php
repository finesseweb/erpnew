<?php
class Application_Form_EmployeeAllotment extends Zend_Form
{
    public function init()
	
    {
		
		
		
		/*$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getEmployeeIds();
		$ea_name = $this->createElement('select','ea_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
              ->setAttrib('required','required')
			  ->addMultiOptions(array(''=>'Select'))
			  ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($ea_name);
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDesiggroupDropDownList();
		$desig_name = $this->createElement('select','desig_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
              ->setAttrib('required','required')
			  ->addMultiOptions(array(''=>'Select'))
			  ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($desig_name);
		
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDesignationDropDownList();
		$designation_name = $this->createElement('select','designation_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
              ->setAttrib('required','required')
			  ->addMultiOptions(array(''=>'Select'))
			  ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($designation_name);
		
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDepartments();
		//print_r($data);die;
		$department_name = $this->createElement('select','department_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
              ->setAttrib('required','required')
			  ->addMultiOptions(array(''=>'Select'))
			  ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($department_name);
		
		 */
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id);
        
        
		//print_r($data); die;
		$term_id = $this->createElement('select','term_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
                                                        ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
		
		
		
		
		}
	}
		?>