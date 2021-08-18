<?php
//ini_set('display_errors', '1');
class LibraryController extends Zend_Controller_Action {
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
	
    public function bookAction() {
        $this->view->action_name = 'book';
        $this->view->sub_title_name = 'book';
        $this->accessConfig->setAccess('SA_ACAD_BM',$storage->admin_login->role_id);
		$books = new Application_Model_Library();
		$bookhold = new Application_Model_LibraryHold();
		$insert_book_request = new Application_Model_UserHoldBook();
		$max_number = $insert_book_request->getMaxData();
		$result = $books->getBookList();
		$this->view->books = $books ;
		$this->view->bookhold = $bookhold ;
		$this->view->insert_book_request = $insert_book_request ;
		$this->view->result = $result;
		$this->view->max_number = $max_number;
        	
		if(isset($_SESSION['cart_item'])){
		   $book_count=count($_SESSION['cart_item']);
		}
		$this->view->book_count = $book_count;
               
		
        
    }
	
	public function holdbookAction() {
        $this->view->action_name = 'holdbook';
        $this->view->sub_title_name = 'holdbook';
         $this->accessConfig->setAccess('SA_ACAD_BH',$storage->admin_login->role_id);
        $holReq = new Application_Model_UserHoldBook();
        if(isset($_SESSION['admin_login']['admin_login']->id)){
            $user_id=$_SESSION['admin_login']['admin_login']->id;

        }else{
            $user_id=$_SESSION['admin_login']['admin_login']->user_id;
        }
		
        $result = $holReq->getHoldRequest($user_id);
        $max_number = $holReq->getMaxData();
        $this->view->result = $result;
		
        
    }
	
	public function extensionAction() {
		
        $this->view->action_name = 'extension';
        $this->view->sub_title_name = 'extension';
         $this->accessConfig->setAccess('SA_ACAD_BE',$storage->admin_login->role_id);
		$holReq = new Application_Model_UserHoldBook();
		
		if(isset($_SESSION['admin_login']['admin_login']->id)){
                    $user_id=$_SESSION['admin_login']['admin_login']->id;
		}else{
                    $user_id=$_SESSION['admin_login']['admin_login']->user_id;
		}
		$result = $holReq->getIssueBook($user_id);
		$books = new Application_Model_Library();
		$this->view->books = $books ;
		$this->view->result = $result;
		$this->view->holReq = $holReq;
		
		
        
    }
    public function issueBookAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$insert_book_request = new Application_Model_UserHoldBook();
		$max_number = $insert_book_request->getMaxData();
		if($_POST['isbn1']){
			if(isset($_SESSION['admin_login']['admin_login']->user_id)){
				$user_id=$_SESSION['admin_login']['admin_login']->user_id;
			}else{
				$user_id=$_SESSION['admin_login']['admin_login']->id;
			}
			
		   $last_emp1 = substr($max_number[0]['maxId'], 9);

		   $emp_inc_id = $last_emp1 + 1;
		    if (strlen($emp_inc_id) == 1) {
		        $bookIssueId = 'IssueId-00' . $emp_inc_id;
		    } else if (strlen($emp_inc_id) == 2) {
		        $bookIssueId = 'IssueId-0' . $emp_inc_id;
		    } else {
		        $bookIssueId = 'IssueId-' . $emp_inc_id;
		    }
			$insert_data = array(
				'user_id' => $user_id,
				'ISBN' => $_POST['isbn1'],
				'book_title' => $_POST['title1'],
				'copies_no' => $_POST['copies_no1'],
				'author' => $_POST['author1'],
				'publisher' => $_POST['publisher1'],
				'edition' => $_POST['edition1'],
				'issueReqId' => $bookIssueId,
				'direct_issue' => 0
			);
						
			$userIssueBookList=$insert_book_request->getUserBookList($user_id,$_POST['isbn1']);
			if($userIssueBookList==0){
				$BookIssue=$insert_book_request->insert($insert_data);
				echo "Issued Book  successfully";
			}else{
				echo "You already issue this book!! ";
			}
		}else{
			echo "error";
		}
		
    }
    
    public function cartAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
        if(!empty($_POST["isbn"])) {
                    $books = new Application_Model_Library();
                    $productByCode = $books->getbook($_POST["isbn"]);
                    $get_copies = $books->getBookCopyData($_POST["isbn"]);
			$itemArray = array($productByCode[0]["ISBN"]=>array('title'=>$productByCode[0]["title"], 'ISBN'=>$productByCode[0]["ISBN"], 'author'=>$_POST["author"], 'publisher'=>$productByCode[0]["publisher"], 'edition'=>$productByCode[0]["edition"],'copy_no'=>$get_copies[0]['copies_no']));                      		
			
			
			if(!empty($_SESSION["cart_item"])) {
				if(in_array($productByCode[0]["ISBN"],array_keys($_SESSION["cart_item"]))) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($productByCode[0]["ISBN"] == $k) {
								if(empty($_SESSION["cart_item"][$k]["title"])) {
									$_SESSION["cart_item"][$k]["title"] = 0;
								}
								if(empty($_SESSION["cart_item"][$k]["author"])) {
									$_SESSION["cart_item"][$k]["author"] = 0;
								}
								if(empty($_SESSION["cart_item"][$k]["publisher"])) {
									$_SESSION["cart_item"][$k]["edition"] = 0;
								}
								if(empty($_SESSION["cart_item"][$k]["edition"])) {
									$_SESSION["cart_item"][$k]["edition"] = 0;
								}
							
							}
					}
				} else {
					$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
				}
			} else {
				$_SESSION["cart_item"] = $itemArray;
			}
			echo count($_SESSION['cart_item']);
		}
    }
	public function getBookAction(){
		$this->view->action_name = 'get-book';
        $this->view->sub_title_name = 'get-book';
		if(isset($_SESSION['cart_item'])){
		   $book_count=count($_SESSION['cart_item']);
		}
		$this->view->book_count = $book_count;
		$insert_book_request = new Application_Model_UserHoldBook();
		$books = new Application_Model_Library();
		$Erp_tbl = new Application_Model_LibraryHold();
		$this->view->books = $books ;
		$this->view->userHold = $insert_book_request ;

		$isbnArr = $_POST['ISBN'];
		$titleArr = $_POST['title'];
		$authorArr = $_POST['author'];
		$publisherArr = $_POST['publisher'];
		$editionArr = $_POST['edition'];
		$copyArr = $_POST['copy_no'];
		$max_number = $insert_book_request->getMaxData();
		
	   $last_emp1 = substr($max_number[0]['maxId'], 9);

	   $emp_inc_id = $last_emp1 + 1;
	    if (strlen($emp_inc_id) == 1) {
	        $bookIssueId = 'IssueId-00' . $emp_inc_id;
	    } else if (strlen($emp_inc_id) == 2) {
	        $bookIssueId = 'IssueId-0' . $emp_inc_id;
	    } else {
	        $bookIssueId = 'IssueId-' . $emp_inc_id;
	    }
	  
		if($_POST['hold_date']){
			$direct_issue=1;
		}else{
			$direct_issue=0;
		}
		
		if(isset($_POST['submit_form']) || isset($_POST['hold_date'])){
			
			
			for($no = 0; $no < count($isbnArr); $no++){
				
				$isbn=$isbnArr[$no];
				$title=$titleArr[$no];
				$author=$authorArr[$no];
				$publisher=$publisherArr[$no];
				$edition=$editionArr[$no];
				
			    $copiesVal = $books->checkReq($copyArr[$no],$isbn);

			    if(empty($copiesVal)){
			    	$valCopyNumber=$books->getBookcopiesNumber($isbn);
			    	$get_copies =$valCopyNumber[0]['copies_no'];
			    	// if(empty($valCopyNumber)){
			    	// 	$this->view->error='<h4 style="text-align:center;color:red;font-size:bold">Book '.$isbn.' not available please go to main page</h4>';
			    	// 	break;
			    	// }else{
			    	// 	$this->view->error='';
			    	// }
			    	
			    }else{
			    	$get_copies = $copyArr[$no];
			    }

				if(isset($_SESSION['admin_login']['admin_login']->id)){
                    $user_id=$_SESSION['admin_login']['admin_login']->id;
				}else{
                     $user_id=$_SESSION['admin_login']['admin_login']->user_id;
				}

				if(isset($_SESSION['admin_login']['admin_login']->participant_username)){

				   $username=$_SESSION['admin_login']['admin_login']->participant_username;
				}else{
				   $username=$_SESSION['admin_login']['admin_login']->user_id;
				}

				if(isset($_SESSION['admin_login']['admin_login']->empl_id)){
						$_empl_id=$_SESSION['admin_login']['admin_login']->empl_id;
				}else{
					$_empl_id='';
				}
                      


       //          if($get_copies==""){
       //           	 $this->view->error='<h4 style="text-align:center">Book '.$isbn.' not available please go to main page</h4>';
       //           	 break;

       //          }else{
			    // 	$this->view->error='';
			    // }  

			    
				$insert_data = array(
					'user_id' => $user_id,
                    'empl_id' => $_empl_id,
                    'username' => $username,
					'ISBN' => $isbn,
					'book_title' => $title,
					'copies_no' => $get_copies,
					'author' => $author,
					'publisher' => $publisher,
					'edition' => $edition,
					'hold_date' => $_POST['hold_date'],
					'issueReqId' => $bookIssueId,
					'direct_issue' => $direct_issue
				);

				$data = array(
                    'req_copy' => 1
                );
                if($get_copies!=""){
					$Erp_tbl->update($data,array('copies_no=?' => $get_copies,'ISBN = ?' => $isbn)); 

					
					$BookIssue=$insert_book_request->insert($insert_data);
					
					if($BookIssue){
						unset($_SESSION["cart_item"][$isbn]);
					}
			    }


				
			}

			 
			//if($BookIssue){
				
				//unset($_SESSION["cart_item"]);
			//}
		}
	}
	

}
