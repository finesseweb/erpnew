<?php

class Application_Form_TermMaster extends Zend_Form {

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
        
        
        
     $declaredTerms =  new Application_Model_Declaredterms();
        $data = $declaredTerms->getDropDownList();
           $term = $this->createElement('select', 'cmn_terms')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                   ->addMultiOptions($data);
                   $this->addElement($term);
        
        
        $term_name = $this->createElement('textarea', 'term_name')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($term_name);

        $start_date = $this->createElement('text', 'start_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control', 'datepicker'))
                ->setRequired(true)
                //->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($start_date);


        $end_date = $this->createElement('text', 'end_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control', 'datepicker'))
                ->setRequired(true)
                //->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($end_date);

        $term_description = $this->createElement('textarea', 'term_description')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($term_description);


        $year_id = $this->createElement('select', 'year_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired('required', 'required')
                ->addMultiOptions(array('' => 'Select',
                    '1' => 'First Year',
                    '2' => 'Second Year'))
                ->removeDecorator("htmlTag");
        $this->addElement($year_id);

        $tot_no_of_credits = $this->createElement('text', 'tot_no_of_credits')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($tot_no_of_credits);
    }

}
