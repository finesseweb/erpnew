<?phpclass Application_Form_ReportsForm extends Zend_Form
{    public function init()
        {
            $start_date = $this->createElement('text','start_date');
            $start_date ->removeDecorator('label')
                                        ->setAttrib('class', 'form-control start_date')
                                        ->setAttrib('required','required')										//->setValue(date( "Y-m-d" ))
                                        ->removeDecorator("htmlTag");			$end_date = $this->createElement('text','end_date');            $end_date ->removeDecorator('label')                                        ->setAttrib('class', 'form-control end_date')                                        ->setAttrib('required','required')										//->setValue(date( "Y-m-d" ))                                        ->removeDecorator("htmlTag");
            $this->addElements(array(
                  $start_date,				  $end_date
            ));
		}
}