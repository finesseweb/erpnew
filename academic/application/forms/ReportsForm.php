<?php
{
        {
            $start_date = $this->createElement('text','start_date');
            $start_date ->removeDecorator('label')
                                        ->setAttrib('class', 'form-control start_date')
                                        ->setAttrib('required','required')
                                        ->removeDecorator("htmlTag");
            $this->addElements(array(
                  $start_date,
            ));
		}
}