<?php

class Application_Form_ScholarStructure extends Zend_Form {

    public function init() {
        $FeeCategory_model = new Application_Model_ScholarStructure();
        $data = $FeeCategory_model->getDropDownList();
        //print_r($data); die;
        $feecategory_id = $this->createElement('select', 'batch_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($feecategory_id);

    /*    $data1 = $FeeCategory_model->getDropDownListOfTerms();
        $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data1)
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);*/




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

        $Scholarship_eligibility = $this->createElement('text', 'scholarship_fee_wavier')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                 ->setAttrib('maxlength', '4')
                ->removeDecorator("htmlTag");
        $this->addElement($Scholarship_eligibility);
    }

}
