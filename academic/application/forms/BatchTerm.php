<?php
class Application_Form_BatchTerm extends Zend_Form
{
	public function init()
	{
            
        $Academic_model = new Application_Model_BatchSchedule();
		$data = $Academic_model->getDropDownList();
        //$data1 = $Academic_model->getDropDownList1();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                            ->setAttrib('required','required')
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                       $this->addElement($academic_year_id);
                                                
            $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => '--Select--'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
        }
        
}