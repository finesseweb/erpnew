<?php

class Application_Model_ExamBatch extends Zend_Db_Table_Abstract {

    public $_name = 'exambatch';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);
              
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
      public function getAllBatch(){
          
               $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.status != ?",2);
               
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
          
      }

    public function getRecords() {

        $select = $this->_db->select()
                ->from($this->_name)
              ->join(array("dept"=>"department"),"dept.id=$this->_name.department",array("department as department_name"));
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }
    
    public function getBatch($batch, $department){
           $select = $this->_db->select();
                $select->from($this->_name);
                if(!empty($batch)){
                   $select->where('batch = ?', $batch);
                }
                   $select->where('department = ?', $department);
           // echo $select; exit; 
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    	public function getDropDownList(){
        $select = $this->_db->select()
		->from($this->_name, array('id','batch',))				
				->where("status =?",0)
                ->order('batch  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[$val['id']] = $val['batch'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }
    
    
    
}
