<?php



class Application_Form_StudentDashboard extends Zend_Form
{
	public function init()
	{
            
            $Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList2($_SESSION['admin_login']['admin_login']->participant_academic);
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                                                $this->addElement($academic_year_id);
                                                
            $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
        
                $section = $this->createElement('select', 'section')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($section);
        
        }
}
        
