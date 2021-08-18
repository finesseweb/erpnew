<?php
class Application_Form_FeedBackElement extends Zend_Form
{
	public function init()
	{
            $Rating_master = new Application_Model_FeedBack();
            $ques_no = $Rating_master->Auto_num();
            $rating_id1= $this->createElement('text', 'Auto_no1')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                     ->setAttrib('disabled', 'true')
                    ->setValue($ques_no)
                ->removeDecorator("htmlTag");
        $this->addElement($rating_id1);
        
         $rating_id= $this->createElement('text', 'Auto_no')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->setValue($ques_no)
                ->removeDecorator("htmlTag");
        $this->addElement($rating_id);
        
         $template_code= $this->createElement('text', 'template_code')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($template_code);
        
        $Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
                  $this->addElement($academic_id);
                
                
                
                $FeeCategory_model = new Application_Model_ScholarStructure();
$data1 = $FeeCategory_model->getDropDownListOfTerms();
        $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions($data1)
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
        
       
        
        
          $question_type = $this->createElement('select', 'question_type')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(''=>'Select','1'=>'Course','2'=>'Instructor'));
        $this->addElement($question_type);
     	
       
            
        }
}
