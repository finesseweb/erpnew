<?php

class Application_Form_ExperientialLearningProjectAllocation extends Zend_Form {

    public function init() {
        $Academic_model = new Application_Model_Academic();
        $data = $Academic_model->getDropDownList();
        //print_r($data); die;
        $batch_id = $this->createElement('select', 'batch_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($batch_id); 
        
        $year_id = $this->createElement('select', 'year_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select',
                                            '1' => 'First Year',
                                            '2' => 'Second Year'));
        $this->addElement($year_id);
        $el_component_id = $this->createElement('select', 'el_component_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->setRegisterInArrayValidator(false)
                ->addMultiOptions(array('' => 'Select'));
        $this->addElement($el_component_id);
        
        $el_project_id = $this->createElement('select', 'el_project_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->setRegisterInArrayValidator(false)
                ->addMultiOptions(array('' => 'Select'));
        $this->addElement($el_project_id);
        
        $group_name = $this->createElement('text', 'group_name')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')                
                ->removeDecorator("htmlTag");
        $this->addElement($group_name);
        
        $student_ids = $this->createElement('Multiselect', 'student_ids')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->setRegisterInArrayValidator(false)
                ->addMultiOptions(array('' => 'Select'));
        $this->addElement($student_ids);
    }

}

?>