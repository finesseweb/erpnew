<?php
class Application_Model_CourseGrade extends Zend_Db_Table_Abstract
{

    protected $_name = 'course_wise_grades';

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
				->where("$this->_id=?",$id);
			$result=$this->getAdapter()
			->fetchRow($select);
        return $result;
    }
	
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
					->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_id",array('short_code'))
                 ->joinleft(array("term"=>"term_master"),"term.term_id=$this->_name.term_id",array("term_name"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
    }

   
	public function getElectiveValues($academic_year_id,$term_id,$student_id){
		$select = $this->_db->select()
					->from($this->_name)
                    ->joinLeft(array("grade_items"=>"course_wise_grade_items"),"grade_items.id=$this->_name.id",array("term_id","student_id","academic_grades","academic_electives","elective_values","total_grade"))
					->where("$this->_name.academic_id=?",$academic_year_id)
                    ->where("grade_items.term_id=?",$term_id)
                    ->where("grade_items.student_id=?",$student_id);
//echo $select;die;
        $result = $this->getAdapter()
					->fetchRow($select);
			return $result;

   }
   
   
   
   public function getTermRecords($academic_year_id,$year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_id =?", $academic_year_id)
					  ->where("$this->_name.year_id =?", $year_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
	
	
	public function getValidSecondTermsRecord($academic_year_id,$year_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_id =?", $academic_year_id)
					  ->where("$this->_name.year_id =?", $year_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }

	
	  public function getValidTermsRecord($academic_year_id,$term_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.academic_id =?", $academic_year_id)
					  ->where("$this->_name.term_id =?", $term_id);	
        //echo $select;die;					  
        $result=$this->getAdapter()
                      ->fetchRow($select);    
    //print_r($result);die;					  
        return $result;
    }
   /**
     * Retrieve all Records
     *
     * @return Array
     */
 /*  public function findall($com_id){
        $select=$this->_db->select()
				->from($this->_name)
				->where("erp_com_id =?",$com_id);
			$result=$this->getAdapter()
			->fetchAll($select);
        return $result;
    }
	
	//Get tyre weight data's with compound
	public function getTyreWeightCompoundItems($com_id) {
        $select=$this->_db->select()
				->from($this->_name)
				->joinLeft(array("items"=>"erp_items_master"),"items.item_id=$this->_name.item_master_id", array("item_id", 'item_name'))
				//->joinLeft(array("com"=>"erp_compound_master"),"com.id=$this->_name.erp_com_id", array("compound_name", 'compound_quantity'))
				->where("erp_com_id =?",$com_id);			
			$result=$this->getAdapter()
			->fetchAll($select);
        return $result;
    } */


}

