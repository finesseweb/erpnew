<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GradeSheet
 *
 * @author w10
 */
class Application_Model_GradeSheet extends Zend_Db_Table_Abstract {
    public $_name = 'grade_sheet';
    protected $_id = 'id';
    public function getNewGradeSheetNumber($batch_id, $year_id) {
        $select = $this->_db->select()
                ->from($this->_name,array('count(*) as total_count'))                
                ->where("$this->_name.batch_id =?", $batch_id)
                ->where("$this->_name.year_id =?", $year_id)
                ->where("$this->_name.deleted !=?", 1);
        $result = $this->getAdapter()
                ->fetchRow($select);
        $new_id = $result['total_count'] + 1;
        return $new_id;
    }
    public function getGradeSheetNumber($batch_id, $year_id, $student_dmi_id) {
        $select = $this->_db->select()
                ->from($this->_name,array('sr_no'))                
                ->where("$this->_name.batch_id =?", $batch_id)
                ->where("$this->_name.year_id =?", $year_id)
                ->where("$this->_name.student_id =?", $student_dmi_id)
                ->where("$this->_name.deleted !=?", 1);
        $result = $this->getAdapter()
                ->fetchRow($select);
        
        if(is_array($result) && !empty($result)){
            return $result['sr_no'];
        }
        else{
            $storage = new Zend_Session_Namespace("admin_login");           
            $new_id = $this->getNewGradeSheetNumber($batch_id, $year_id);    
            $data = array(
                'batch_id' => $batch_id,
                'year_id' => $year_id,
                'sr_no' => $new_id,
                'student_id' => $student_dmi_id,
                'added_date' => date('Y-m-d H:i:s'),
                'added_by' => $storage->admin_login->id
            );
            $this->insert($data);
            return $new_id;
        }
    }
    
    
      public function getGradeSheetNumber1($batch_id, $year_id, $student_dmi_id) {
         
        $select = $this->_db->select()
                ->from($this->_name,array('sr_no'))                
                ->where("$this->_name.batch_id =?", $batch_id)
                ->where("$this->_name.year_id =?", $year_id)
                ->where("$this->_name.student_id =?", $student_dmi_id)
                ->where("$this->_name.deleted !=?", 1);
       // echo $select; echo "<br />";
        $result = $this->getAdapter()
                ->fetchRow($select);
      if(is_array($result))
          return $result['sr_no'];
      else 
          return 0;
    }
    
    
}
