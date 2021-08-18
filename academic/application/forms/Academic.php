<?php

class Application_Form_Academic extends Zend_Form
{
	public function init()
	{
            
            
            $Department_model = new Application_Model_Department();
		$data = $Department_model->getDropDownList();
		//print_r($data); die;
		$department_id = $this->createElement('select','department')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
                $this->addElement($department_id);
		
		 $from_date = $this->createElement('text','from_date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($from_date);
        
        
        
        
		
		$to_date = $this->createElement('text','to_date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($to_date);
		
		/* $tot_no_of_credits = $this->createElement('text','tot_no_of_credits')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($tot_no_of_credits); */
		
		$batch_code = $this->createElement('text','batch_code')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($batch_code);
		
		
		// $Academic_model = new Application_Model_Academic();
		// $data = $Academic_model->getIncrementID();
	//	print_r($data);die;
		$short_code = $this->createElement('text','short_code')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
				->removeDecorator('label')->setAttrib('data-toggle',array('tooltip'))
				->setAttrib('data-placement',array('bottom'))
				->removeDecorator('label')->setAttrib('title',array('PDM-XXXX-YYYY'))
				->setRequired(true)
				//->setAttrib('readonly','readonly')
				
                ->removeDecorator("htmlTag");
        $this->addElement($short_code);
		
		
	
	}
	
}