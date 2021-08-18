<?php

class Application_Form_ExperientialLearning extends Zend_Form {

    public function init() {


        $Academic_model = new Application_Model_Academic();
        $data = $Academic_model->getDropDownList();
        //print_r($data); die;
        $academic_year_id = $this->createElement('select', 'academic_year_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($academic_year_id);
        
        
        //========================[--CREDIT VALUE DROPDOWN--]===========//
        $CreditMaster_model = new Application_Model_CreditMaster();
       // $data = $CreditMaster_model->getExperientialCreditDropDownList();
        //print_r($data); die;
        $credit_id = $this->createElement('select', 'credit_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'));
               // ->addMultiOptions($data);
        $this->addElement($credit_id);

        //===================[--CREDIT NAME DROPDOWN--]===================//

        $data = $CreditMaster_model->getExperientialCreditNameDropDownList();
        //print_r($data); die;
        $credit_nsme = $this->createElement('select', 'credit_name')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($credit_nsme);


        $ExperientialLearningComponents_model = new Application_Model_ExperientialLearningComponents();
        $data = $ExperientialLearningComponents_model->getDropDownList();
        //print_r($data); die;
        $elc_id = $this->createElement('select', 'elc_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($elc_id);

        $year_id = $this->createElement('select', 'year_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired('required', 'required')
                ->addMultiOptions(array('' => 'Select',
                    '1' => 'First Year',
                    '2' => 'Second Year'))
                ->removeDecorator("htmlTag");
        $this->addElement($year_id);


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



        $TermMaster_model = new Application_Model_TermMaster();
        $data = $TermMaster_model->getDropDownList();
        //print_r($data); die;
        $terms_id = $this->createElement('select', 'terms_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                // ->setAttrib('required','required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($terms_id);
    }

}

?>