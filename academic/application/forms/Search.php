<?phpclass Application_Form_Search extends Zend_Form
{    public function init()
	{
		$search_by = $this->createElement('text','search_by')			->removeDecorator('label')->setAttrib('class',array('form-control'))			->setAttrib('required','required')			->removeDecorator("htmlTag");		$this->addElement($search_by);
	}
}