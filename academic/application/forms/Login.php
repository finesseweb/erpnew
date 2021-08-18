<?php


class Application_Form_Login extends Zend_Form

{

    public function init()

    {

        $username = $this->createElement("text",'username');

        $username->setRequired(true)

                ->removeDecorator('label')

                ->removeDecorator("htmlTag")

                ->setAttrib('class', array('form-control','uname'))
                ->setAttrib('placeholder', 'username') ->setAttrib('required', '')
                ->setAttrib('tabindex', '1')
                ->removeDecorator('errors');

                ;
         $select_com_product = $this->createElement('select','select_com_product');
            $select_com_product->removeDecorator('label')			 
                                    ->removeDecorator("htmlTag") 
                                    ->setAttrib("class", "form-control") 
                                    ->addMultiOptions( array('0'  => 'Tech Integra ERP' )  );


        $password = $this->createElement('password','password');

        $password->setAttrib('class', array('form-control','pword'))

                ->removeDecorator('label') ->setAttrib('required', '')

                ->removeDecorator("htmlTag")

                 ->setAttrib('tabindex', '2')
                 ->setAttrib('placeholder', 'password')
                 ->removeDecorator('errors')

                ->setRequired(true);

        $rem = $this->createElement('checkbox','remember');

        $rem->removeDecorator('label')

            ->setAttrib('class',array('form-control'))

                ->setAttrib('tabindex', '4')
                ->removeDecorator('errors')
            ->removeDecorator("htmlTag");                

        $this->addElements(array($username,$rem,$password,$select_com_product));
    }





}



