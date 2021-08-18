<?php
class Application_Form_Batch extends Zend_Form
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
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id);
		
        $batch_no = $this->createElement('text','batch_no')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($batch_no);
		
		
		 $description = $this->createElement('textarea','description')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
			   ->setAttrib('rows', '2')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($description);
		}
	}
		?>