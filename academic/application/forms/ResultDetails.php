<?php
class Application_Form_ResultDetails extends Zend_Form {

    public function init() {
        $Academic_model = new Application_Model_Academic();
        $data = $Academic_model->getDropDownList();
        //print_r($data); die;
        $academic_id = $this->createElement('select', 'academic_id')
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
                //->setAttrib('readonly', 'true')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
    }

}
