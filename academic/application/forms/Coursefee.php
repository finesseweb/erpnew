<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Application_Form_Coursefee extends Zend_Form
{
	public function init()
	{

	$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','batch')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
                                                        // ->setAttrib('disabled', 'disabled')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($academic_year_id);
         
        $term_id = $this->createElement('select', 'term')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
			
		
		$fee = $this->createElement('text','fee')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($fee);
        $status = $this->createElement('select', 'paper')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('1' => 'Current Papers','2'=>'Back Papers'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($status);
        }
}