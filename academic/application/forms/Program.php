<?php
/**
 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */

class Application_Form_Program extends Zend_Form
{	
    public function init()
    {
        $program_name = $this->createElement('text','program_name')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setRequired(true)
				->setAttrib('required','required')
             ->removeDecorator("htmlTag");
        $this->addElement($program_name);
		$program_code = $this->createElement('text','program_code')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setRequired(true)
				->setAttrib('required','required')
             ->removeDecorator("htmlTag");
        $this->addElement($program_code);
		$live_updates = $this->createElement('checkbox','live_updates')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				//->setAttrib('style', 'width: 10px;')
             ->removeDecorator("htmlTag");
        $this->addElement($live_updates);
    }
}

