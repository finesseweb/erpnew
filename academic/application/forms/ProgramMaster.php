<?php
class Application_Form_ProgramMaster extends Zend_Form
{
    public function init()
	
    {
		
		$Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownListShortCode();
	//print_r($data); die;
		$short_id = $this->createElement('select','short_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($short_id);
		
		
		
		/* $Academic_model = new Application_Model_Academic();
		$data = $Academic_model->getDropDownList();
	//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
						   ->setAttrib('required','required')
							->removeDecorator("htmlTag")
							->addMultiOptions(array('' => 'Select'))
							->addMultiOptions($data);
        $this->addElement($academic_year_id); */
		
        $pm_name = $this->createElement('text','pm_name')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
               ->setAttrib('required','required')
                ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($pm_name);
		
		$pm_desc = $this->createElement('textarea','pm_desc')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
				->setAttrib('rows', '2')
                ->setRequired(true)
                ->removeDecorator("htmlTag");
        $this->addElement($pm_desc);
		
	/*	$start_date = $this->createElement('text','start_date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                ->setRequired(true)
				//->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($start_date);
		
		$end_date = $this->createElement('text','end_date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
                ->setRequired(true)
				//->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($end_date); */
		
		
		
		}
	}
		?>