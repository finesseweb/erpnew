<?php

class Application_Form_ResultStructure extends Zend_Form {

    public function init() {
        // $FeeCategory_model = new Application_Model_ResultStructure();
        // $data = $FeeCategory_model->getDropDownList();
        $GPA_range_from = $this->createElement('text', 'gpa_from')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($GPA_range_from);

        $GPA_range_to = $this->createElement('text', 'gpa_to')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($GPA_range_to);

        $Scholarship_eligibility = $this->createElement('text', 'result')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($Scholarship_eligibility);
    }

}
