<?php
class Application_Model_Grade extends Zend_Db_Table_Abstract
{

    protected $_name = 'grade_master';

    protected $_id = 'grade_id';

    /**
     * Set Primary Key Id as a Parameter 
     * @return single dimention array
     */
	//get record (edit) 
    public function getRecord($grade_id)
    {
        $select=$this->_db->select()
				->from($this->_name)
				->where("$this->_id=?",$grade_id);
			$result=$this->getAdapter()
			->fetchRow($select);
        return $result;
    }
	
	public function getRecords()
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
	//->joinleft(array("academic"=>"academic_master"),"academic.academic_year_id=$this->_name.academic_year_id",array("from_date","to_date"))
					  ->where("$this->_name.status !=?", 2)
					  ->order("$this->_name.$this->_id DESC");
        $result=$this->getAdapter()
                      ->fetchAll($select);       
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

