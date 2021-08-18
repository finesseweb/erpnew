<?php
    class ExperientialCourseController extends Zend_Controller_Action {

    private $_siteurl = null;

    private $_db = null;

    private $_authontication = null;

    private $_agentsdata = null;

    private $_usersdata = null;

    private $_act = null;

    private $_adminsettings = null;

    private $_flashMessenger = null;
    private $accessConfig =NULL;



    public function init() {

        $zendConfig = new Zend_Config_Ini(

                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();

        $config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;

        $this->_action = $this->getRequest()->getActionName();
        //access role id
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
            $this->_redirect('student-portal/student-dashboard');
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

    public function gradeAction() {

        $this->view->action_name = 'Expr Grade';

        $this->view->sub_title_name = 'Exprcourse-grade';
		
		$ExprCourseGrade_form = new Application_Form_ExprCourseGrade();
		
        $ExprCourseGrade_model = new Application_Model_ExprCourseGrade();
		
		$ExprCourseGradeItems_model = new Application_Model_ExprCourseGradeItems();

        $id = $this->_getParam("id");

        $type = $this->_getParam("type");

        switch ($type) {

            case "add":

                $this->view->type = $type;

                $this->view->form = $ExprCourseGrade_form;

                if ($this->getRequest()->isPost()) {

                    if ($ExprCourseGrade_form->isValid($this->getRequest()->getPost())) {

                        $data = $ExprCourseGrade_form->getValues();

                    $last_insert_id = $ExprCourseGrade_model->insert($data);
					
					$ExperientialLearning_model= new Application_Model_ExperientialLearning();
					$course_result= $ExperientialLearning_model->getExpCourseRecords($data['academic_id'],$data['year_id']);
					$course = $_POST['course'];
					//echo'<pre>';print_r($course);die;
					for($i=0;$i<count($course_result);$i++) {   
					//print_r(count($_POST['course']['student_id_'.$course_result[$i]['elc_id']]));die;
							for($k=0;$k<count($course['student_id_'.$course_result[$i]['elc_id']]);$k++){
								//echo'<pre>';print_r($_POST['course']['student_id_'.$course_result[$i]['elc_id']]);die;
									$item_data['id'] = $last_insert_id;
									$item_data['courses_id'] = $course_result[$i]['elc_id'];
									$item_data['student_id'] = 	$course['student_id_'.$course_result[$i]['elc_id']][$k];
									//echo'<pre>';print_r($item_data);die;
									$item_data['academic_credits'] = $course['academic_credits_'.$course_result[$i]['elc_id']][$k];
									$item_data['credit_value'] = $course['crd'.$course_result[$i]['elc_id']][$k];
									//echo'<pre>';print_r($item_data);die;
									$ExprCourseGradeItems_model->insert($item_data);
								}
					}
                        $this->_flashMessenger->addMessage('Experiential Courses Grade Added Successfully ');

                        $this->_redirect('experiential-course/grade');

                    } 
                }

                break;

         /*    case 'edit':

                $this->view->type = $type;

                $this->view->form = $CourseGrade_form;

                $result = $CourseGrade_model->getRecord($id); 

                $CourseGrade_form->populate($result);

                if ($this->getRequest()->isPost()) {

                    if ($CourseGrade_form->isValid($this->getRequest()->getPost())) {

                        $data = $CourseGrade_form->getValues();

                        $CourseGrade_model->update($data, array('id=?' => $id));

                        $this->_flashMessenger->addMessage('Courses Grade  Updated Successfully');

                        $this->_redirect('course-grade/index');

                    }

                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($account_id)

                    $Account_model->update($data, array('account_id=?' => $account_id));

                $this->_flashMessenger->addMessage('Account Master Deleted Successfully');

                $this->_redirect('account/master');

                break; */

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $ExprCourseGrade_model->getRecords();
				//print_r($result); die;

                $page = $this->_getParam('page', 1);

                $paginator_data = array(

                    'page' => $page,

                    'result' => $result

                );

                $this->view->paginator = $this->_act->pagination($paginator_data);

                break;

        }

    }
	
	public function ajaxGetTermsAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			$course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);
			//print_r($course_result);die;
			$this->view->course_result = $course_result;
		}
	}
	
	
	public function ajaxCheckAcademicDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$ExprCourseGrade_model = new Application_Model_ExprCourseGrade();
			$grade_result= $ExprCourseGrade_model->getValidRecord($academic_year_id,$year_id);
			$counts = count($grade_result['id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
		}
	}

	public function penaltiesAction() {

        $this->view->action_name = 'Expr Penalty';

        $this->view->sub_title_name = 'Exprcourse-penalties';
		
		$ExprCoursePenalties_form = new Application_Form_ExprCoursePenalties();
		
        $ExprCoursePenalties_model = new Application_Model_ExprCoursePenalties();
		
		$ExprCoursePenaltyItems_model = new Application_Model_ExprCoursePenaltyItems();

        $id = $this->_getParam("id");

        $type = $this->_getParam("type");

        switch ($type) {

            case "add":

                $this->view->type = $type;

                $this->view->form = $ExprCoursePenalties_form;

                if ($this->getRequest()->isPost()) {

                    if ($ExprCoursePenalties_form->isValid($this->getRequest()->getPost())) {

                        $data = $ExprCoursePenalties_form->getValues();

                    $last_insert_id = $ExprCoursePenalties_model->insert($data);
					
					$ExperientialLearning_model= new Application_Model_ExperientialLearning();
					$course_result= $ExperientialLearning_model->getExpCourseRecords($data['academic_id'],$data['year_id']);
					$course = $_POST['course'];
					//echo'<pre>';print_r($course);die;
					for($i=0;$i<count($course_result);$i++) {   
					//print_r(count($_POST['course']['student_id_'.$course_result[$i]['elc_id']]));die;
							for($k=0;$k<count($course['student_id_'.$course_result[$i]['elc_id']]);$k++){
								//echo'<pre>';print_r($_POST['course']['student_id_'.$course_result[$i]['elc_id']]);die;
									$item_data['id'] = $last_insert_id;
									$item_data['courses_id'] = $course_result[$i]['elc_id'];
									$item_data['student_id'] = 	$course['student_id_'.$course_result[$i]['elc_id']][$k];
									//echo'<pre>';print_r($item_data);die;
									$item_data['academic_credits'] = $course['academic_credits_'.$course_result[$i]['elc_id']][$k];
									$item_data['penalties'] = $course['penalties'.$course_result[$i]['elc_id']][$k];
									//echo'<pre>';print_r($item_data);die;
									$ExprCoursePenaltyItems_model->insert($item_data);
								}
					}
                        $this->_flashMessenger->addMessage('Experiential Courses Penalties Added Successfully ');

                        $this->_redirect('experiential-course/penalties');

                    } 
                }

                break;

         /*    case 'edit':

                $this->view->type = $type;

                $this->view->form = $CourseGrade_form;

                $result = $CourseGrade_model->getRecord($id); 

                $CourseGrade_form->populate($result);

                if ($this->getRequest()->isPost()) {

                    if ($CourseGrade_form->isValid($this->getRequest()->getPost())) {

                        $data = $CourseGrade_form->getValues();

                        $CourseGrade_model->update($data, array('id=?' => $id));

                        $this->_flashMessenger->addMessage('Courses Grade  Updated Successfully');

                        $this->_redirect('course-grade/index');

                    }

                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($account_id)

                    $Account_model->update($data, array('account_id=?' => $account_id));

                $this->_flashMessenger->addMessage('Account Master Deleted Successfully');

                $this->_redirect('account/master');

                break; */

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $ExprCoursePenalties_model->getRecords();
				//print_r($result); die;

                $page = $this->_getParam('page', 1);

                $paginator_data = array(

                    'page' => $page,

                    'result' => $result

                );

                $this->view->paginator = $this->_act->pagination($paginator_data);

                break;

        }

    }

	
	public function ajaxGetPenaltiesAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			$course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);
			//print_r($course_result);die;
			$this->view->course_result = $course_result;
		}
	}
	
	public function ajaxCheckPenaltyDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$ExprCoursePenalties_model = new Application_Model_ExprCoursePenalties();
			$penalty_result= $ExprCoursePenalties_model->getValidRecord($academic_year_id,$year_id);
			$counts = count($penalty_result['id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
			
		}
	}
	

    public function gradeaftrPenaltyAction() {

        $this->view->action_name = 'Expr AftrPenalty';

        $this->view->sub_title_name = 'Exprcourse-grade-aftr-penalties';
		
		$ExprCourseGradeAftrPenalties_form = new Application_Form_ExprCourseGradeAftrPenalties();
		
        $ExprCourseGradeAftrPenalties_model = new Application_Model_ExprCourseGradeAftrPenalties();
		
$ExprCourseGradeAftrPenaltyItems_model = new Application_Model_ExprCourseGradeAftrPenaltyItems();

        $id = $this->_getParam("id");

        $type = $this->_getParam("type");

        switch ($type) {

            case "add":

                $this->view->type = $type;

                $this->view->form = $ExprCourseGradeAftrPenalties_form;

                if ($this->getRequest()->isPost()) {

                    if ($ExprCourseGradeAftrPenalties_form->isValid($this->getRequest()->getPost())) {

                     $data = $ExprCourseGradeAftrPenalties_form->getValues();

                    $last_insert_id = $ExprCourseGradeAftrPenalties_model->insert($data);
					
					$ExperientialLearning_model= new Application_Model_ExperientialLearning();
					$course_result= $ExperientialLearning_model->getExpCourseRecords($data['academic_id'],$data['year_id']);
					$course = $_POST['course'];
					//echo'<pre>';print_r($course);die;
					for($i=0;$i<count($course_result);$i++) {   
							for($k=0;$k<count($course['student_id_'.$course_result[$i]['elc_id']]);$k++){
								//echo'<pre>';print_r($_POST['course']['student_id_'.$course_result[$i]['elc_id']]);die;
									$item_data['id'] = $last_insert_id;
									$item_data['courses_id'] = $course_result[$i]['elc_id'];
									$item_data['student_id'] = 	$course['student_id_'.$course_result[$i]['elc_id']][$k];
									//echo'<pre>';print_r($item_data);die;
									$item_data['academic_credits'] = $course['academic_credits_'.$course_result[$i]['elc_id']][$k];
									$item_data['grade_aftr_penalty'] = $course['grade_aftr_penalty'.$course_result[$i]['elc_id']][$k];
									$item_data['grade_point_avg'] = $course['grade_point_avg'.$course_result[$i]['elc_id']][$k];
									//echo'<pre>';print_r($item_data);die;
									$ExprCourseGradeAftrPenaltyItems_model->insert($item_data);
								}
					}
                        $this->_flashMessenger->addMessage('Experiential Courses Penalties Added Successfully ');

                        $this->_redirect('experiential-course/gradeaftr-penalty');

                    } 
                }

                break;

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $ExprCourseGradeAftrPenalties_model->getRecords();
				//print_r($result); die;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	
	public function ajaxGetGradeAfterPenaltiesAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			
			$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			$course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);
			//print_r($course_result);die;
			$this->view->course_result = $course_result;
			
		}
	}
	

	public function ajaxCheckGradeaftrpenaltyDataAction(){

		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$ExprCourseGradeAftrPenalties_model = new Application_Model_ExprCourseGradeAftrPenalties();
			$penalty_result= $ExprCourseGradeAftrPenalties_model->getValidRecord($academic_year_id,$year_id);
			$counts = count($penalty_result['id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
			
		}
	}

	public function ajaxGetFirstYearTermsViewAction(){
	  $this->_helper->layout->disableLayout();
      if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
		$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			 $course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);	
			 $this->view->course_result = $course_result;
		}
	}
	public function ajaxGetSecondYearTermsViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
		$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			 $course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);	
			 $this->view->course_result = $course_result;
		}
	}
	
	public function ajaxGetExpcoursesGradesAfterPenaltiesViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
			$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			$course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);
			//print_r($course_result);die;
			$this->view->course_result = $course_result;
		}
	}
	public function ajaxGetExpsecondYearGradeAfterPenaltiesViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
			$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			$course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);
			//print_r($course_result);die;
			$this->view->course_result = $course_result;
		}
	}
	
	public function ajaxGetFirstYearExpcourseWiseViewAction(){
	  $this->_helper->layout->disableLayout();
      if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
		$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			 $course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);	
			 $this->view->course_result = $course_result;
		}
	}
	public function ajaxGetSecondYearExpcourseWiseViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
		$ExperientialLearning_model= new Application_Model_ExperientialLearning();
			 $course_result= $ExperientialLearning_model->getExpCourseRecords($academic_year_id,$year_id);	
			 $this->view->course_result = $course_result;
		}
	}
}