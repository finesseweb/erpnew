<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_ExperientialLearningComponents extends Zend_Db_Table_Abstract
{
    public $_name = 'experiential_learning_components_master';
    protected $_id = 'elc_id';
  
    //get details by record for edit
	public function getRecord($id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.$this->_id=?", $id)				   
					  ->where("$this->_name.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);       
        return $result;
    }
	
	//Get all records
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name) 
						//->joinleft(array("academic"=>"course_type_master"),"academic.ct_id=$this->_name.ct_id",array("ct_name"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	
	public function getDropDownList(){
        $select = $this->_db->select()
     ->from($this->_name, array('elc_id','elc_name'))
				->where("$this->_name.status!=?",2)
                ->order('elc_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		
        foreach ($result as $val) {
			
			$data[$val['elc_id']] = $val['elc_name'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
	
	
	//same name validation
	
	
	public function getcomponenetname($componentname) {

        $select = $this->_db->select()
                ->from($this->_name,array("elc_name","elc_id"))	
				->where("$this->_name.elc_name =?", $componentname)
				->where("$this->_name.status!=?", 2);
		
        $result = $this->getAdapter()
                ->fetchRow($select);
		return $result;
		

    }
	
	
}
?>