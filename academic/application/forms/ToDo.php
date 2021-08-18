<?php

class Application_Form_ToDo extends Zend_Form
{
public function init()
	{
            
             $category = $this->createElement('select', 'toDo_category')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
               // ->setAttrib('style',array('display:none')) 
                       ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
                    '1'=>'Academic','2'=>'Admission'))
                //->addMultiOptions($data1)
               ->setRegisterInArrayValidator(false);
        $this->addElement($category);	
        
       $toDo_activity_date = $this->createElement('text','toDo_activity_date')
              
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
               ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($toDo_activity_date);
		
		$toDo_due_date= $this->createElement('text','toDo_due_date')
                ->removeDecorator('label')->setAttrib('class',array('form-control','datepicker'))
               ->setAttrib('required', 'required')
               
                //->setAttrib(array('value', date('d-m-Y h:s:a')))
                ->removeDecorator("htmlTag");
        $this->addElement($toDo_due_date);
        
     	
                   $toDo_priority = $this->createElement('select', 'toDo_priority')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
            '1' => 'Critical',
            '2' => 'High',
            '3' => 'Low'));
        $this->addElement($toDo_priority);
        $val = '';
        if($_SESSION['admin_login']['admin_login']->empl_id!='')
            $val = $_SESSION['admin_login']['admin_login']->empl_id;
          $toDo_assigned_to = $this->createElement('select', ' toDo_assigned_to')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array($val => 'Self',
            'EMP-F' => 'Faculty',
            'VF' => 'Visiting Faculty'));
        $this->addElement($toDo_assigned_to);
        
        
        $toDo_assigned_to_id = $this->createElement('select', ' toDo_assigned_to_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array('' => 'Select'));
        $this->addElement($toDo_assigned_to_id);
        
        
        $toDo_status = $this->createElement('select', 'toDo_status')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control', 'chosen-select'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag")
                ->addMultiOptions(array(
            '1' => 'Not Started',
            '2' => 'In Progress',
            '3' => 'Completed'));
        $this->addElement($toDo_status);

     
        
     $toDo_task_description = $this->createElement('textarea', 'toDo_task_description')
                ->removeDecorator('label')->setAttrib('class', array('form-control'))
               ->setAttrib('required', 'required')
                ->setAttrib('rows', '2')
                ->removeDecorator("htmlTag");
        $this->addElement($toDo_task_description);
            
        }
        
       
        
        
        }

