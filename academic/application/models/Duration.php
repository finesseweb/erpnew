<?php

class Application_Model_Duration extends Zend_Db_Table_Abstract {

    public $_name = 'duration';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);

        $result = $this->getAdapter()
                ->fetchRow($select);
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
                ->where('status=?', 0);

        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getDuration($from, $to) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.duration_from=?", $from)
                ->where("$this->_name.duration_to=?", $to);

        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
      public function getDurations()
      {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.duration_from");
               

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
       public function getDurationsto()
      {
        $select = $this->_db->select()
                ->from($this->_name) 
                ->where("$this->_name.duration_to"); 
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
    
        	public function getDropDownList(){
        $select = $this->_db->select()
		->from($this->_name, array('id','duration_from','duration_to'))				
				->where("status =?",0)
                ->order('duration_from  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
		$st_year ='';
		$end_year='';
        foreach ($result as $val) {
			
			$data[$val['id']] = $val['duration_from'].' - '.$val['duration_to'];
			
           // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
			//print_r($data);die;
        }
        return $data;
    }

}
