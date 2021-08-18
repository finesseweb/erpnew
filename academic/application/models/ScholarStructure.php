<?php

class Application_Model_ScholarStructure extends Zend_Db_Table_Abstract {

    public $_name = 'academic_master';
    protected $_id = 'academic_year_id';

    public function getDropDownList() {
        $select = $this->_db->select()
                ->from($this->_name, array('academic_year_id', 'from_date', 'to_date','short_code'))
                ->where("$this->_name.status!=?", 2)
                ->order('academic_year_id  ASC');
        $result = $this->getAdapter()->fetchAll($select);
        $data = array();
        $st_year = '';
        $end_year = '';
        foreach ($result as $val) {

            $data[$val['academic_year_id']] = $val['short_code'];

            // $data[$val['academic_id']] = substr($val['from_date']).'-'.substr($val['to_date']);
            //print_r($data);die;
        }
        return $data;
    }

    public function getDropDownListOfTerms(){
      
        
        $select = $this->_db->select()
              ->distinct()
                ->from(array('term'=>'term_master'),array('term_name','term_id'))
              ->where('term.status != ?', 2);
$result = $this->getAdapter()->fetchAll($select);   
//print_r($result);
         $data = array();
        $st_year = '';
        $end_year = '';
       // print_r(count($result));exit;
        for($i=0;$i<count($result); $i++){
            $data[$result[$i]['term_id']] = $result[$i]['term_name'] ;
        }
      //  print_r($data);exit;
        return $data;
    }


    public function getRecords() {
        $select = $this->_db->select()
                ->from('scholarship_management')
                ->joinleft(array("cat" => $this->_name), "cat.$this->_id=scholarship_management.batch_id")
                ->joinleft(array("term" => 'term_master'), "term.term_id=scholarship_management.term_id")
                ->where("cat.status !=?", 2)
                ->where('scholarship_management.status!=?', 2)
                ->order("cat.$this->_id DESC");
       
        $result = $this->getAdapter()
               
                ->fetchAll($select);
        return $result;
    }

   	public function getRecordById($id)
    {       
        $select=$this->_db->select()
                      ->from('scholarship_management')
                      ->where("scholarship_management.id=?", $id)				   
                        ->where("scholarship_management.status !=?", 2);
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
}
