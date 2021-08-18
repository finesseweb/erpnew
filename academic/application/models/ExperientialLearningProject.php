<?php
/**
 * 
 */
class Application_Model_ExperientialLearningProject extends Zend_Db_Table_Abstract
{
    public $_name = 'experiential_learning_projects';
    protected $_id = 'el_project_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.deleted !=?", 1);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
    
    
  
	
	//Get all records
	public function getRecords()
    {       
   
        $empl_id = $_SESSION['admin_login']['admin_login']->empl_id;
        if($empl_id){
                 $result1 =  $this->getAll();
        $select=$this->_db->select()
                      ->from($this->_name)  
                        ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.batch_id",array("from_date","to_date","short_code"))
                         ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=  $this->_name.el_component_id",array("elc_name"))
                            ->where("$this->_name.year_id IN(?)", $result1)
                        ->where("$this->_name.deleted !=?", 1)
                        ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);
        }
 else {
       $select=$this->_db->select()
                      ->from($this->_name)  
                        ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.batch_id",array("from_date","to_date","short_code"))
                         ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=  $this->_name.el_component_id",array("elc_name"))
                        ->where("$this->_name.deleted !=?", 1)
                        ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);
 }
        
       // echo "<pre>"; print_r($result);exit;
        return $result;
    }
    
    
    public function getAll()
    {
          $select=$this->_db->select()
                      ->from('experiential_learning_allotment_items',array('year_id'))
                      ->where("employee_id=?", $_SESSION['admin_login']['admin_login']->empl_id);
        $result=$this->getAdapter()
                      ->fetchAll($select);  
        foreach($result as $key =>$value){
         $myarr[$key] = $value['year_id'];
        }
        return array_unique($myarr);
         
    }
    
    
    public function getExpProjectRecords($batch_id, $year_id, $el_component_id){
         $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.batch_id=?", $batch_id)	
                      ->where("$this->_name.year_id=?", $year_id)
                      ->where("$this->_name.el_component_id=?", $el_component_id)
                      ->where("$this->_name.deleted !=?", 1)
                      ->order("$this->_name.updated_date DESC");;
        $result=$this->getAdapter()
                      ->fetchAll($select);   
        return $result;
    }

}
?>