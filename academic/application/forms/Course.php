<?php
class Application_Form_Course extends Zend_Form
{
    public function init()
	
    {
		
		$coursecategory_model = new Application_Model_Coursecategory();
		$data = $coursecategory_model->getDropDownList();
		$course_category_id = $this->createElement('select','course_category_id')
		                     ->removeDecorator('label')
							 ->setAttrib('class',array('form-control','chosen-select'))
							 ->setAttrib('required','required')
							 ->addMultiOptions(array(''=>'Select'))
							 ->addMultiOptions($data)
							 ->removeDecorator("htmlTag");
		$this->addElement($course_category_id);				 
							 
		$coursetype_model = new Application_Model_Coursetype();
		$data = $coursetype_model->getDropDownList();
	//print_r($data); die;
		$ct_id = $this->createElement('select','ct_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($ct_id);
			
		
		$course_code = $this->createElement('text','course_code')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($course_code);
		 
		
        $course_name = $this->createElement('text','course_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($course_name);
		
		$course_description = $this->createElement('textarea','course_description')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($course_description);
		
		
		}
	}
		?>