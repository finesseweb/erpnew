<?php
class Application_Form_ElectiveCourseLearning extends Zend_Form
{
    public function init()
	
    {
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id);
		
		$term_model = new Application_Model_TermMaster();
		$data = $term_model->getDropDownList();
		$term_id = $this->createElement('select','term_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($term_id);
		
		$coursecategory_model = new Application_Model_Coursecategory();
		$data = $coursecategory_model->getDropDownList();
		$course_category_id = $this->createElement('select','course_category_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
							->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array(''=>'Select'))
							->addMultiOptions($data);
			$this->addElement($course_category_id);
        $Course_model = new Application_Model_Course();
		$data = $Course_model->getElectiveDropDownList();
		$course_id = $this->createElement('select','course_id')
						->removeDecorator('label')
						->setAttrib('class',array('form-control','chosen-select'))
						->setAttrib('required','required')
						->removeDecorator("htmlTag")
						->addMultiOptions(array(''=>'Select'))
						->addMultiOptions($data);
			$this->addElement($course_id);			
			
		$CreditMaster_model = new Application_Model_CreditMaster();
			$data = $CreditMaster_model->getCourseCreditDropDownList();
			$credit_id = $this->createElement('select','credit_id')
			             ->removeDecorator('label')
						 ->setAttrib('class',array('form-control','chosen-select'))
						 ->setAttrib('required','required')
						 ->removeDecorator("htmlTag")
						 ->addMultiOptions(array(''=>'Select'))
						 ->addMultiOptions($data);
				$this->addElement($credit_id);	  
		
		}
	}
		?>