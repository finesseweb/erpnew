<?php
class Application_Model_Coursewisepenalties extends Zend_Db_Table_Abstract
{

    protected $_name = 'course_wise_penalties';

    protected $_id = 'id';

    /**
     * Set Primary Key Id as a Parameter 
     * @return single dimention array
     */
	//get record (edit) 
    public function getRecord($id)
    {
        $select=$this->_db->select()
				->from($this->_name)
				->where("$this->_id=?",$id)
                                ->where("status != ?",2);
			$result=$this->getAdapter()
			->fetchRow($select);
        return $result;
    }
	
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array("short_code"))
                ->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }
	public function getAbsenceValues($academic_year_id,$term_id,$student_id){
		$select = $this->_db->select()
					->from($this->_name)
                   ->joinLeft(array("penalties_items"=>"course_wise_penalties_items"),"penalties_items.id=$this->_name.id",array("item_id","term_id","student_id","absence","academic_electives","academic_electives_ids","academic_courses"))
					->where("$this->_name.academic_id=?",$academic_year_id)
                    ->where("penalties_items.term_id=?",$term_id)
                   ->where("penalties_items.student_id=?",$student_id);

        $result = $this->getAdapter()
					->fetchRow($select);
			return $result;

   }
   
   public function getStudentAbsenceRecord($academic_year_id,$term_id)
    {
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_id =?", $academic_year_id)
                      ->where("$this->_name.status !=?", 2)
                      ->where("$this->_name.term_id =?", $term_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }

  

}

