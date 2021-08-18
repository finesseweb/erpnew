<?php

class Application_Form_ExperientialLearningProject extends Zend_Form {

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
        
        $sector = $this->createElement('text', 'sector')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')                
                ->removeDecorator("htmlTag");
        $this->addElement($sector);
        
        $project_location = $this->createElement('text', 'project_location')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')                
                ->removeDecorator("htmlTag");
        $this->addElement($project_location);
        
        $hosting_org = $this->createElement('text', 'hosting_org')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($hosting_org);      

        $project_name = $this->createElement('textarea', 'project_name')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->setAttrib('maxlength', '300')
                ->setAttrib('rows', '4')
                ->removeDecorator("htmlTag");
        $this->addElement($project_name);
    }

}

?>