<?php 
class Application_Model_DmiHoliday extends Application_Model_GlobalDbModel
{
	
	
    protected $_name = 'ic_events';
    public  function __construct(){
        parent::__construct('main_web');
        }
    
    
    public function getHolidayList($holiday_category){
        
        $select = $this->_db->select()
				->from($this->_name)
				->where("category IN(?)",$holiday_category)
				->where("deleted IS NULL")
				->order('start');
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
                       return $result;
    }
    
    
    
      public function getHolidayListForStudents(){
        
        $select = $this->_db->select()
				->from($this->_name)
				->where("category IN(?)",array(38,39))
				->where("deleted IS NULL")
				->order('start');
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
                       return $result;
    }
    
    
    
    
    
}