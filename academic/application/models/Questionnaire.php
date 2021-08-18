<?php

class Application_Model_Questionnaire extends Zend_Db_Table_Abstract {

    public $_name = 'question_master';
    protected $_id = 'id';
    public $_roll = 'Q';
    
    public function Auto_num() {
        $select = $this->_db->select()
                ->from($this->_name, array('max(id) as id'));
        $result = $this->getAdapter()
                ->fetchRow($select);
        if ($result['id']){ 
            return $this->setNum($result['id']+1);
        }
            return $this->setNum(1);
    }

    
    public function setNum($id) {
        $len = strlen((string) $id);
        if ($len == 1)
            return  $this->_roll. '00' . $id;
        else if ($len == 2)
            return $this->_roll. '0' . $id;
        return $this->_roll.$id;
    }
    
    
    public function getRecordById($id){
          $select = $this->_db->select()
                ->from($this->_name)
                  ->where('id = ?',$id);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
    public function getRecords(){
          $select = $this->_db->select()
                ->from($this->_name);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getAllQuestionByQuestionType($question_id){
         $select = $this->_db->select()
                ->from($this->_name)
                 ->where('status = ?',1)
         ->where('question_type = ?', $question_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getAllQuestionByEmpl($batch_id, $term_id,$course_id, $empl_id){
          $select = $this->_db->select()
                  ->distinct()
                ->from('instructor_feed', array("question_no_c as question"))
                 ->where('batch = ?',$batch_id)
                  ->where('course = ?', $course_id)
                  ->where('instructor = ?', $empl_id)
         ->where('term = ?', $term_id);
         // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
        
    }
    
    
        public function getAllQuestionByEmplFeed($batch_id, $term_id,$course_id, $empl_id,$feed,$question){
          
          $select = $this->_db->select()
                ->from('instructor_feed')
                 ->where('batch = ?',$batch_id)
                  ->where('course = ?', $course_id)
                  ->where('instructor = ?', $empl_id)
                  ->where('feed = ?', $feed)
                  ->where('question_no_c =?', $question)
         ->where('term = ?', $term_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
        
    }
    
    
    
     public function getAllQuestionName($question_no){
          $select = $this->_db->select()
                ->from($this->_name, array("text_filed as name"))
               ->where('status = ?',1)
         ->where('Auto_no = ?', $question_no);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['name'];
        
    }
    
    
      public function getAllQuestionByQuestionType1($question_id, $term_id, $batch_id){
         $select = $this->_db->select()
                ->from('feed_master')
                 ->where('term_id = ?',$term_id)
                 ->where('academic_year_id = ?',$batch_id)
                ->where('question_type = ?', $question_id);
        // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getAllQuestionByQuestionType2($question_id,$question_no){
          $select = $this->_db->select()
                ->from($this->_name,array('Auto_no','text_filed'))
                 ->where('status = ?',1)
                  ->where('Auto_no = ?',$question_no)
         ->where('question_type = ?', $question_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    

}

