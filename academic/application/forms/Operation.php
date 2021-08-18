<?php

class Application_Form_Operation extends Zend_Form {


 

    public function init() {
         

        $booking_date = $this->createElement('text', 'booking_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control', 'datepicker'))
                ->setAttrib('placeholder', 'dd/mm/yy')
                ->setAttrib('required', 'required')
                
                ->removeDecorator("htmlTag");

        $this->addElement($booking_date);

         $batch = $this->createElement('select', 'batch')
                ->removeDecorator('label')->setAttrib('class', array('form-control','chosen'))
                  ->setAttrib('required', 'required')
              ->addMultioptions(array(''=>'Select'))
                ->removeDecorator("htmlTag");
        $this->addElement($batch);


        $department = new Application_Model_Department();
        $data = $department->getDropDownList();

        $department = $this->createElement('select', 'department')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control','chosen'))
                 ->setAttrib('required', 'required')
                ->addMultioptions(array('' => 'Select'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($department);
        
        $duration = new Application_Model_Duration();
        $data = $duration->getDropDownList();
        
        $duration = $this->createElement('select', 'duration')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                 ->setAttrib('required', 'required')
                ->addMultioptions(array('' => 'Select'))
                ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($duration);
        
        
        
  //  $room_no = new Application_Model_Room();
    //    $data = $room_no->getDropDownList();
  $room_no = $this->createElement('select', 'room_no')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control','chosen'))
           ->setAttrib('required', 'required')
                ->addMultioptions(array('' => 'Select'))
              //  ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($room_no);
        
        
        //  $duration = new Application_Model_Student();
        //$data = $duration->getDropDownList1();
        
          $room_start = $this->createElement('select', 'roll_start')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control','chosen'))
                   ->setAttrib('required', 'required')
                ->addMultioptions(array('' => 'start'))
                //->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($room_start);
        
          //$duration = new Application_Model_student();
        //$data = $duration->getDropDownList1();
        
          $room_end = $this->createElement('select', 'roll_end')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control','chosen'))
                   ->setAttrib('required', 'required')
                ->addMultioptions(array('' => 'end'))
               // ->addMultioptions($data)
                ->removeDecorator('htmlTag');
        $this->addElement($room_end);
        

        
         $declaredTerms =  new Application_Model_Declaredterms();
        $data = $declaredTerms->getDropDownList();
           $term = $this->createElement('select', 'cmn_terms')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                   ->addMultiOptions($data);
                   $this->addElement($term);
     



            //$Academic_model = new Application_Model_Academic();
		//$data = $Academic_model->getDropDownList();
		//print_r($data); die;
		$academic_year_id = $this->createElement('select','academic_year_id')
							->removeDecorator('label')
							->setAttrib('class',array('form-control','chosen-select'))
                                                        ->setAttrib('required','required')
							->addMultiOptions(array('' => 'Select'))
							//->addMultiOptions($data)
							->removeDecorator("htmlTag");
        $this->addElement($academic_year_id);
        
        $term_id = $this->createElement('select', 'term_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select '))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->setRegisterInArrayValidator(false);
        $this->addElement($term_id);
            
             $course_id = $this->createElement('select', 'course_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select chosen'))
               // ->setAttrib('style',array('display:none')) 
                       ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                //->addMultiOptions($data1)
               ->setRegisterInArrayValidator(false);
        $this->addElement($course_id);	
	
    //seating arrangement form creating form here

        $credit_models=new Application_Model_Operation();
        $get_date =$credit_models->getExamDate(); 

          $exam_date = $this->createElement('select','exam_date')
              ->removeDecorator('label')
              ->setAttrib('class',array('form-control','chosen-select'))
              ->setAttrib('required','required')
              ->removeDecorator("htmlTag")
              ->addMultiOptions(array('' => '-- Select Exam Date-- '))
              ->addMultiOptions($get_date);            
        $this->addElement($exam_date); 


        $time_slot = $this->createElement('select', 'time_slot')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select '))
                ->setAttrib('required', 'required')   
                 ->removeDecorator("htmlTag"); 
        $this->addElement($time_slot);
            
       $room_number = $this->createElement('select', 'room_number')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
               // ->setAttrib('style',array('display:none')) 
                       ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");

        $this->addElement($room_number);  

                //->addMultiOptions(array('' => 'Select'))
                //->addMultiOptions($data1)
               // ->setRegisterInArrayValidator(false);
        


   
    }

}
