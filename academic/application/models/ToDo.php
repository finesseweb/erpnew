<?php

class Application_Model_ToDo extends Zend_Db_Table_Abstract {

    public $_name = 'todo_list';
    protected $_id = 'toDo_id';

    public function getRecords($status) {
        if($_SESSION['admin_login']['admin_login']->empl_id){
 $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.toDo_status =?", $status)
                ->where("$this->_name.toDo_assigned_to_id =?", $_SESSION['admin_login']['admin_login']->empl_id)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
        ->fetchAll($select);
        return $result;
        }
 else {
      $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.toDo_status =?", $status)
                ->where("$this->_name.toDo_assigned_by =?", $_SESSION['admin_login']['admin_login']->id)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
        ->fetchAll($select);
        return $result;
 }
        
    }
    
     public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.$this->_id =?", $id)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
       // print_r($result);exit;
        return $result;
    }
    
    public function getAllEmployee($status){
     
        $select = $this->_db->select()
                ->from($this->_name,array('toDo_assigned_to_id'))
                ->where("$this->_name.toDo_assigned_by =?", $_SESSION['admin_login']['admin_login']->id)
                ->where("$this->_name.toDo_status=?", $status)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
       //print_r(count($result));exit;
        return $result;
    }
    
    public function gerRecordsByEverything(array $data, $empl_name = '',$category){
        
        $data_field = 'toDo_assigned_to_id';
        $id = $_SESSION['admin_login']['admin_login']->empl_id;
  
        if($data['btn']=='by_me'){
            $data_field = 'toDo_assigned_by';
            $id = $_SESSION['admin_login']['admin_login']->id;
        }
       
        if($data['todo_status']=='')
            return $this->filterOnBasisOfCategory($data, $empl_name,$category,$id,$data_field);
       
      if($empl_name!='')
         return $this->filterOnBasisOfEmplName($data, $empl_name,$category,$id,$data_field);
      
     if($data['from_date'] == '1970-01-01' && $data['to_date'] == '1970-01-01' ){
         
            $select = $this->_db->select()
                ->from($this->_name)
                    ->where('toDo_status =?', $data['todo_status'])
                     //->where('toDo_category = ?', $category)
                     ->where("$data_field =?",$id)
                ->order("$this->_name.$this->_id DESC");
          // echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
         
     
       return $result; 
     }
   
            $select = $this->_db->select()
                ->from($this->_name)
                //->where("$this->_name.status !=?", 2)
                 ->where('toDo_due_date >=?', $data['from_date'])
                    ->where('toDo_due_date <=?', $data['to_date'])
                    ->where('toDo_status =?', $data['todo_status'])
                     //->where('toDo_category = ?', $category)
                     ->where("$data_field =?",$id)
                ->order("$this->_name.$this->_id DESC");
          // echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
         
     
       return $result; 
    }
    
    
    
    
      public function filterOnBasisOfCategory(array $data , $empl_name='',$category,$id,$data_field){
    
          if($data['from_date'] == '1970-01-01' && $data['to_date'] == '1970-01-01'){
            
            $select = $this->_db->select()
                ->from($this->_name)
                     ->where('toDo_category = ?', $category)
                  
                     ->where("$data_field =?",$id)
                ->order("$this->_name.$this->_id DESC");
           
        $result = $this->getAdapter()
                ->fetchAll($select);
         
       return $result; 
     }
 
            $select = $this->_db->select()
                ->from($this->_name)
                //->where("$this->_name.status !=?", 2)
                 ->where('toDo_due_date >=?', $data['from_date'])
                    ->where('toDo_due_date <=?', $data['to_date'])
                
                    //->where('toDo_status =?', $data['todo_status'])
                     ->where('toDo_category = ?', $category)
                     ->where("$data_field =?",$id)
                ->order("$this->_name.$this->_id DESC");
          // echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
         
     
       return $result; 
    }
    
    
    
    public function filterOnBasisOfEmplName(array $data , $empl_name='',$category,$id,$data_field){
     
          if($data['from_date'] == '1970-01-01' && $data['to_date'] == '1970-01-01'){
            
            $select = $this->_db->select()
                ->from($this->_name)
                    ->where('toDo_status =?', $data['todo_status'])
                     //->where('toDo_category = ?', $category)
                    ->where('toDo_assigned_to_id = ?', $empl_name)
                     ->where("$data_field =?",$id)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
         
       return $result; 
     }
 
            $select = $this->_db->select()
                ->from($this->_name)
                //->where("$this->_name.status !=?", 2)
                 ->where('toDo_due_date >=?', $data['from_date'])
                    ->where('toDo_due_date <=?', $data['to_date'])
                     ->where('toDo_assigned_to_id = ?', $empl_name)
                    ->where('toDo_status =?', $data['todo_status'])
                     //->where('toDo_category = ?', $category)
                     ->where("$data_field =?",$id)
                ->order("$this->_name.$this->_id DESC");
          // echo $select;die;
        $result = $this->getAdapter()
                ->fetchAll($select);
         
     
       return $result; 
    }
    
    
    
    
   public function getAllEmployeeByEmplId($status,$empl_id){
    
       $select = $this->_db->select()
                ->from($this->_name,array('toDo_assigned_to_id'))
                ->where("$this->_name.toDo_status=?", 1)
               ->where("$this->_name.toDo_assigned_to_id =?",$empl_id)
                ->order("$this->_name.$this->_id DESC");
      //echo $select ; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
       //print_r($result);exit;
        return $result;
       
   }
   
   
      public function getAllEmployeeById($status, $id){
          $select = $this->_db->select()
                ->from($this->_name,array('toDo_assigned_to_id'))
                ->where("$this->_name.toDo_status=?", $status)
                    ->where("$this->_name.toDo_assigned_by =?",$id)
                ->order("$this->_name.$this->_id DESC");
         // echo $select; exit;
        $result = $this->getAdapter()
                ->fetchAll($select);
       // print_r($result);exit;
        return $result;
       
   }
    
    
    public function getMyTask($emp_id,$from_date, $todate,$status,$empl_id,$category){
       $where ='';
     // print_r($todate);exit;
       if($from_date == '1970-01-01' && $todate == '1970-01-01' ){
                   $select = $this->_db->select()
                ->from($this->_name)
                //->where("$this->_name.status !=?", 2)
         
                    ->where('toDo_assigned_by =?', $emp_id)
                   // ->where('toDo_category = ?', $category)
                  //  ->where('toDo_assigned_to_id =?', $empl_id)
                   ->where('toDo_status = ?',$status)
                ->order("$this->_name.$this->_id DESC");
       //echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
       }
          $select = $this->_db->select()
                ->from($this->_name)
                //->where("$this->_name.status !=?", 2)
                 ->where('toDo_due_date >=?', $from_date)
                    ->where('toDo_due_date <=?', $todate)
                    ->where('toDo_assigned_by =?', $emp_id)
                   // ->where('toDo_category = ?', $category)
                  //  ->where('toDo_assigned_to_id =?', $empl_id)
                   ->where('toDo_status = ?',$status)
                ->order("$this->_name.$this->_id DESC");
       //echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        //print_r($result);exit;
        return $result;
    }
    
    public function getAll($emp_id,$from_date, $todate,$status,$empl_id, $category){
        
           if($from_date == '1970-01-01' && $todate == '1970-01-01' ){
                $select = $this->_db->select()
                ->from($this->_name)
                    ->where('toDo_assigned_by =?', $emp_id)
                // ->where('toDo_category = ?', $category)
                   ->where('toDo_status = ?',$status)
                ->order("$this->_name.$this->_id DESC");
       // echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
               
               return $result;
               
           }
        
        
        $select = $this->_db->select()
                ->from($this->_name)
                //->where("$this->_name.status !=?", 2)
                 ->where('toDo_due_date >=?', $from_date)
                    ->where('toDo_due_date <=?', $todate)
                    ->where('toDo_assigned_by =?', $emp_id)
               //  ->where('toDo_category = ?', $category)
                   ->where('toDo_status = ?',$status)
                ->order("$this->_name.$this->_id DESC");
       // echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    public function getTask($emp_id,$from_date, $todate, $status,$empl_id, $category){
        
             if($from_date == '1970-01-01' && $todate == '1970-01-01' ){
                  $select = $this->_db->select()
                ->from($this->_name)
                //->where("$this->_name.status !=?", 2)
                    ->where('toDo_assigned_to_id =?', $emp_id)
               //  ->where('toDo_category = ?', $category)
                ->where('toDo_status = ?',$status)
                ->order("$this->_name.$this->_id DESC");
        //echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
                
                
            }
        
              
        
        
        $select = $this->_db->select()
                ->from($this->_name)
                //->where("$this->_name.status !=?", 2)
                 ->where('toDo_due_date >=?', $from_date)
                    ->where('toDo_due_date <=?', $todate)
                    ->where('toDo_assigned_to_id =?', $emp_id)
                 //->where('toDo_category = ?', $category)
                ->where('toDo_status = ?',$status)
                ->order("$this->_name.$this->_id DESC");
        //echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    
    
    
    public function getTodoData($empl_id,$status){
         $select = $this->_db->select()
                ->from($this->_name)
                 ->where("$this->_name.toDo_assigned_to_id=?",$empl_id)
                ->where("$this->_name.toDo_status=?", $status)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
       // print_r($result);exit;
        return count($result);
    }
    
    
     public function getTodoDataByStatus($status){
         $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.toDo_status=?", $status)
                ->order("$this->_name.$this->_id DESC");
        $result = $this->getAdapter()
                ->fetchAll($select);
       // print_r($result);exit;
        return count($result);
    }
    

}
