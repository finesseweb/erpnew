<?php
class Inventory_PurchaseController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {	
        $this->view->action_name = 'index';
        $this->view->sub_title_name = 'inventory_purchase-estimation'; //die
        $type = $this->_getParam("type");
		//echo $type; die;
        $this->view->type = $type;		
        switch ($type) {
            case "add":               
                if ($this->getRequest()->isPost()) {					
					// add form here
                }
                break; 
			case "edit":               
                if ($this->getRequest()->isPost()) {					
                    //Defualt edit form
                }
                break; 
            default:
               //Defualt list here              
				break;
        }
    }
	
	 
	  public function purchaseInwardAction() {	
        $this->view->action_name = 'purchase-inward';
        $this->view->sub_title_name = 'inventory_purchase-inward'; //die
        $type = $this->_getParam("type");
		//echo $type; die;
        $this->view->type = $type;		
        switch ($type) {
            case "add":               
                if ($this->getRequest()->isPost()) {					
					// add form here
                }
                break; 
			case "edit":               
                if ($this->getRequest()->isPost()) {					
                    //Defualt edit form
                }
                break; 
            default:
               //Defualt list here              
				break;
        }
    }
	
	 public function inwardSearchAction() {	
        $this->view->action_name = 'inward-search';
        $this->view->sub_title_name = 'inventory_inward-search'; //die
        $type = $this->_getParam("type");
		//echo $type; die;
        $this->view->type = $type;		
        switch ($type) {
            case "add":               
                if ($this->getRequest()->isPost()) {					
					// add form here
                }
                break; 
			case "edit":               
                if ($this->getRequest()->isPost()) {					
                    //Defualt edit form
                }
                break; 
            default:
               //Defualt list here              
				break;
        }
    }
	
	 public function billSearchAction() {	
        $this->view->action_name = 'bill-search';
        $this->view->sub_title_name = 'inventory_bill-search'; //die
        $type = $this->_getParam("type");
		//echo $type; die;
        $this->view->type = $type;		
        switch ($type) {
            case "add":               
                if ($this->getRequest()->isPost()) {					
					// add form here
                }
                break; 
			case "edit":               
                if ($this->getRequest()->isPost()) {					
                    //Defualt edit form
                }
                break; 
            default:
               //Defualt list here              
				break;
        }
    }
	
	 public function indentApprovalAction() {	
        $this->view->action_name = 'indent-approval';
        $this->view->sub_title_name = 'inventory_indent-approval'; //die
        $type = $this->_getParam("type");
		//echo $type; die;
        $this->view->type = $type;		
        switch ($type) {
            case "add":               
                if ($this->getRequest()->isPost()) {					
					// add form here
                }
                break; 
			case "edit":               
                if ($this->getRequest()->isPost()) {					
                    //Defualt edit form
                }
                break; 
            default:
               //Defualt list here              
				break;
        }
    }
	
	public function creditDebitAction() {	
        $this->view->action_name = 'credit-debit';
        $this->view->sub_title_name = 'inventory_credit-debit'; //die
        $type = $this->_getParam("type");
		//echo $type; die;
        $this->view->type = $type;		
        switch ($type) {
            case "add":               
                if ($this->getRequest()->isPost()) {					
					// add form here
                }
                break; 
			case "edit":               
                if ($this->getRequest()->isPost()) {					
                    //Defualt edit form
                }
                break; 
            default:
               //Defualt list here              
				break;
        }
    }
	
	public function purchaseStatusAction() {	
        $this->view->action_name = 'purchase-status';
        $this->view->sub_title_name = 'inventory_purchase-status'; //die
        $type = $this->_getParam("type");
		//echo $type; die;
        $this->view->type = $type;		
        switch ($type) {
            case "add":               
                if ($this->getRequest()->isPost()) {					
					// add form here
                }
                break; 
			case "edit":               
                if ($this->getRequest()->isPost()) {					
                    //Defualt edit form
                }
                break; 
            default:
               //Defualt list here              
				break;
        }
    }
	
	public function purchasedMaterialsAction() {	
        $this->view->action_name = 'purchased-materials';
        $this->view->sub_title_name = 'inventory_purchased-materials'; //die
        $type = $this->_getParam("type");
		//echo $type; die;
        $this->view->type = $type;		
        switch ($type) {
            case "add":               
                if ($this->getRequest()->isPost()) {					
					// add form here
                }
                break; 
			case "edit":               
                if ($this->getRequest()->isPost()) {					
                    //Defualt edit form
                }
                break; 
            default:
               //Defualt list here              
				break;
        }
    }
	
}