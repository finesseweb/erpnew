<?php

class AssignmentController extends Zend_Controller_Action {

    private $_siteurl = null;

    private $_db = null;

    private $_authontication = null;

    private $_agentsdata = null;

    private $_usersdata = null;

    private $_act = null;

    private $_adminsettings = null;

    private $_flashMessenger = null;
    private $login_storage = NULL;
    private $roleConfig = NULL;
    private $accessConfig =NULL;
    public function init() {

        $zendConfig = new Zend_Config_Ini(

                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();

        $config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;

        $this->_action = $this->getRequest()->getActionName();
        
        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");					
        $this->login_storage = $data = $storage->admin_login;
        $this->view->login_storage = $data;
        //print_r($data);exit;
        if( isset($data) ){
                $this->view->role_id = $data->role_id;
                $this->view->login_empl_id = $data->empl_id;
        }

        if ($this->_action == "login" || $this->_action == "forgot-password") {

            $this->_helper->layout->setLayout("adminlogin");

        } else {

            $this->_helper->layout->setLayout("layout");

        }
	

        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();

        $this->_act = new Application_Model_Adminactions();

        $this->_db = Zend_Db_Table::getDefaultAdapter();

    }
    protected function authonticate() {

        $storage = new Zend_Session_Namespace("admin_login");

        $data = $storage->admin_login;
        if($data->role_id == 0)
            $this->_redirect('student-portal/assignments');
        if (!$data && $this->_action != 'login' &&

                $this->_action != 'forgot-password') {

            $this->_redirect('index/login');

            return;

        }

        if ($this->_action != 'forgot-password') {

            $this->_authontication = $data;

            $this->_agentsdata = $storage->agents_data;

        }

    }

    public function indexAction() {
		$this->view->action_name = 'notificationpush';
        $this->view->sub_title_name = 'notificationpush';
        $this->accessConfig->setAccess('SA_ACAD_NOTIFICATION');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        
		$EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
                $assignment_model = new Application_Model_Assignment();
		$assignment_form = new Application_Form_Assignment();
                $student_assignment_model = new Application_Model_SubmitAssignment();
		$ec_id = $this->_getParam("id");
                $aca = $this->_getParam('aca');
                $ter = $this->_getParam('ter');
        $type = $this->_getParam("type");
		$this->view->type = $type;
        $this->view->form = $assignment_form;
		
        switch ($type) {
            case "add":    
                if ($this->getRequest()->isPost()) {
                    if ($assignment_form->isValid($this->getRequest()->getPost())) {
                       
                        $data = $assignment_form->getValues();
                        $data['updated_by'] = $_SESSION['admin_login']['admin_login']->id;
                        $data['updated_date'] = date('d-m-Y h:i:s A');
                        if($this->login_storage->empl_id)
                        $data['empl_id'] = $this->login_storage->empl_id;
                        else
                          $data['empl_id'] = 1;  
                        $last_insert_id = $assignment_model->insert($data);
                                        //$student_assignment_model->insert($data_student);
                                                $student_info = $student_assignment_model->getstudents($data['academic_year_id'],$data['term_id']);
                                                foreach($student_info as $key => $value){
                                                            $student_data['student_id'] = $value['student_id'];
                                                            $student_data['assignment_id'] = $last_insert_id;
                                                            $student_data['course_id'] = $data['course_id'];
                                                            $student_assignment_model->insert($student_data);      
                                                }
                            $dirPath = APPLICATION_PATH . '/../public/Assignments/' . '/'.$last_insert_id.'/assignment_details/';
					//print_r($dirPath);exit;	
						if (!file_exists($dirPath)) {
							mkdir($dirPath,755,true);
							
						}	
						$file_name = $_FILES["file"]["name"]; 
						
						$tem_name = $_FILES["file"]["tmp_name"];
						  $imageFileType = strtolower(pathinfo($file_name[0],PATHINFO_EXTENSION));
                        
                                               if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "pdf" || $imageFileType == "doc" || $imageFileType == "docx" || $imageFileType == "ppt" || $imageFileType == "xls") {
						if(move_uploaded_file($tem_name[0], $dirPath.$file_name[0])){
							//echo "File is valid, and was successfully uploaded"; 
						}else {
						  // echo "Upload failed";  
						}	
						$file_data['filename'] = "public/Assignments/".$last_insert_id."/assignment_details/".$file_name[0];
                        
						 $assignment_model->update($file_data, array('assignment_id=?' => $last_insert_id));
                                               }
                        $this->_flashMessenger->addMessage('Evaluation Components Successfully added');

                        $this->_redirect('assignment/index');
						}
                    }

                
                break;
            case 'edit': 
                   $result = $assignment_model->getRecord($ec_id);
                 $Academic_model = new Application_Model_TermMaster();
                $data = $Academic_model->getCoreCourseTerms($aca);
                $assignment_form->getElement('term_id')->setAttrib('style',array("display","initial"));
                $employee_id = $assignment_form->createElement('select', 'term_id');
                $employee_id->setAttrib('class', array('form-control', 'chosen-select'));
                $employee_id->setAttrib('required','required');
                //$employee_id->removeDecorator("htmlTag");
                $employee_id->addMultiOptions(array('' => 'Select'));
                $employee_id->setRegisterInArrayValidator(false);
                $employee_id->addMultiOptions($data);
                $assignment_form->addElement($employee_id);
                
              
                   $data = $this->GetCourse($ter,$aca);
                   
                 $assignment_form->getElement('course_id')->setAttrib('style',array("display","initial"));
                $employee_id = $assignment_form->createElement('select', 'course_id');
                $employee_id->setAttrib('class', array('form-control', 'chosen-select'));
                $employee_id->setAttrib('required','required');
               // $employee_id->removeDecorator("htmlTag");
                $employee_id->addMultiOptions(array('' => 'Select'));
                $employee_id->setRegisterInArrayValidator(false);
                $employee_id->addMultiOptions($data);
                $assignment_form->addElement($employee_id);
              
                
                $assignment_form->populate($result);
				$this->view->result = $result;
                   if ($this->getRequest()->isPost()) {
                    if ($assignment_form->isValid($this->getRequest()->getPost())){
                        $data = $assignment_form->getValues();
                        $data['updated_by'] = $_SESSION['admin_login']['admin_login']->id;
                        $data['updated_date'] = date('d-m-Y h:i:s A');
                      //  echo "<pre>";print_r($data);exit;
                      $dirPath = APPLICATION_PATH . '/../public/Assignments/' . '/'.$ec_id.'/assignment_details/';
						
						if (!file_exists($dirPath)) {
							mkdir($dirPath,755,true);
							
						}	
						$file_name = $_FILES["file"]["name"]; 
						
						$tem_name = $_FILES["file"]["tmp_name"];
						  $imageFileType = strtolower(pathinfo($file_name[0],PATHINFO_EXTENSION));
                        
                                               if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "pdf" || $imageFileType == "doc" || $imageFileType == "docx" || $imageFileType == "ppt" || $imageFileType == "xls") {
						if(move_uploaded_file($tem_name[0], $dirPath.$file_name[0])){
							//echo "File is valid, and was successfully uploaded"; 
						}else {
						  // echo "Upload failed";  
						}	
						if(!empty($file_name[0])){
							$data['filename'] = "public/Assignments/".$ec_id."/assignment_details/".$file_name[0];
						}else{
							$data['filename'] = $result['filename'];
						}
                                        
                                               }                  
                        $assignment_model->update($data, array('assignment_id=?' => $ec_id));  
                    }
                      $this->_flashMessenger->addMessage('Assignment Sharing Successfully updated !');
					$this->_redirect('assignment/index');
                   }
                
            
                
               break;
            case 'delete':
                $data['status'] = 2;
                if ($ec_id){
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
					$EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
					$this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
					$this->_redirect('assignment/index');
				}
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                    $result = $assignment_model->getRecords();
                    $i=0;
                    foreach($result as $key){
                    $file_arr = explode('/',$key['filename']);
                    $file_name = explode(".",$file_arr[count($file_arr)-1]);
                    $result[$i]['filename1'] = $file_name[0];
                    $i++;
                    }
                  
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }

    }
	        
 public function GetCourse($term_id, $batch_id) {
    
        $course_details = new Application_Model_Attendance();
    
            $result = $course_details->getCourseDetails($term_id, $batch_id);
           
           $body[""] = 'Select';
            foreach ($result as $value) {
                $body[ $value['course_id'] ] = $value['course_code'];
            }
       return $body;
    }
    
    
	public function ajaxEvaluationComponentsViewAction()
	{
		$this->_helper->layout->disableLayout();
		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
			$evaluation_id = $this->_getParam("evaluation_id");
			$EvaluationComponents_model = new Application_Model_EvaluationComponents();
			$result = $EvaluationComponents_model->getComponentsView($evaluation_id);
			$this->view->result = $result;
			
		}
	}	

}