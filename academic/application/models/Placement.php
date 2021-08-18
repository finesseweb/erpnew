<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Placement extends Zend_Db_Table_Abstract
{
    public $_name = 'placement_master';
    public $_selection = 'placement_selection_process';
    public $_country = 'apps_countries';
    public $_cities = 'cities';
    public $_states = 'states';

    
    protected $_id = 'id';
  
	//Get all records
      public function getRecord($id)
    {                                   
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id);          
                 
        $result=$this->getAdapter()
                      ->fetchRow($select);  
//print_r($result); die;            
        return $result;
    }

      public function getState($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_states)
                      ->where("$this->_states.country_id=?", $id);      
           // ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);  
//print_r($result); die;            
        return $result;
    }
        public function getCity($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_cities)
                      ->where("$this->_cities.state_id=?", $id)    ;      
           // ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchAll($select);  
//print_r($result); die;            
        return $result;
    }
  public function getMaxRegistration(){

       $select=$this->_db->select()
                      ->from($this->_name,array('max(registration_id)as max_id'));
        $result=$this->getAdapter()
                      ->fetchRow($select);  
/*echo $select; die;            
*/        return $result;

  }
 

	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->order("$this->_name.$this->_id DESC")                				   
					            ->where("$this->_name.status !=?",Inactive);
					 
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    public function getRecords_to_configuration_process()
    {       
        $select=$this->_db->select()
                      ->from($this->_name,array('organization_name'))  
                      ->where("$this->_name.status !=?", 'Inactive');
           
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }

  public function getRecords_selection_process()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                       ->joinLeft(array("country" => "apps_countries"), "country.id=$this->_name.country")
                        ->joinLeft(array("states" => "states"), "states.id=$this->_name.state")
                        ->joinLeft(array("city" => "cities"), "city.id=$this->_name.city",array('name as city_name'))
                      ->order("$this->_name.$this->_id DESC");                          
                   
           
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
    public function getDropDownList(){
        $select = $this->_db->select()
    ->from($this->_name, array('address'))       
        ->where("$this->_name.status!=?",Inactive)
                ->order('id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
  
        return $data;
    }
      public function getDropDownCityList(){
            $select = $this->_db->select()
        ->from($this->_cities, array('id','name','state_id'));       
            
          $result = $this->getAdapter()->fetchAll($select);
          $data = array();
          foreach ($result as $key => $value) {
            $data[$value['id']]=$value['name'];
          }
  
        return $data;
    }
     public function getDropDownStateList(){
            $select = $this->_db->select()
        ->from($this->_states, array('id','name','country_id'));       
            
            $result = $this->getAdapter()->fetchAll($select);
             $data = array();
             foreach ($result as $key => $value) {
               $data[$value['id']] = $value['name'];
             }
  
        return $data;
    }
    public function getDropDownCountryList(){
        $select = $this->_db->select()
    ->from($this->_country, array('id','country_name'));       
        
        $result = $this->getAdapter()->fetchAll($select);
        // $data = array();
  $data = array();
        foreach($result as $key => $value){
          $data[$value['id']] = $value['country_name'];
        }
        return $data;
    }
    public function getValidateRegistrationNo($id) {
        $select = $this->_db->select()
                ->from($this->_name,array("id")) 
        ->where("$this->_name.id =?", $id)
        ->where("$this->_name.status!=?", Inactive);
  $result = $this->getAdapter()
                ->fetchRow($select);
    return $result;
 } 


  public function selectionProcessGetRecords($id)
  {
    
    $select =$this->_db->select()
            ->from($this->_selection)
            ->where("$this->_selection._id=?",$id)   
            ->where("$this->_selection.status=?",Inactive);
    $result=$this->getAdapter()
            ->fetchRow($select);      
    return $result;

  }


 } 