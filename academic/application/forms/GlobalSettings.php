<?php
class Application_Form_GlobalSettings extends Zend_Form {

    public function init() {
        $GlobalSettings_model = new Application_Model_GlobalSettings();
        $data = $GlobalSettings_model->gs_category;
        $gs_category = $this->createElement('select', 'gs_category')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($gs_category);
        $gs_display_name = $this->createElement('text', 'gs_display_name')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($gs_display_name);

        $gs_system_name = $this->createElement('text', 'gs_system_name')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($gs_system_name);
        
        $gs_system_name = $this->createElement('text', 'gs_default_time')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($gs_system_name);
        
        $gs_system_name = $this->createElement('text', 'gs_min_time')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($gs_system_name);
        
        $gs_system_name = $this->createElement('text', 'gs_max_time')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($gs_system_name);

        $gs_content = $this->createElement('textarea', 'gs_content')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($gs_content);
    }

}

?>