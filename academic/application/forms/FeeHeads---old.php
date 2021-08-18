<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */

class Application_Form_FeeHeads extends Zend_Form
{	
    public function init()
    {
        $fee_head = $this->createElement('text','fee_head')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
                ->setAttrib('required','required')
				->setRequired(true)
             ->removeDecorator("htmlTag");
        $this->addElement($fee_head);
		
		$one_time = $this->createElement('checkbox','one_time')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($one_time);

		$monthly = $this->createElement('checkbox','monthly')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($monthly);
		
		$bi_monthly = $this->createElement('checkbox','bi_monthly')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($bi_monthly);
		
		$quarterly = $this->createElement('checkbox','quarterly')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($quarterly);
		
		$term = $this->createElement('checkbox','term')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($term);
		
		$half_yearly = $this->createElement('checkbox','half_yearly')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($half_yearly);
		
		$yearly = $this->createElement('checkbox','yearly')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($yearly);

		$refundable = $this->createElement('checkbox','refundable')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($refundable);	 
		
		$royalty = $this->createElement('checkbox','royalty')
                ->removeDecorator('label')
                ->setAttrib('class',array('form-control'))
				->setAttrib('style', 'width: 20px;')
             ->removeDecorator("htmlTag");
        $this->addElement($royalty);
		
    }
}

