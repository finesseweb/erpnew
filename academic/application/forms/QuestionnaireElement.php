<?php
class Application_Form_QuestionnaireElement extends Zend_Form
{
	public function init()
	{
            $Rating_master = new Application_Model_Questionnaire();
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
        
       $text_filed = $this->createElement('textarea', 'text_filed')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired(true)
                 ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($text_filed);
        
        
          $question_type = $this->createElement('select', 'question_type')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('1'=>'Course','2'=>'Instructor'));
        $this->addElement($question_type);
     	
        $rating_value = $this->createElement('select', 'rating_required')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('1'=>'yes','2'=>'No'));
        $this->addElement($rating_value);
        
        
        $status = $this->createElement('select', 'status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
            '1' => 'Active',
            '2' => 'Block'));
        $this->addElement($status);
            
        }
}
