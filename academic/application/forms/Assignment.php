<?php
class Application_Form_Assignment extends Zend_Form
{
	public function init()
	{
            $Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($academic_year_id);
        
        $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
            
             $course_id = $this->createElement('select', 'course_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
               // ->setAttrib('style',array('display:none')) 
                       ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                //->addMultiOptions($data1)
               ->setRegisterInArrayValidator(false);
        $this->addElement($course_id);	
        
     	
                   $document_type = $this->createElement('select', 'document_type')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select',
            '1' => 'Assignments',
            '2' => 'Course Materials'));
        $this->addElement($document_type);
        
        
        $status = $this->createElement('select', 'status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
            '1' => 'Active',
            '2' => 'Inactive'));
        $this->addElement($status);

        $document_title = $this->createElement('text', 'document_title')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($document_title);
        
     $remarks = $this->createElement('textarea', 'remarks')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($remarks);
        
    $assignment_due= $this->createElement('text','due_date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
              // ->setAttrib('required', 'required')
               
                //->setAttrib(array('value', date('d-m-Y h:s:a')))
                ->removeDecorator("htmlTag");
        $this->addElement($assignment_due);
        
        
            
        }
        
 }
