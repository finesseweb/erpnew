<?php
class Finance_AssestsController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'assests';
		$this->view->sub_title_name = 'assests';
       // $ErpFinanceReceiptVoucher_model = new Finance_Model_ReceiptVoucher();
		$Assests_model = new Finance_Model_Assests();
		$Assests_form = new Finance_Form_Assests();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->assests = $Assests_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->assests = $Assests_form;
                if ($this->getRequest()->isPost()) {			
                    if ($Assests_form->isValid($this->getRequest()->getPost())) {						
                        $data = $Assests_form->getValues();
						$print = $this->getRequest()->getPost("print");
						//$TrailBalance_form->populate($data);
						$account_statement = $Assests_model->getAccountAssetsStatement($data);
						
						//echo '<pre>';print_r($cash_statement);die;	
						$this->view->account_statement = $account_statement;
						
					/*	if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('trading-account/trading-account-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Trading Account");
						} */
						
                }
				}else{
					$this->_redirect('finance/assests');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = date('Y').'-04-01';
				$this->view->enddate = $end_date = (date('Y')+1).'-03-31';
				$this->view->middate = $mid_date = (date('Y')).'-09-30';
				$data = array("start_date" => $start_date, "end_date" => $end_date , "mid_date" => $mid_date);
				$account_statement = $Assests_model->getAccountAssestsStatement($data);
				
				
				//echo '<pre>';print_r($dealer_statement);die;	
				$this->view->account_statement = $account_statement;	
					
				
                break;
        }
    }	
}
