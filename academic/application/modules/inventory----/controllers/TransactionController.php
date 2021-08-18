<?php
class Inventory_TransactionController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {	
        $this->view->action_name = 'index';
        $this->view->sub_title_name = 'tender_estimation1'; //die
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
	
	 public function sendMaterialAction() {	
        $this->view->action_name = 'send-material';
        $this->view->sub_title_name = 'inventory_send-material'; //die
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
	
	
	public function materialReceivedAction() {	
        $this->view->action_name = 'material-received';
        $this->view->sub_title_name = 'inventory_material-received'; //die
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
	
	
	public function supplierReturnAction() {	
        $this->view->action_name = 'supplier-return';
        $this->view->sub_title_name = 'inventory_supplier-return'; //die
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
	
	public function materialtransferSentAction() {	
        $this->view->action_name = 'materialtransfer-sent';
        $this->view->sub_title_name = 'inventory_materialtransfer-sent'; //die
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
	
	public function materialtransferReceivedAction() {	
        $this->view->action_name = 'materialtransfer-received';
        $this->view->sub_title_name = 'inventory_materialtransfer-received'; //die
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
	
	public function scarpSalesAction() {	
        $this->view->action_name = 'scarp-sales';
        $this->view->sub_title_name = 'inventory_scarp-sales'; //die
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