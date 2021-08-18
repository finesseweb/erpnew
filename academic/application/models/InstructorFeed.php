<?php

class Application_Model_InstructorFeed extends Zend_Db_Table_Abstract {

    public $_name = 'instructor_feed';
    protected $_id = 'id';

    public function hasRated($student_id, $course_id , $insturctor_id, $term, $batch) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_id=?', $student_id)
                ->where('term=?', $term)
                ->where('batch=?', $batch)
                ->where('course=?', $course_id)
                ->where('instructor =? ', $insturctor_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }

    public function ratedValue($student_id, $insturctor_id, $term, $batch,$course_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_id=?', $student_id)
                ->where('term=?', $term)
                ->where('batch=?', $batch)
                ->where('course =?',$course_id )
                ->where('instructor =? ', $insturctor_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getRatingCount($term_id, $batch_id, $instructor,$course_id, $rating_val) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where('term=?', $term_id)
                ->where('batch=?', $batch_id)
                ->where('feed=?', $rating_val)
                ->where('course=?',$course_id)
                ->where('instructor =? ', $instructor);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }
    
     public function hasRatedQuestion($student_id, $course_id, $term, $batch) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_id=?', $student_id)
                ->where('term=?', $term)
                ->where('batch=?', $batch)
                ->where('course =? ', $course_id)
                ->where('feed Is NULL');
        
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }
     public function hasRatedFaculty($student_id, $course_id, $term, $batch,$faculty_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('student_id=?', $student_id)
                ->where('term=?', $term)
                ->where('batch=?', $batch)
                ->where('course =? ', $course_id)
                ->where('instructor =? ', $faculty_id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }


}
