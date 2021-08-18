<?php

class Application_Form_StudentPortal extends Zend_Form {
 var $year_of_passing='10-01-2011';
    
 

    public function init() {


        /* $coursetype_model = new Application_Model_Coursetype();
          $data = $coursetype_model->getDropDownList();
          //print_r($data); die;
          $ct_id = $this->createElement('select','ct_id')
          ->removeDecorator('label')
          ->setAttrib('class',array('form-control','chosen-select'))
          ->setAttrib('required','required')
          ->removeDecorator("htmlTag")
          ->addMultiOptions(array('' => 'Select'))
          ->addMultiOptions($data);
          $this->addElement($ct_id); */
        $stu_fname = $this->createElement('text', 'stu_fname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_fname);

        $stu_lname = $this->createElement('text', 'stu_lname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_lname);
        
        $blood_group = $this->createElement('text', 'blood_group')
                ->setAttrib('maxlength', '3')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))                
                ->removeDecorator("htmlTag");
        $this->addElement($blood_group);
        
        $gender = $this->createElement('select', 'gender')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select',
            '1' => 'Male',
            '2' => 'Female'));
        $this->addElement($gender);
       
        $participant_username = $this->createElement('text', 'participant_username')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($participant_username);
      
        $linked_in = $this->createElement('text', 'linked_in')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($linked_in);
        
        $secondary_mail = $this->createElement('text', 'secondary_mail')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($secondary_mail);
        
         $participant_pword = $this->createElement('password', 'participant_pword')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($participant_pword);
        
          $confirm_password = $this->createElement('password', 'confirm_password')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                //->setAttrib('required', 'true')
                  //->addValidator($validator)
                ->removeDecorator("htmlTag");
        $this->addElement($confirm_password);
        
                $date_box =  $this->getDropDownList($this->year_of_passing);
              //  print_r($date_box);exit;
                 $year_of_passing = $this->createElement('select', 'passing_year')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))

                //->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
               ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($date_box);
        $this->addElement($year_of_passing);

        $stu_mobileno = $this->createElement('text', 'stu_mobileno')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_mobileno);

        $stu_email_id = $this->createElement('text', 'stu_email_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_email_id);

        $stu_dob = $this->createElement('text', 'stu_dob')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_dob);

        $present_addr = $this->createElement('textarea', 'present_addr')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($present_addr);

        $premanent_addr = $this->createElement('textarea', 'premanent_addr')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($premanent_addr);

        $father_fname = $this->createElement('text', 'father_fname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($father_fname);

        $father_lname = $this->createElement('text', 'father_lname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($father_lname);

        $father_mobileno = $this->createElement('text', 'father_mobileno')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($father_mobileno);


        $mother_fname = $this->createElement('text', 'mother_fname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($mother_fname);

        $mother_lname = $this->createElement('text', 'mother_lname')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setAttrib('required', 'true')
                ->removeDecorator("htmlTag");
        $this->addElement($mother_lname);

        $mother_mobileno = $this->createElement('text', 'mother_mobileno')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($mother_mobileno);

        /* $mother_occupation = $this->createElement('select','mother_occupation')
          ->removeDecorator('label')
          ->setAttrib('class',array('form-control','chosen-select'))
          ->setAttrib('required','required')
          ->removeDecorator("htmlTag")
          ->addMultiOptions(array('' => 'Select',
          '1'=>'Employee',
          '2'=>'Self Employed',
          '3'=>'Home Maker'));
          $this->addElement($mother_occupation); */

        $Academic_model = new Application_Model_Academic();
        $data = $Academic_model->getDropDownList();
        //print_r($data); die;
        $academic_id = $this->createElement('select', 'academic_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'))
                ->addMultiOptions($data);
        $this->addElement($academic_id);

        /* $Term_model = new Application_Model_TermMaster();
          $data = $Term_model->getDropDownList();
          //print_r($data); die;
          $terms_id = $this->createElement('select','terms_id')
          ->removeDecorator('label')
          ->setAttrib('class',array('form-control','chosen-select'))
          ->setAttrib('required','required')
          ->removeDecorator("htmlTag")
          ->addMultiOptions(array('' => 'Select'))
          ->addMultiOptions($data);
          $this->addElement($terms_id); */

        /* 	$year = $this->createElement('select','year')
          ->removeDecorator('label')
          ->setAttrib('class',array('form-control','chosen-select'))
          ->setAttrib('required','required')
          ->removeDecorator("htmlTag")
          ->addMultiOptions(array('' => 'Select',
          '1'=>'First Year',
          '2'=>'Second Year'));
          $this->addElement($year); */


        $stu_id = $this->createElement('text', 'stu_id')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','required')
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($stu_id);

        $stu_status = $this->createElement('select', 'stu_status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(// '' => 'Select',
            '1' => 'Continue',
            '2' => 'Discontinue'));
        $this->addElement($stu_status);


        $effective_date = $this->createElement('text', 'effective_date')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($effective_date);

        $adv_col = $this->createElement('textarea', 'adv_col')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
                //->setRequired(true)
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($adv_col);
    }
    
public function getDropDownList($year_of_passing=''){
$data = array();
$start_date = $year_of_passing;
//print_r($start_date);exit;
$year = substr($start_date, strlen($start_date)-4,4);

$years = $this->date_diff2($start_date,date('d-m-Y'));
for($i=0;$i<=$years; $i++){
$data[(int)$year+$i] = (int)$year+$i;
}
return $data;
}



function date_diff2($date1 = '', $date2 = '') {


    $diff = abs(strtotime($date2) - strtotime($date1));

    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff ) / (60 * 60 * 24));
    return (int) $years;
}

}

?>