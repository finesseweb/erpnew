<?php
class Application_Form_ElectiveSelection extends Zend_Form
{
    public function init()
	
    {
		
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
		
		$StudentPortal_model = new Application_Model_StudentPortal();
		$data = $StudentPortal_model->getDropDownList();
		$student_id = $this->createElement('select','student_id')
		              ->removeDecorator('label')
					  ->setAttrib('class',array('form-control','chosen-select'))
					  ->setAttrib('required','required')
					  ->removeDecorator("htmlTag")
					  ->addMultiOptions(array(''=>'Select'))
					  ->addMultiOptions($data);
		$this->addElement($student_id);		
        
        $TermMaster_model = new Application_Model_TermMaster();
		$data = $TermMaster_model->getSecondYearTerms();
         $term_id = $this->createElement('select','term_id')
                    ->removeDecorator('label')
                     ->setAttrib('class',array('form-control','chosen-select'))
                     ->setAttrib('required','required')
                     ->removeDecorator("htmlTag")
                     ->addMultiOptions(array(''=>'Select'))
                      ->addMultiOptions($data);
        $this->addElement($term_id);					  
		
	}
	}
		?>