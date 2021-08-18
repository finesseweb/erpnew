<?php
class Application_Form_ExperientialAllotment extends Zend_Form
{
    public function init()
	
    {
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$batch_id = $this->createElement('select','batch_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($batch_id);
		
		}
	}
?>