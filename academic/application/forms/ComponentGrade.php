<?php
class Application_Form_ComponentGrade extends Zend_Form
{
    public function init()
	
    {
		//Batch
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
		//Department 
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getDepartments();
        $department_id = $this->createElement('select','department_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->addMultiOptions(array(''=>'Select'))
				->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($department_id);
		
		//Faculty
		$HRMModel_model = new Application_Model_HRMModel();
		$data = $HRMModel_model->getEmployeeIds();
		$employee_id = $this->createElement('select','employee_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->addMultiOptions(array(''=>'Select'))
				->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($employee_id);
		 
		}
	}
		?>