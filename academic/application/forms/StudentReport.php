<?php
class Application_Form_StudentReport extends Zend_Form
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
		
		$year_id = $this->createElement('select','year_id')
					    ->removeDecorator('label')
						->setAttrib('class',array('form-control','chosen-select'))
						->setAttrib('required','required')
						->addMultiOptions(array('' => 'Select',
												'1' => 'First Year',
												'2' => 'Second Year'))
						->removeDecorator("htmlTag");
		$this->addElement($year_id);
	
	    $StudentPortal_model = new Application_Model_StudentPortal();
		$data = $StudentPortal_model->getDropDownList();
		//print_r($data); die;
		$stu_id = $this->createElement('select','stu_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						    //->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($stu_id);
		
		

		
		}
	}
		?>