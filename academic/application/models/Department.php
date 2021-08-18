<?php

class Application_Model_Department extends Zend_Db_Table_Abstract {

    public $_name = 'department';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id =?", $id);
               // ->where("status=?",0);
        $result = $this->getAdapter()
                ->fetchRow($select);
        $result['batch_id'] = $this->academic($id);
        return $result;
    }
    
    public function academic($id){
            
     $select = $this->_db->select()
                ->from('academic_master')
                ->where("department like ?", "%$id%")
               ->where("status=?",0);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    public function getDepartment($department)
    {
       
          $select = $this->_db->select()
                ->from($this->_name)
                ->where('department = ?',$department);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRecords() {

        $select = $this->_db->select()
                ->from($this->_name);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
        public function getActiveRecords() {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('status=?',0);
            
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    	public function getDropDownList(){
        $select = $this->_db->select()
		->from($this->_name, array('id','department',))				
				->where("status =?",0)
                ->order('department  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[$val['id']] = $val['department'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
    
    
    
    
}
