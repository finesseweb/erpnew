<?php
class Inventory_ClientmaterialController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {	
        $this->view->action_name = 'index';
        $this->view->sub_title_name = 'client_material'; //die
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
																	
	 public function materialOutwardAction() {	
        $this->view->action_name = 'material-outward';
        $this->view->sub_title_name = 'inventory_material-outward'; //die
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
        $this->view->sub_title_name = 'inventory_physical-stock'; //die
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