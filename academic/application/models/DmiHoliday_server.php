<?php 
class Application_Model_DmiHoliday extends Zend_Db_Table_Abstract
{
	
	
    protected $_name = 'ic_events';
    public  function __construct(){
       // echo "hello";exit;
            $adaptor = new Zend_Db_Adapter_Pdo_Mysql(array(
				'host'  => 'localhost',
             'username' => 'dmi_web_usr',
             'password' => '#;y]lkQw[{oF',
             'dbname'   => 'dmi_mainweb'
        ));
        $this->_db = $adaptor;
        parent::__construct();
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
}