<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */

class Application_Form_FeesType extends Zend_Form
{	
    public function init()
    {
        $fee_type_title = $this->createElement('text','fee_type_title')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setAttrib('required','required')
				->setRequired(true)
             ->removeDecorator("htmlTag");
        $this->addElement($fee_type_title);
    }
}

