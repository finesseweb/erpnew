<?php
class Application_Form_ProgramDesign extends Zend_Form
{
    public function init()
	
    {
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownListShortCode();
	//print_r($data); die;
		$short_code = $this->createElement('select','short_code')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($short_code);
		
		
		
	/*	$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
	//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
						   ->setAttrib('readonly','readonly')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id); */
		
		$ProgramMaster_model = new Application_Model_ProgramMaster();
		$data = $ProgramMaster_model->getDropDownListSessionName();
		//print_r($data);die;
		$pd_name = $this->createElement('select','pd_name')
                ->removeDecorator('label')
				->setAttrib('class',array('form-control','chosen-select'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag")
				->addMultiOptions(array('' => 'Select'))
				->addMultiOptions($data);
        $this->addElement($pd_name);
		
		$se_name = $this->createElement('text','se_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($se_name);
		
		$pd_desc = $this->createElement('textarea','pd_desc')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($pd_desc);
		
		
		$start_date = $this->createElement('text','start_date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                          ->setAttrib('autocomplete','off')
                ->setRequired(true)
				//->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($start_date);
		
		$end_date = $this->createElement('text','end_date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                ->setRequired(true)
				//->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($end_date);
		
		/* $no_weeks = $this->createElement('text','no_weeks')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($no_weeks); 
		 
		 
		 $sort_no = $this->createElement('text','sort_no')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($sort_no); */
		
		}
	}
		?>