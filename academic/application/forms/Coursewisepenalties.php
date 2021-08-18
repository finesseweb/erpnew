<?php
class Application_Form_Coursewisepenalties extends Zend_Form
{
    public function init()
	
    {
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_id = $this->createElement('select','academic_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($academic_id);
		
		$year_id = $this->createElement('select','year_id')
					->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select',
													'1' => 'First Year',
													 '2' => 'Second Year'))
							->removeDecorator("htmlTag");
		$this->addElement($year_id);					
		
       
		
		}
	}
		?>