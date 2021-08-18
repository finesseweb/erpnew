<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Form_Section extends Zend_Form {

    public function init() {
        $Academic_model = new Application_Model_Academic();
        $data = $Academic_model->getDropDownList();
        //print_r($data); die;
        $academic_id = $this->createElement('select', 'academic_year_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($academic_id);

        /* 	$year = $this->createElement('select','year')
          ->removeDecorator('label')
          ->setAttrib('class',array('form-control','chosen-select'))
          ->setAttrib('required','required')
          ->removeDecorator("htmlTag")
          ->addMultiOptions(array('' => 'Select',
          '1'=>'First Year',
          '2'=>'Second Year'));
          $this->addElement($year); */

        $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
        
        
    $section_name = $this->createElement('text','name')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                         ->setAttrib('required','required')
							->removeDecorator("htmlTag");
                                                        $this->addElement($section_name);
        
        $status = $this->createElement('select', 'status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select',0=>'Active',1=>'Deactive'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($status);
    }

}

