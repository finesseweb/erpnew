<?php 

class Application_Model_StuTrans extends Application_Model_GlobalDbModel
{
    
  public $_name = 'fa_stu_trans';
  protected $_id = 'trans_no';
function __construct() {
    parent::__construct('erp');
    } 
    
    public function getStuTrans($id){
		if($id){
		$select = $this->_db->select()
					->from($this->_name)
					->where("$this->_name.stu_id=?",$id);
		$result = $this->getAdapter()
				->fetchAll($select);	

		return $result;
                }
                else
                    return false;
        
    }
    
    
   
    
}
