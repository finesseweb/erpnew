<?php
class Finance_AccountBookController extends My_MasterController {

    public function init() {
        $this->globalFunction();
		  $this->_act = new Application_Model_Adminactions();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'account-book';
		$this->view->sub_title_name = 'account-book';
        $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$Reports_model = new Finance_Model_AccountBook();
		$Reports_form = new Finance_Form_AccountBook();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->Reports_form = $Reports_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->Reports_form = $Reports_form;
                if ($this->getRequest()->isPost()) {			
                    if ($Reports_form->isValid($this->getRequest()->getPost())) {						
                        $data = $Reports_form->getValues();
						//print_r($data); die;
						$print = $this->getRequest()->getPost("print");
					
						$Reports_form->populate($data);
						$account_statement = $Reports_model->getAccountStatement($data);
						$this->view->account_statement = $account_statement;
						if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('account-book/account-book-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Account Book");
					  }
                }
				}else{
					$this->_redirect('finance/account-book');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = date('Y').'-04-01';
				$this->view->enddate = $end_date = (date('Y')+1).'-03-31';
				$data = array("start_date" => $start_date, "end_date" => $end_date);
								
				$account_statement = $Reports_model->getAccountStatement($data);								
				$this->view->account_statement = $account_statement;
				$page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $account_statement
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
				
                break;
        }
    }	
}
