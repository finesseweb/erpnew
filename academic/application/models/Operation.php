<?php

class Application_Model_Operation extends Zend_Db_Table_Abstract {

    public $_name = 'seating_operation';
    protected $_id = 'id';

    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id=?", $id);

        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
  public function getExamDate() {
        $select = $this->_db->select()

                // ->from($this->_name,'booking_date');
                ->from($this->_name,array("DATE_FORMAT(booking_date, '%d/%m/%Y') as booking_date "));
        $result = $this->getAdapter()
                ->fetchAll($select);	
		$data = array();
        foreach($result as $key => $value){
			$data[$value['id']] = $value['booking_date'];
		}
		return $data;
    } 
 
    public function getTimeSlot($date) {

        $select = $this->_db->select()  
        
            ->from($this->_name)
            ->joinLeft(array("dur" => "duration"), "dur.id=$this->_name.duration")
                ->group("$this->_name.duration")
            // ->distinct()
            ->where("DATE_FORMAT($this->_name.booking_date,'%d/%m/%Y')", $date);         
        $result = $this->getAdapter()
                ->fetchAll($select); 
        return $result; 
    } 
    
    public function getRoomNo($date) {
        $select = $this->_db->select() 

            ->from($this->_name)
            ->joinLeft(array("dur" => "duration"), "dur.id=$this->_name.duration")
            ->joinLeft(array("room_namm" => "room"), "room_namm.id=$this->_name.room_no") 
             ->group("$this->_name.room_no")
            // ->distinct()
            ->where("$this->_name.duration=?", $date);         
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;

    }  

 
       public function getAllExamSchedule($date ='' ,$time='',$room = '') {

            $select = $this->_db->select();
               $select->from($this->_name);
                $select->joinLeft(array("dur" => "duration"), "dur.id=$this->_name.duration") ;
                $select->joinLeft(array("course_nam" => "course_master"), "course_nam.course_id=$this->_name.course_id") ;
                $select->joinLeft(array("room_namm" => "room"), "room_namm.id=$this->_name.room_no");
               if(!empty($room))
               $select->where("$this->_name.room_no=?", $room);  
                if(!empty($date))
               $select->where("DATE_FORMAT($this->_name.booking_date,'%d/%m/%Y')=?", $date);  
                if(!empty($time))
                $select->where("$this->_name.duration=?", $time);  
            $result = $this->getAdapter()
                           ->fetchAll($select);
         return $result;

    }
    
    public function prevRoom($booking,$duration,$department,$batch,$room_no,$course) {
        $select = $this->_db->select()
                ->from($this->_name, array('sum(allocated_seat) as total'))
                ->where("$this->_name.booking_date=?", $booking)
                ->where("$this->_name.duration=?", $duration)
                ->where("$this->_name.department=?", $department)
                ->where("$this->_name.course_id=?", $course)
                ->where("$this->_name.batch=?", $batch)
                ->where("$this->_name.room_no=?", $room_no);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result['total'];
    }

    public function prevDetails($booking,$duration,$department,$batch,$room_no,$course_id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.booking_date=?", $booking)
                ->where("$this->_name.duration=?", $duration)
                ->where("$this->_name.department=?", $department)
                ->where("$this->_name.batch=?", $batch)
                ->where("$this->_name.course_id=?", $course_id)
                ->where("$this->_name.room_no=?", $room_no);
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }
    
    
    public function getRecords() {

        $select = $this->_db->select()
                ->from($this->_name)
               // ->join(array("academic" => "application"), "academic.application_id=$this->_name.registration_no", array("stu_id", "stu_name", 'dob'))
                ->join(array("dur" => "duration"), "dur.id=$this->_name.duration", array("concat(duration_from,' - ',duration_to) as duration"))
                ->join(array("dept" => "department"), "dept.id=$this->_name.department", array("department as department_name"))
                ->join(array("room" => "room"), "room.id=$this->_name.room_no", array("room_number as room","seating_capacity"))
                ->join(array("btch" => "exambatch"), "btch.id=$this->_name.batch", array("batch as batch_name"));
        // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
    //    echo "<pre>";print_r($result);exit;

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
