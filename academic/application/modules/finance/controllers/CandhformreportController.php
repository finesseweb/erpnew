<?php


class Finance_CandhformreportController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {  
        $this->view->module = "finance";
        $this->view->action_name = 'index';
        $this->view->controller = 'candhformreport';
		$this->view->sub_title_name = 'candhformreport';       
		$Candhformreport_model = new Finance_Model_Candhformreport();		
		$Candhformreport_form = new Finance_Form_Candhformreport();	
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->Candhformreport_form = $Candhformreport_form;
		
        switch ($type) {
            case "search":                
                $this->view->type = $type;
                $this->view->Candhformreport_form = $Candhformreport_form;
                if ($this->getRequest()->isPost()) {			
                    if ($Candhformreport_form->isValid($this->getRequest()->getPost())) {						
                        $data = $Candhformreport_form->getValues();
						$print = $this->getRequest()->getPost("print");
						//print_r($print); die;
						
						$Candhformreport_form->populate($data);
						$this->view->middate = $mid_date = (date('Y')).'-09-30';
						$data['mid_date'] = $mid_date;			
						
						 $result = $Candhformreport_model->getDiscountPrice($data);
						 //print_r($result); die;
						$this->view->result = $result;
						
						/* $result1 = $Candhformreport_model->getDiscountPriceH($data);
						$this->view->result1 = $result1; */
						/*
						$getAccountStatementByDatesForPurchaseVat = $Candhformreport_model->getAccountStatementByDatesForPurchaseVat($data);
						$this->view->vat_account = $getAccountStatementByDatesForPurchaseVat;
						
						*/
						
						if(isset($print)){
							$pdfheader = $this->view->render('index/pdfheader.phtml');
							$pdffooter = $this->view->render('index/pdffooter.phtml');				
							$htmlcontent = $this->view->render('candhformreport/candhform-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "C & H Form Report");
						}
		
						
                }
				}else{
					$this->_redirect('finance/candhformreport');
				}
                break; 
            default:               
				$this->view->start_date = $start_date = date('Y').'-04-01';
				//$this->view->start_date = $start_date = date('Y').'-01-01';
				$this->view->enddate = $end_date = (date('Y')+1).'-03-31';
				$this->view->middate = $mid_date = (date('Y')).'-09-30';	
				$data = array("start_date" => $start_date, "end_date" => $end_date, "mid_date" => $mid_date);	

				$result = $Candhformreport_model->getDiscountPriceC($data);				
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
				 $this->view->paginator = $this->_act->pagination($paginator_data);
				
				$result1 = $Candhformreport_model->getDiscountPriceH($data);				
                $page = $this->_getParam('page', 1);
                $paginator_data1 = array(
                    'page' => $page,
                    'result' => $result1
                );
                $this->view->paginator1 = $this->_act->pagination($paginator_data1);
                break;
        }
    }	
}
