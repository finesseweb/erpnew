<?php

class Application_Model_StudentFeed extends Zend_Db_Table_Abstract {

    public $_name = 'course_feed';
    protected $_id = 'id';

    public function hasRated($student_id, $course_id, $term, $batch) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_id=?', $student_id)
                ->where('term=?', $term)
                ->where('batch=?', $batch)
                ->where('course =? ', $course_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }
    
    
    
     public function hasRatedQuestion($student_id, $course_id,$instructor, $term, $batch) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_id=?', $student_id)
                ->where('term=?', $term)
                ->where('batch=?', $batch)
                ->where('course =? ', $course_id)
                ->where('instructor =?',$instructor )
                ->where('feed Is NULL');
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }
    
    
    
    
    public function isNotRequired($ques_no) {

        $select = $this->_db->select()
                ->from('question_master',array('rating_required'))
                ->where('Auto_no=?', $ques_no)
                ->where('status =?',1 );
       // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['rating_required'];
    }
    
    
    

    public function ratedValue($student_id, $course_id, $term, $batch) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_id=?', $student_id)
                ->where('term=?', $term)
                ->where('batch=?', $batch)
                ->where('course =? ', $course_id);
      
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRatingCount($term_id, $batch_id, $course, $rating_val) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where('term=?', $term_id)
                ->where('batch=?', $batch_id)
                ->where('feed=?', $rating_val)
                ->where('course =? ', $course);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }
    
    public function getRatingLabel($rating_val){
        $select = $this->_db->select()
                ->from('rating_master', array('text_filed'))
                ->where('rating_value=?', $rating_val);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['text_filed'];
    }
    
        
         public function courseFeed($term, $batch,$student_id) {

        $select = $this->_db->select()
                ->from($this->_name,array('distinct (course) as course_id'))
                ->where('term=?', $term)
                ->where('student_id=?', $student_id)
                ->where('batch=?', $batch);
       // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

}
