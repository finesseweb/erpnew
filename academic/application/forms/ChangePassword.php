<?php


class Application_Form_ChangePassword extends Zend_Form
{
	public function init()
	{
		
		 
		
		$to_date = $this->createElement('text','current_password')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($to_date);
		
		/* $tot_no_of_credits = $this->createElement('text','tot_no_of_credits')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($tot_no_of_credits); */
		
		$batch_code = $this->createElement('text','new_password')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($batch_code);
		
		
		// $Academic_model = new Application_Model_Academic();
		// $data = $Academic_model->getIncrementID();
	//	print_r($data);die;
		$confirm_code = $this->createElement('text','confirm_pword')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($confirm_code);
		
		
	
	}
	
}

