<?php
class Inventory_StockandledgerController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {	
        $this->view->action_name = 'index';
        $this->view->sub_title_name = 'stockandledger_index'; //die
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
	
	 public function ledgerProjectwiseAction() {	
        $this->view->action_name = 'ledger-projectwise';
        $this->view->sub_title_name = 'inventory_ledger-projectwise'; //die
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
	
	public function stockAction() {	
        $this->view->action_name = 'stock';
        $this->view->sub_title_name = 'inventory_stock'; //die
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
	
	public function overallStockAction() {	
        $this->view->action_name = 'overall-stock';
        $this->view->sub_title_name = 'inventory_overall-stock'; //die
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
	
	public function stockAnalysisAction() {	
        $this->view->action_name = 'stock-analysis';
        $this->view->sub_title_name = 'inventory_stock-analysis'; //die
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
	
	public function damagedStockAction() {	
        $this->view->action_name = 'damaged-stock';
        $this->view->sub_title_name = 'inventory_damaged-stock'; //die
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
	
	public function reorderLevelAction() {	
        $this->view->action_name = 'reorder-level';
        $this->view->sub_title_name = 'inventory_reorder-level'; //die
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
	
	public function physicalStockAction() {	
        $this->view->action_name = 'physical-stock';
        $this->view->sub_title_name = 'inventory_physical-stock1'; //die
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