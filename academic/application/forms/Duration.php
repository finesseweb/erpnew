<?php

class Application_Form_Duration extends Zend_Form {

    public function init() {

        $duration = $this->createElement('text', 'duration_from')
                ->removeDecorator('label')->setAttrib('class', array('form-control', 'datepicker'))
                ->setAttrib('placeholder', '')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($duration);

        $duration = $this->createElement('text', 'duration_to')
                ->removeDecorator('label')->setAttrib('class', array('form-control', 'datepicker'))
                ->setAttrib('placeholder', '')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($duration);


        $status = $this->createElement('select', 'status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->addMultioptions(array( '0' => 'Active', '1' => 'Inactive'))
                ->removeDecorator('htmlTag');
        $this->addElement($status);
    }

}
