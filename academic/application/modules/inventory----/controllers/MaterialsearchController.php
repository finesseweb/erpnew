<?php
class Inventory_MaterialsearchController extends My_MasterController {

    public function init() {
        $this->globalFunction();
    }

    public function indexAction() {	
        $this->view->action_name = 'index';
        $this->view->sub_title_name = 'inventory_estimation'; //die
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
	
	 public function returnableMaterialAction() {	
        $this->view->action_name = 'returnable-material';
        $this->view->sub_title_name = 'inventory_returnable-material'; //die
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
	
	
	 public function scarpSearchAction() {	
        $this->view->action_name = 'scarp-search';
        $this->view->sub_title_name = 'inventory_scarp-search'; //die
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
	
	 public function abcMaterialAction() {	
        $this->view->action_name = 'abc-Material';
        $this->view->sub_title_name = 'inventory_abc-Material'; //die
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
	
	 public function transferDeviationAction() {	
        $this->view->action_name = 'transfer-deviation';
        $this->view->sub_title_name = 'inventory_transfer-deviation'; //die
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
	
	public function consumptionSearchAction() {	
        $this->view->action_name = 'consumption-search';
        $this->view->sub_title_name = 'inventory_consumption-search'; //die
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