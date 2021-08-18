<?php
//ini_set('display_errors', '1');
class EventController extends Zend_Controller_Action {
    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
 private $accessConfig =NULL;
    public function init() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
         require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();
        $config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;
        $this->_action = $this->getRequest()->getActionName();
        if ($this->_action == "login" || $this->_action == "forgot-password") {
            $this->_helper->layout->setLayout("adminlogin");
        } else {

            $this->_helper->layout->setLayout("layout");
        }
		$storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        $this->view->login_storage = $data;
        if (isset($data)) {
            $this->view->role_id = $data->role_id;
            $this->view->login_empl_id = $data->empl_id;
        }
        $this->_act = new Application_Model_Adminactions();
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();
        $this->view->authontication = $this->_authontication;
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;


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
	
    public function addeventAction() {
    	//$this->_helper->layout->disableLayout();
        //echo SA_ACAD_ADDEVENT; exit;
        $this->view->action_name = 'addEvent';
        $this->view->sub_title_name = 'addEvent';
        $this->accessConfig->setAccess('SA_ACAD_ADDEVENT',$storage->admin_login->role_id);
        $FetcheventCar = new Application_Model_Event();
		$category = $FetcheventCar->category();
		$eventList = $FetcheventCar->events();
		$eventAllList = $FetcheventCar->eventsList();
		
		foreach($eventAllList as $val){
			$eventColor->view->backgroundColor=$val['backgroundColor'];
		}

		$this->view->result = $category;
		$this->view->eventList = $eventcolor;
		$this->view->color = json_encode($eventcolor);
		$this->view->event= json_encode($eventList);


		$Event = new Application_Model_EventAdd();

		if(isset($_POST['ic_event_title'])){

			$catList = $FetcheventCar->categorybyId($_POST['marker_category']);
			if($_POST['only_faculty']){
				$val=$_POST['only_faculty'];
			}else{
				$val=0;
			}
			$insert_data = array(
				'category' => $_POST['marker_category'],
				'title' => $_POST['ic_event_title'],
				'description' => $_POST['ic_event_desc'],
				'start' =>$_POST['ic_event_starttime'],
				'end' =>$_POST['ic_event_endtime'],
				'backgroundColor' =>$catList[0]['backgroundColor'],
				'borderColor' =>$catList[0]['borderColor'],
				'textColor' =>$catList[0]['textColor'],
				'only_faculty' =>$val,
			);

			
				$eventadd=$Event->insert($insert_data);
				$this->_redirect('event/addevent');
	    }

	    if(isset($_POST['delButton'])){

	    	
	    	$insert_data = array(
				'deleted' => 1,
			);
	    	$Event->update($insert_data,array('eid=?' => $_POST['id'])); 
			$this->_redirect('event/addevent');

	    }

	    if(isset($_POST['ic_event_title1'])){	

	    	$catList = $FetcheventCar->categorybyId($_POST['marker_category1']);
			$insert_data = array(
				'category' => $_POST['marker_category1'],
				'title' => $_POST['ic_event_title1'],
				'description' => $_POST['ic_event_desc1'],
				'start' =>$_POST['ic_event_starttime1'],
				'end' =>$_POST['ic_event_endtime1'],
				'backgroundColor' =>$catList[0]['backgroundColor'],
				'borderColor' =>$catList[0]['borderColor'],
				'textColor' =>$catList[0]['textColor'],
				'only_faculty' =>$_POST['only_faculty'],

			);

			$Event->update($insert_data,array('eid=?' => $_POST['id'])); 
			$this->_redirect('event/addevent');
	    }

	}

	public function categoryAction() {
    	//$this->_helper->layout->disableLayout();
        $this->view->action_name = 'eventCategory';
        $this->view->sub_title_name = 'eventCategory';
        $this->accessConfig->setAccess('SA_ACAD_EVENTCATEGORY',$storage->admin_login->role_id);
       // $category_form = new Application_Form_CategoryPortal();
        $FetcheventCar = new Application_Model_Event();
        $student_id = $this->_getParam("id");
        $result = $FetcheventCar->category2();
        $page = $this->_getParam('page', 1);

        $paginator_data = array(
            'page' => $page,
            'result' => $result
        );
        $this->view->paginator = $this->_act->pagination($paginator_data);



        if(isset($_POST['name'])){	
			$insert_data = array(
				'category_name' => $_POST['name'],
				'category_desc' => $_POST['category_desc'],
				'backgroundColor' => $_POST['category_bgcolor'],
				'borderColor' =>$_POST['category_bcolor'],
				'textColor' =>$_POST['category_color'],
			);

			
				$eventadd=$FetcheventCar->insert($insert_data);
				$this->_redirect('event/category');
	    }
	    if(isset($_POST['name1'])){	
			$insert_data = array(
				'category_name' => $_POST['name1'],
				'category_desc' => $_POST['category_desc1'],
				'backgroundColor' => $_POST['category_bgcolor1'],
				'borderColor' =>$_POST['category_bcolor1'],
				'textColor' =>$_POST['category_color1'],
			);


			$FetcheventCar->update($insert_data,array('category_id=?' => $_POST['cat_id'])); 
			$this->_redirect('event/category');
	    }
	   if($_GET['delete'] !=''){
	   		$FetcheventCar->delete(array('category_id =?' => $_GET['delete']));
	   		$this->_redirect('event/category');
	   }
               
	}



    public function testAction() {
    	//$this->_helper->layout->disableLayout();
        $this->view->action_name = 'test';
        $this->view->sub_title_name = 'test';
        $this->accessConfig->setAccess('SA_ACAD_TEST',$storage->admin_login->role_id);
        $FetcheventCar = new Application_Model_Event();
		$category = $FetcheventCar->category();
		$eventList = $FetcheventCar->events();
		$eventAllList = $FetcheventCar->eventsList();
		
		foreach($eventAllList as $val){
			$eventColor->view->backgroundColor=$val['backgroundColor'];
		}

		$this->view->result = $category;
		$this->view->eventList = $eventcolor;
		$this->view->color = json_encode($eventcolor);
		$this->view->event= json_encode($eventList);


		$Event = new Application_Model_EventAdd();

		if(isset($_POST['ic_event_title'])){	
		$insert_data = array(
			'category' => $_POST['marker_category'],
			'title' => $_POST['ic_event_title'],
			'description' => $_POST['ic_event_desc'],
			'start' =>$_POST['ic_event_starttime'],
			'end' =>$_POST['ic_event_endtime'],
		);

		
			$eventadd=$Event->insert($insert_data);
			
			//$this->view->_redirect('https://localhost/finesse-erp/academic/event/test');
		}
		

	//	$add_event = new Application_Model_Event();
		//$result = $add_event->addEvent();
		//$this->view->add_event = $add_event 
		//$this->view->result = $result
    }
	
	public function get_categoryAction()	{ 	  
			
		$this->view->action_name = 'get_category';
        $this->view->sub_title_name = 'get_category';
        //$this->accessConfig->setAccess('SA_ACAD_EVENTCATEGORY');
		$FetcheventCar = new Application_Model_Event();
		$category = $FetcheventCar->category();
		echo json_encode($category); 
	}

	public function ajaxaddeventAction(){

		$this->_helper->layout->disableLayout();
		$this->view->action_name = 'ajaxaddevent';
        $this->view->sub_title_name = 'ajaxaddevent';
        $FetcheventCar = new Application_Model_EventAdd();
        $Eventcategory = new Application_Model_Event();


		// $insert_data = array(
		// 	'category' => $_POST['category'],
		// 	'title' => $_POST['title'],
		// 	'start' =>date('Y-m-d'),
		// 	'end' =>date('Y-m-d'),
		// );
			
		// $eventadd=$FetcheventCar->insert($insert_data);
	}

	public function ajaxgetAction(){

		$this->_helper->layout->disableLayout();
		$this->view->action_name = 'ajaxget';
        $this->view->sub_title_name = 'ajaxget';
	       $id=$_POST['id'];
	       $FetcheventCar = new Application_Model_Event();
	       $eventdata=$FetcheventCar->getEvent($id);
	       $val=$eventdata;
	       echo json_encode($val);

		
	}

	public function eventlistAction(){

		$this->view->action_name = 'eventList';
        $this->view->sub_title_name = 'eventList';
        
              $this->accessConfig->setAccess('SA_ACAD_EVENTLIST');
        $FetcheventCar = new Application_Model_Event();
        $eventList = $FetcheventCar->userEvent();
        $this->view->event= json_encode($eventList);
	}
	
}
