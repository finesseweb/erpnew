<?php
/**
 * 
 */
class Application_Model_ExperientialLearningProjectAllocation extends Zend_Db_Table_Abstract
{
    public $_name = 'experiential_learning_project_allocation';
    protected $_id = 'id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.batch_id",array("from_date","to_date","short_code"))
                        ->joinleft(array("elp"=>"experiential_learning_projects"),"elp.el_project_id=  $this->_name.el_project_id",array("project_name","project_location","sector","year_id","hosting_org","el_component_id"))
                         ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=  elp.el_component_id",array("elc_name"))
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
           //  echo $empl_id; exit;
        if($empl_id){
                $result1 =  $this->getAll();
                 if(count($result1)==0)
                     return 'MESSAGE';
        $select=$this->_db->select()
                      ->from($this->_name)  
                        ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.batch_id",array("from_date","to_date","short_code"))
                        ->joinleft(array("elp"=>"experiential_learning_projects"),"elp.el_project_id=  $this->_name.el_project_id",array("project_name","project_location","sector","year_id","hosting_org"))
                         ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=  elp.el_component_id",array("elc_name"))
                        ->where("$this->_name.deleted !=?", 1)
                          ->where("elp.year_id IN(?)", $result1)
                        ->order("$this->_name.updated_date DESC");  
        $result=$this->getAdapter()
                      ->fetchAll($select); 
        }
        else
        {
           $select=$this->_db->select()
                      ->from($this->_name)  
                        ->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.batch_id",array("from_date","to_date","short_code"))
                        ->joinleft(array("elp"=>"experiential_learning_projects"),"elp.el_project_id=  $this->_name.el_project_id",array("project_name","project_location","sector","year_id","hosting_org"))
                         ->joinleft(array("elcm"=>"experiential_learning_components_master"),"elcm.elc_id=  elp.el_component_id",array("elc_name"))
                        ->where("$this->_name.deleted !=?", 1)
                        ->order("$this->_name.updated_date DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);  
        }
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
    
    public function getELAllocatedStudents($batch_id, $year_id, $el_component_id) {
        $select=$this->_db->select()
                      ->from($this->_name,array('student_ids'))
                         ->joinInner(array("elp"=>"experiential_learning_projects"),"elp.el_project_id=  $this->_name.el_project_id")                         
                         ->where("elp.el_component_id =?", $el_component_id)
                         ->where("elp.batch_id =?", $batch_id)
                         ->where("elp.deleted !=?", 1)
                         ->where("$this->_name.deleted !=?", 1)
                         ->order("$this->_name.updated_date DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }

}
?>