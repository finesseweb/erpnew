<?php

class Application_Form_Department extends Zend_Form {

    public function init() {


        $department = $this->createElement('text', 'department')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($department);

        $status = $this->createElement('select', 'status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->addMultioptions(array('' => 'Select', '0' => 'Active', '1' => 'Inactive'))
                ->removeDecorator('htmlTag');
        $this->addElement($status);
    }

}
