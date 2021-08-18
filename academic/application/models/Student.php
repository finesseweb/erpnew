<?php

class Application_Model_Student extends Zend_Db_Table_Abstract {

    public $_name = 'student';
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
                ->from($this->_name)
                ->join(array("academic"=>"application"),"academic.application_id=$this->_name.registration_no",array("stu_id","stu_name",'dob'))
                ->join(array("dept"=>"department"),"dept.id=$this->_name.department",array("department as department_name"))
                ->join(array("btch"=>"exambatch"),"btch.id=$this->_name.batch",array("batch as batch_name"));
       // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);

        return $result;
    }

    public function getStudent($student_id, $department, $batch) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("registration_no=?", $student_id)
                ->where("department=?", $department)
                ->where("batch=?", $batch);

        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
     public function getDropDownList1($dept,$course_id,$date,$batch,$duration){
        $select = $this->_db->select()
		->from(array("stu"=>$this->_name),array('id as exam_id'))
                ->join(array("app"=>"application"),"app.application_id = stu.registration_no",array('application_id','stu_id','stu_name','course_id','course_id_b'))
                ->where('stu.department =?', $dept)
                ->where("stu.batch=?", $batch)
                ->where('stu.alocated=?',0)
                ->where("stu.date !=?",$date)
                ->where('stu.status=?',0)
               ->where('app.course_id like ?',"%,{$course_id},%")
               ->orWhere('app.course_id_b like ?',"%,{$course_id},%")
               
                        ->group('app.stu_id')
               ->order('application_id  ASC');
       //   echo $select; exit;
        $result = $this->getAdapter()->fetchAll($select);
        $result = $this->updateCount($result,$course_id,$date,$batch,$duration);
        return $result;
    } 
    
     public function getBtweenMinAndMaxId($min,$max,$dept){
        $select = $this->_db->select()
                ->from($this->_name)
                 ->join(array("app"=>"application"),"app.application_id =$this->_name.registration_no")
                ->where("app.application_id >= ?",$min)
                ->where("$this->_name.department = ?",$dept)
                ->where("app.application_id <= ?",$max);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
        public function getStudentCount($department, $batch, $course_id,$date,$duration) {
            
        $select = $this->_db->select()
                ->from(array('stu'=>$this->_name))
                ->join(array("app"=>"application"),"app.application_id = stu.registration_no")
              //  ->where('opp.course_id != ?', $course_id)
                ->where("stu.department=?", $department)
                ->where("stu.batch=?", $batch)
                ->where("stu.alocated=?",0)
                ->where("stu.date !=?",$date)
                 ->where("stu.status=?",0)
                ->where("app.course_id like ?","%,{$course_id},%")
                ->orWhere('app.course_id_b like ?',"%,{$course_id},%")
                
              ->group("app.stu_id");
              //  echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        
       $result = $this->updateCount($result,$course_id,$date,'',$duration);
        return count($result);
    }
    
    
    public function updateCount($result,$course_id,$date,$batch ='',$duration){
        foreach($result as $key => $value){
             
              $select = $this->_db->select();
              $select->from('seating_operation');
             $select ->where("course_id = ?",$course_id);
             if(!empty($batch))
              $select->where("batch = ?",$batch);
             // ->where("duration = ?",$duration)
              $select->where("booking_date like ? ",date('Y-m-d',strtotime($date))."%");
              $select->where("selected_roll like ?","%{$value['stu_id']}%");
            
        $resultnew = $this->getAdapter()
                ->fetchAll($select);
 
                if(count($resultnew)>0){
                    unset($result[$key]);
                }
            
        }
        return $result;
    }
    
    

}
